<?php

namespace App\Http\Controllers;

use App\Models\BaseDatos;
use App\Models\Registro;
use Illuminate\Http\Request;

class BaseDatosController extends Controller
{
    /**
     * Muestra el dashboard con la lista de bases de datos.
     */
    public function index()
    {
        $basesDatos = BaseDatos::withCount('registros')->latest()->get();
        return view('dashboard', compact('basesDatos'));
    }

    /**
     * Guarda una nueva base de datos (Estructura).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:basesdatos,nombre',
            'columnas' => 'required|array|min:1',
            'columnas.*.nombre' => 'required|string|max:50',
            'columnas.*.tipo' => 'required|in:texto,numero,fecha,booleano',
        ]);

        BaseDatos::create([
            'nombre' => $request->nombre,
            'configuracion_tabla' => $request->columnas, 
        ]);

        return redirect()->route('dashboard')->with('success', 'Proyecto creado correctamente.');
    }

    /**
     * Muestra la base de datos y sus registros con ordenamiento dinámico.
     */
    public function show(Request $request, BaseDatos $baseDatos)
    {
        $sortBy = $request->input('sort_by'); 
        $sortOrder = $request->input('sort_order', 'asc');

        $query = $baseDatos->registros();

        if ($sortBy) {
            // Buscamos el tipo de dato de la columna para ordenar correctamente
            $columnaConfig = collect($baseDatos->configuracion_tabla)->firstWhere('nombre', $sortBy);
            $tipo = $columnaConfig['tipo'] ?? 'texto';

            // Sentencia SQL segura para extraer valores del JSON
            // JSON_UNQUOTE: Quita comillas para que el texto se ordene bien
            // JSON_EXTRACT: Saca el valor. Usamos '$.\"Nombre\"' para soportar tildes y espacios.
            $jsonExtract = "JSON_UNQUOTE(JSON_EXTRACT(datos, '$.\"$sortBy\"'))";

            if ($tipo === 'numero') {
                // Casteamos a DECIMAL para orden numérico real (1, 2, 10) y no alfabético (1, 10, 2)
                $query->orderByRaw("CAST($jsonExtract AS DECIMAL(20,2)) $sortOrder");
            } elseif ($tipo === 'fecha') {
                $query->orderByRaw("CAST($jsonExtract AS DATE) $sortOrder");
            } else {
                // LOWER para orden insensible a mayúsculas/minúsculas
                $query->orderByRaw("LOWER($jsonExtract) $sortOrder");
            }
        } else {
            $query->latest();
        }

        $registros = $query->get();

        return view('basesdatos.show', compact('baseDatos', 'registros', 'sortBy', 'sortOrder'));
    }

    /**
     * Crea un nuevo registro.
     */
    public function storeRegistro(Request $request, BaseDatos $baseDatos)
    {
        // Validamos usando la configuración de la tabla
        $datos = $request->validate($this->getValidationRules($baseDatos));

        $baseDatos->registros()->create([
            'datos' => $datos['datos'] // Guardamos el array validado
        ]);

        return back()->with('success', 'Registro añadido.');
    }

    /**
     * Actualiza un registro existente (y gestiona campos extra).
     */
    public function updateRegistro(Request $request, BaseDatos $baseDatos, Registro $registro)
    {
        // 1. Validamos los campos oficiales
        $validated = $request->validate($this->getValidationRules($baseDatos));
        $datosFinales = $validated['datos'];

        // 2. Procesamos campos extra (NoSQL) si vienen del Modal de Detalles
        if ($request->has('extras_keys') && $request->has('extras_values')) {
            $keys = $request->input('extras_keys');
            $values = $request->input('extras_values');
            
            foreach ($keys as $index => $key) {
                if (!empty($key)) {
                    $datosFinales[$key] = $values[$index] ?? null;
                }
            }
        }

        // 3. Preservamos el color de fila si existe y no se ha enviado uno nuevo
        // (Esto es útil si editamos desde el modal y no enviamos el color explícitamente)
        if (!isset($datosFinales['_row_theme']) && isset($registro->datos['_row_theme'])) {
            $datosFinales['_row_theme'] = $registro->datos['_row_theme'];
        }

        // 4. Actualizamos (Reemplazamos el JSON antiguo con el nuevo reconstruido)
        $registro->update(['datos' => $datosFinales]);

        return back()->with('success', 'Registro actualizado.');
    }

    public function destroy(BaseDatos $baseDatos)
    {
        $baseDatos->delete();
        return redirect()->route('dashboard')->with('success', 'Base de datos eliminada.');
    }

    public function create() { return view('basesdatos.create'); }

    public function updateColumnConfig(Request $request, BaseDatos $baseDatos)
    {
        $columnaNombre = $request->input('columna');
        $colorClass = $request->input('color_class');

        $config = $baseDatos->configuracion_tabla;

        foreach ($config as &$col) {
            if ($col['nombre'] === $columnaNombre) {
                $col['estilo'] = $colorClass;
                break;
            }
        }

        $baseDatos->update(['configuracion_tabla' => $config]);
        return back()->with('success', 'Columna actualizada.');
    }

    /**
     * Genera reglas de validación dinámicas basadas en la configuración JSON.
     */
    private function getValidationRules(BaseDatos $baseDatos): array
    {
        $reglas = [];
        foreach ($baseDatos->configuracion_tabla as $columna) {
            $nombre = $columna['nombre'];
            $tipo = $columna['tipo'];
            
            // Regla base: requerido (puedes cambiarlo a nullable si prefieres)
            $rules = ['required'];

            if ($tipo === 'numero') $rules[] = 'numeric';
            if ($tipo === 'fecha') $rules[] = 'date';
            if ($tipo === 'texto') $rules[] = 'string';
            
            // Validación para arrays anidados en Laravel
            $reglas["datos.$nombre"] = $rules; 
        }
        
        // Permitimos el campo de tema opcional
        $reglas['datos._row_theme'] = ['nullable', 'string'];

        return $reglas;
    }
}