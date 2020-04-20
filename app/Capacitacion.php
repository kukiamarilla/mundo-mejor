<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Capacitacion extends Model
{
    //
    protected $fillable = [
        'nombre', 'video'
    ];

    public function examenes()
    {
        return $this->hasMany("App\Examen");
    }

    public function preguntas()
    {
        return $this->hasMany("App\Pregunta");
    }

}
