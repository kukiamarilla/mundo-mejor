<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    //
    protected $fillable = [
        'user_id', 'capacitacion_id', 'estado'
    ]; 

    public function capacitacion()
    {
        return $this->belongsTo("App\Capacitacion");
    }

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function respuestas()
    {
        return $this->hasMany("App\Respuesta");
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($examen) {
            $examen->respuestas()->delete();
        });
    }
}
