<?php

namespace App\Http\Controllers;

use App\Detraccion;
use Illuminate\Http\Request;

class DetraccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('detracciones.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $detr = new Detraccion();
        $detr->tipo = $request->get('tipo');
        $detr->numero = $request->get('numero');
        $detr->razon_social = $request->get('razon_social');
        $detr->nro_proforma = $request->get('nro_proforma');
        $detr->bien_servicio = $request->get('bien_servicio');
        $detr->cuenta = $request->get('cuenta');
        $detr->importe = $request->get('importe');
        $detr->operacion = $request->get('operacion');
        $detr->periodo = $request->get('periodo');
        $detr->comprobante = $request->get('comprobante');
        $detr->serie = $request->get('serie');
        $detr->comprobante_numero = $request->get('comprobante_numero');
        $detr->id_proveedor = $request->get('id_proveedor');
        $detr->save();                    
        return response()->json(['insert'=>true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Detracciones  $detracciones
     * @return \Illuminate\Http\Response
     */
    public function show(Detracciones $detracciones)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Detracciones  $detracciones
     * @return \Illuminate\Http\Response
     */
    public function edit(Detracciones $detracciones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Detracciones  $detracciones
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Detracciones $detracciones)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Detracciones  $detracciones
     * @return \Illuminate\Http\Response
     */
    public function destroy(Detracciones $detracciones)
    {
        //
    }
}
