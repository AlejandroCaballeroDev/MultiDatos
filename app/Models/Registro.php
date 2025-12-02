<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    use HasFactory;

    protected $table = 'registros';

    protected $fillable = [
        'basesdatos_id',
        'datos',
    ];

    // Casteamos los datos a array (automáticamente hace json_decode/encode)
    protected $casts = [
        'datos' => 'array',
    ];

    /**
     * Relación: Un Registro pertenece a una Base de Datos.
     */
    public function baseDatos()
    {
        // Especificamos 'basesdatos_id' porque es el nombre exacto en tu migración
        return $this->belongsTo(BaseDatos::class, 'basesdatos_id');
    }
}