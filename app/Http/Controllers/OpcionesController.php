<?php

namespace App\Http\Controllers;

use App\Opcion;
use Illuminate\Http\Request;

class OpcionesController extends Controller
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
        $opcion = Opcion::create($request->all());
        return $opcion;
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
        $opcion = Opcion::findOrFail($id);
        $opcion->update($request->only("opcion"));
        return $opcion;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $opcion = Opcion::findOrFail($id);
        $respuestas = Respuesta::where('opcion_id', $opcion->id)->count();
        if ($respuestas > 0)
            return response(412)->json(["message" => "No se puede eliminar esta opción porque ya hay recursos relacionados"]);
        $opcion->delete();
        return response()->json(["message" => "Opcion Eliminada"]);
    }

    public function correcto($id)
    {
        $opcion = Opcion::findOrFail($id);
        $pregunta = $opcion->pregunta;
        $pregunta->correcto = $opcion;
        $pregunta->save();
        return $opcion;
    }
}
