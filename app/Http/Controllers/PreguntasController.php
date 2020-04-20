<?php

namespace App\Http\Controllers;

use App\Pregunta;
use Illuminate\Http\Request;
use App\Capacitacion;

class PreguntasController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $capacitacion = Capacitacion::find($request->get('capacitacion_id'));
        if( $capacitacion->activo )
            return response(412)->json(["message" => "No se puede agregar preguntas a una capacitacion activa"]);
        $pregunta = Pregunta::create($request->all());
        return $pregunta->with('opciones');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {
        //
        $pregunta = Pregunta::findOrFail($id);
        return $pregunta->with('opciones');
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
        //
        $pregunta = Pregunta::findOrFail($id);
        $pregunta->update($request->only('pregunta'));
        return $pregunta->with('opciones');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $pregunta = Pregunta::findOrFail($id);
        $pregunta->delete();
        return response()->json(["message" => "Pregunta Eliminada"]);
    }

    public function opciones($id)
    {
        $pregunta = Pregunta::findOrFail($id);
        return $pregunta->opciones;
    }
}
