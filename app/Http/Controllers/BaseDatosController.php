<?php

namespace App\Http\Controllers;

use App\Models\BaseDatos;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BaseDatosController extends Controller
{
    /**
     * Muestra el dashboard con la lista de bases de datos.
     */
    public function index()
    {
        // Obtenemos las bases de datos ordenadas por fecha de creación (descendiente)
        // withCount('registros') nos crea una propiedad 'registros_count' automáticamente
        $basesDatos = BaseDatos::withCount('registros')->latest()->get();

        return view('dashboard', compact('basesDatos'));
    }

    /**
     * Muestra el formulario para crear una nueva base de datos.
     */
    public function create()
    {
        return view('basesdatos.create');
    }

    /**
     * Guarda la nueva base de datos en el sistema.
     */
    public function store(Request $request)
    {
        // 1. Validamos que nos envíen un nombre y al menos una columna válida
        $request->validate([
            'nombre' => 'required|string|max:255|unique:basesdatos,nombre',
            'columnas' => 'required|array|min:1',
            'columnas.*.nombre' => 'required|string|max:50',
            'columnas.*.tipo' => 'required|in:texto,numero,fecha,booleano', // Tipos permitidos
        ]);

        // 2. Creamos la base de datos
        // Nota: El 'slug' se genera automáticamente en el modelo BaseDatos (método boot)
        // Nota 2: La 'configuracion_tabla' se convierte a JSON automáticamente por el $casts del modelo
        BaseDatos::create([
            'nombre' => $request->nombre,
            'configuracion_tabla' => $request->columnas, 
        ]);

        // 3. Redirigimos al dashboard con éxito
        return redirect()->route('dashboard')->with('success', '¡Base de datos creada correctamente!');
    }

    /**
     * Muestra la base de datos con sus registros.
     */
    public function show(BaseDatos $baseDatos)
    {
        // Cargamos los registros ordenados por el más reciente
        $registros = $baseDatos->registros()->latest()->get();

        return view('basesdatos.show', compact('baseDatos', 'registros'));
    }

    /**
     * Guarda un nuevo registro dinámico.
     */
    public function storeRegistro(Request $request, BaseDatos $baseDatos)
    {
        // 1. Construimos las reglas de validación dinámicamente
        $reglas = [];
        
        // Recorremos la configuración de columnas (que ya es un array gracias al cast del modelo)
        foreach ($baseDatos->configuracion_tabla as $columna) {
            $nombreCampo = $columna['nombre'];
            $tipoCampo = $columna['tipo'];
            
            // Regla base
            $rules = ['required'];

            // Añadimos validaciones específicas según el tipo
            if ($tipoCampo === 'numero') $rules[] = 'numeric';
            if ($tipoCampo === 'fecha') $rules[] = 'date';
            if ($tipoCampo === 'texto') $rules[] = 'string';
            
            // Asignamos la regla al campo dentro del array 'datos'
            // La sintaxis 'datos.NombreCampo' valida arrays en Laravel
            $reglas["datos.$nombreCampo"] = $rules; 
        }

        $request->validate($reglas);

        // 2. Creamos el registro
        $baseDatos->registros()->create([
            'datos' => $request->input('datos') // Guardamos todo el array de datos JSON
        ]);

        return back()->with('success', 'Registro añadido correctamente.');
    }
}