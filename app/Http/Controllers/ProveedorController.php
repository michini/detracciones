<?php

namespace App\Http\Controllers;

use App\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Proveedor::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*$messages = [
            'ruc.unique' => 'El RUC ya esta en uso',
            'cuenta.unique' => 'El numero de cuenta ya esta en uso'
        ];
        $v = \Validator::make($request->all(),[
                'ruc'=>'unique:proveedores|required|max:11',
                'cuenta'=>'required|unique:proveedores,nro_cuenta',
                'nombre'=>'required'
            ], $messages)->validate();*/
        $proveedor = Proveedor::firstOrCreate([
                'ruc'=> $request->get('ruc'),
                'nro_cuenta' => $request->get('cuenta'),
                'nombre' => $request->get('nombre')
            ]);

        /*$proveedor = new Proveedor();
        $proveedor->ruc = $request->get('ruc');
        $proveedor->nro_cuenta = $request->get('cuenta');
        $proveedor->nombre = $request->get('nombre');
        $proveedor->save();*/
       
        return response()->json(['proveedor_insertado'=>true,'id_provider'=>$proveedor->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        
        $provider = \DB::table('proveedores')->select('*')->where('ruc',$id)->first();
        //dd(Proveedor::first($id));
        return response()->json($provider);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function edit(Proveedor $proveedor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proveedor $proveedor)
    {
        //
    }
}
