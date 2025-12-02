<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseDatos extends Model
{
    use HasFactory;

    // Definimos la tabla explícitamente porque el nombre no sigue la convención estándar (singular/plural)
    protected $table = 'basesdatos';

    protected $fillable = [
        'nombre',
        'slug',
        'configuracion_tabla',
    ];

    // Casteamos el JSON a array para trabajar con él fácilmente en PHP
    protected $casts = [
        'configuracion_tabla' => 'array',
    ];

    /**
     * Relación: Una Base de Datos tiene muchos Registros.
     */
    public function registros()
    {
        return $this->hasMany(Registro::class, 'basesdatos_id');
    }

    /**
     * Boot function para generar el slug automáticamente al crear.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($baseDatos) {
            if (empty($baseDatos->slug)) {
                $baseDatos->slug = Str::slug($baseDatos->nombre);
            }
        });
    }
}