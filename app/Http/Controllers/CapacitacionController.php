<?php

namespace App\Http\Controllers;

use App\Capacitacion;
use App\Examen;
use App\Respuesta;
use App\Opcion;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class CapacitacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $capacitaciones = Capacitacion::all();
        return $capacitaciones;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $capacitacion = Capacitacion::create($request->all());
        $capacitacion->activo = false;
        return $capacitacion;
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
        $capacitacion = Capacitacion::findOrFail($id);
        return $capacitacion;
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
        $capacitacion = Capacitacion::findOrFail($id);
        $capacitacion->update($request->all());
        return $capacitacion;
    }

    public function activar($id) 
    {
        $capacitacion = Capacitacion::findOrFail($id);
        $preguntas = Preguntas::where("capacitacion_id", $id)->whereNull("correcto")->count();
        if( $preguntas > 0 )
            return response(412)->json([
                "message" => "Todas las preguntas deben tener una respuesta correcta antes de activar la capacitación."
            ]);
        $preguntas = Preguntas::where("capacitacion_id", $id)->count();
        if( $preguntas < $capacitacion->minimo )
            return response(412)->json([
                "message" => "El puntaje minimo no puede ser mayor a la cantidad de preguntas."
            ]);
        $capacitacion->activo = true;
        $capacitacion->save();
        return $capacitacion;
    }

    public function desactivar($id) 
    {
        $capacitacion = Capacitacion::findOrFail($id);
        $capacitacion->activo = false;
        $capacitacion->save();
        return $capacitacion;
    }

    public function preguntas($id)
    {
        $capacitacion = Capacitacion::findOrFail($id);
        return $capacitacion->preguntas;
    }

    public function examen(Request $request, $id)
    {
        $capacitacion = Capacitacion::findOrFail($id);
        $examen = Examen::where("user_id", $request->user()->id)
                        ->where("capacitacion_id", $id)->first();
        return $examen;
    }

    public function iniciarExamen(Request $request, $id)
    {
        $capacitacion = Capacitacion::findOrFail($id);
        $examen = Examen::where("user_id", $request->user()->id)
                        ->where("capacitacion_id", $id)->first();
        if ($examen != null){
            if( $examen->estado == "Terminado"){
                if( $examen->puntaje >= $capacitacion->puntaje_minimo)
                    return response(412)->json(["message" => "Ya aprobaste este examen"]);
                if(Carbon::parse($examen->fecha_fin)->addDays($capacitacion->dias_reintento) < Carbon::now())
                    return response(412)->json(["message" => "Este examen solo se puede realizar una vez cada " . $capacitacion->dias_reintento . " días"]);
            }
            $examen->delete();
        }
        $examen = Examen::create([
            "user_id" => $request->user()->id, 
            "capacitacion_id" => $id,
            "estado" => "En Proceso"
        ]);
        $capacitacion = Capacitacion::findOrFail($id);
        return $capacitacion->with("preguntas.opciones");
    }

    public function finalizarExamen(Request $request, $id)
    {
        $capacitacion = Capacitacion::findOrFail($id);
        $examen = Examen::where("user_id", $request->user()->id)
                        ->where("capacitacion_id", $id)->first();
        if ($examen == null)
            return response(412)->json(["message" => "No tenés un examen en proceso"]);
        if ($examen->estado == "Terminado")
            return response(412)->json(["message" => "Ya terminaste este examen"]);
        $examen->puntaje = 0;
        $preguntas = $request->get("preguntas");
        DB::beginTransaction();
        foreach ( $preguntas as $pregunta ) {
            $pregunta = Pregunta::find($pregunta['id']);
            $opcion = Opcion::find($pregunta['selected']['id']);
            $respuesta = Respuesta::create([
                "examen_id" => $examen->id,
                "opcion_id" => $opcion->id
            ]);
            if( $opcion->id == $pregunta->correcto->id )
                $examen->puntaje++;
        }
        $examen->estado = "Terminado";
        $examen->fecha_fin = Carbon::now();
        $examen->save();
        DB::commit();
        return $examen;
    }
}
