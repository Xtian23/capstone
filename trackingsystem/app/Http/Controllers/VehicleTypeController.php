<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\VehicleType;


class VehicleTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $AllVehicleTypes=VehicleType::all();
        $AllVehicleTypes=VehicleType::orderBy('vehicletype','asc')->get();
        return view('vehicletypes.index',[
                    'vehicletypes'=>$AllVehicleTypes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vehicletypes.addVehicleType');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $newVehicleType = new VehicleType;
        $newVehicleType->vehicletype=$request->vehicletype;
        $newVehicleType->save();

        return redirect()->route('vehicles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $vehicletype = VehicleType::find($id);
         return view('vehicletypes.edit',compact('vehicletype'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vehicletype = VehicleType::find($id);
        return view('vehicletypes.edit',compact('vehicletype'));
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
        $newVehicleType =  VehicleType::find($id);
        $newVehicleType->vehicletype=$request->vehicletype;
        $newVehicleType->save();
        return redirect()->route('vehicles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vehicletype=VehicleType::find($id);
        $vehicletype->delete();
        return redirect('vehicles.index');
    }
}
