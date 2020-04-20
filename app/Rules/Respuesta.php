<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Pregunta;
use App\Opcion;

class Respuesta implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
        $collection = collect($value);
        $respuestas = $collection->sort(function($a, $b){
            if ($a['id'] == $b['id']) 
                return 0;
            else if ($a['id'] < $b['id'])
                return -1;
            else
                return 1;
        });
        foreach( $respuestas as $key => $element) {
            if( $element['id'] == null)
                return false;
            if( $key != 0 && $element['id'] == $value[$key - 1]['id'])
                return false;
            if( $element['selected'] == null)
                return false;
            if( $element['selected']['id'] == null)
                return false;
            if( Opcion::find($element['selected']['id']) == null)
                return false;
            if( Opcion::find($element['selected']['id'])->pregunta == $element['id'])
                return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El listado de respuestas está malformado.';
    }
}
