<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Respuestas extends Model
{
    //
    protected $fillable = [
        'opcion_id', 'examen_id'
    ];

    public function opcion()
    {
        return $this->belongsTo("App\Opcion");
    }

    public function examen()
    {
        return $this->belongsTo("App\Examen");
    }
}
