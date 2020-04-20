<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Preguntas extends Model
{
    //
    protected $fillable = [
        'pregunta', 'capacitacion_id'
    ];

    protected $hidden = [
        'correcto'
    ];

    public function capacitacion()
    {
        return $this->belongsTo("App\Capacitacion");
    }
    
    public function opciones()
    {
        return $this->hasMany("App\Opcino");
    }

    public function correcto()
    {
        return $this->hasOne("App\Opcion", "correcto");
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($pregunta) {
            $pregunta->opciones()->delete();
        });
    }
}
