<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Customer;
use App\Product;
use App\Personnel;
use App\OrderLine;
use DB;
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $AllOrders=Order::with(['details', 'deliveryPersonnel', 'clerk'])->orderBy('order_date')->paginate(10);

      
        return view('orders.index',[
            'orders'=>$AllOrders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $AllCustomers = Customer::all();
        $AllProducts = Product::all();
        $AllPersonnels = Personnel::where('personneltype', '=', "delivery")->get();

        return view('orders.addOrder',[
            'AllCustomers'=>$AllCustomers,'products'=>$AllProducts,'AllPersonnels'=>$AllPersonnels
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|in:cash,cod,credit',
            'status' => 'required|in:Pending,Processed,Delivered,Received',
            'order_date' => 'required|date',
            'served_by' => 'required|exists:users,id',
            'delivered_by' => 'required|exists:personnels,id',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required_with_all:details.*.unit_price,details.*.quantity',
            'details.*.unit_price' => 'required_with_all:details.*.product_id,details.*.quantity',
            'details.*.quantity' => 'required_with_all:details.*.product_id,details.*.unit_price'
           
        ]);



        // dd($input);

        DB::transaction(function () use ($input) {
            $order = Order::create(array_except($input, ['details']));
            $order->details()->createMany($input['details']);
            // $order->notifyCustomer();
        }, 3);
        
       

        return redirect()->route('orders.index');     
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $orders = Order::find($id);
        
        
        // return view('orders.editOrder',[
        //     'orders'=>$orders,
        //     
        // ]);

        $orders = Order::with(['details.product', 'customer', 'deliveryPersonnel'])->whereId($id)->first();
        // dd($orders->toArray());

        $AllCustomers = Customer::all();
        $AllProducts = Product::all();
        $AllPersonnels = Personnel::where('personneltype', '=', "delivery")->get();


        return view('orders.edit', [
                'orders' => $orders, 
                'AllCustomers'=>$AllCustomers,
               'products'=>$AllProducts,
               'AllPersonnels'=>$AllPersonnels
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|in:cash,cod,credit',
            'status' => 'required|in:Pending,Processed,Delivered,Received',
            'order_date' => 'required|date',
            'served_by' => 'required|exists:users,id',
            'delivered_by' => 'required|exists:personnels,id',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required_with_all:details.*.unit_price,details.*.quantity',
            'details.*.unit_price' => 'required_with_all:details.*.product_id,details.*.quantity',
            'details.*.quantity' => 'required_with_all:details.*.product_id,details.*.unit_price'
           
        ]);



        // dd($input);

        DB::transaction(function () use ($input, $id) {
            // $order = Order::create(array_except($input, ['details']));
            // $order->details()->createMany($input['details']);
            // $order->notifyCustomer();
            OrderLine::whereOrderId($id)->delete();

            $order = Order::find($id);
            $order->update($input);
            $order->details()->createMany($input['details']);

            // if( $order->status == 'Processed' ){
            //     $order->notifyCustomer();
            // }

        }, 3);
        
       

        return redirect()->route('orders.index');     
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        $order->delete(); //deletion of the selected id
        // session()->flash('delete',$customer->customer_fname.' '. $customer->customer_lname.' has been deleted Successfully.');//displaying notification for success deletion into the database
        return redirect()->route('orders.index');
    }
}
