<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Opcion extends Model
{
    //
    protected $fillable = [
        "opcion", "pregunta_id"
    ];

    public function pregunta()
    {
        return $this->belongsTo("App\Pregunta");
    }

    public function respuestas()
    {
        return $this->hasMany("App\Respuesta");
    }
}
