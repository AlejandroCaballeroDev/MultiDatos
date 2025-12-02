<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                        {{ $baseDatos->nombre }}
                    </h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-mono">/{{ $baseDatos->slug }}</p>
                </div>
            </div>
            
            <div class="text-sm text-gray-500 dark:text-gray-400 hidden sm:block">
                Fila superior: Crear • Lápiz: Editar en línea
            </div>
        </div>
    </x-slot>

    <form id="inline-create-form" action="{{ route('registros.store', $baseDatos) }}" method="POST">
        @csrf
    </form>

    <div class="py-8 px-4 w-full"> 
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-2 py-2 font-medium w-10 text-center bg-gray-50 dark:bg-gray-800 sticky left-0 z-10 border-r border-gray-200 dark:border-gray-700">#</th>
                            
                            @foreach($baseDatos->configuracion_tabla as $columna)
                                <th class="px-2 py-2 font-medium whitespace-nowrap min-w-[100px]">
                                    {{ $columna['nombre'] }}
                                </th>
                            @endforeach
                            
                            <th class="px-2 py-2 font-medium text-center w-24 bg-gray-50 dark:bg-gray-800 sticky right-0 z-10 border-l border-gray-200 dark:border-gray-700 shadow-[-5px_0_10px_-5px_rgba(0,0,0,0.1)]">
                                Acción
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        
                        <tr class="bg-indigo-50/30 dark:bg-indigo-900/10">
                            <td class="px-2 py-1.5 text-center text-indigo-500 font-bold sticky left-0 z-10 bg-indigo-50/50 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
                                +
                            </td>

                            @foreach($baseDatos->configuracion_tabla as $columna)
                                <td class="px-2 py-1.5">
                                    @php $tipo = $columna['tipo']; @endphp

                                    @if($tipo === 'texto')
                                        <input type="text" form="inline-create-form" name="datos[{{ $columna['nombre'] }}]" placeholder="..." required
                                            class="w-full text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white dark:bg-gray-900 dark:text-white dark:placeholder-gray-600">
                                    @elseif($tipo === 'numero')
                                        <input type="number" step="any" form="inline-create-form" name="datos[{{ $columna['nombre'] }}]" placeholder="0" required
                                            class="w-full text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white dark:bg-gray-900 dark:text-white dark:placeholder-gray-600">
                                    @elseif($tipo === 'fecha')
                                        <input type="date" form="inline-create-form" name="datos[{{ $columna['nombre'] }}]" required
                                            class="w-full text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white dark:bg-gray-900 dark:text-white">
                                    @elseif($tipo === 'booleano')
                                        <select form="inline-create-form" name="datos[{{ $columna['nombre'] }}]"
                                            class="w-full text-xs px-2 py-1 rounded border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white dark:bg-gray-900 dark:text-white">
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    @endif
                                </td>
                            @endforeach

                            <td class="px-2 py-1.5 text-center sticky right-0 z-10 bg-indigo-50/50 dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700">
                                <button type="submit" form="inline-create-form" class="w-full bg-zinc-900 hover:bg-zinc-800 text-white font-medium py-1 px-2 rounded text-[10px] uppercase tracking-wide transition-all shadow-md">
                                    Crear
                                </button>
                            </td>
                        </tr>

                        @forelse($registros as $registro)
                            
                            {{-- FORMULARIO DE EDICIÓN OCULTO PARA ESTA FILA --}}
                            <form id="form-edit-{{ $registro->id }}" action="{{ route('registros.update', ['baseDatos' => $baseDatos, 'registro' => $registro]) }}" method="POST">
                                @csrf
                                @method('PUT')
                            </form>

                            {{-- MODO VISTA (Visible por defecto) --}}
                            <tr id="row-view-{{ $registro->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-2 py-2 text-gray-400 font-mono text-xs text-center sticky left-0 bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 group-hover:bg-gray-50 dark:group-hover:bg-gray-700/50">
                                    {{ $registro->id }}
                                </td>
                                
                                @foreach($baseDatos->configuracion_tabla as $columna)
                                    <td class="px-2 py-2 text-gray-700 dark:text-gray-300 whitespace-nowrap text-xs sm:text-sm">
                                        @php 
                                            $valor = $registro->datos[$columna['nombre']] ?? '-';
                                            if($columna['tipo'] === 'booleano') {
                                                $valor = $valor == '1' ? 'Sí' : 'No';
                                            }
                                        @endphp
                                        
                                        @if($columna['tipo'] === 'booleano')
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium {{ $valor === 'Sí' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $valor }}
                                            </span>
                                        @else
                                            {{ $valor }}
                                        @endif
                                    </td>
                                @endforeach
                                
                                <td class="px-2 py-2 text-center sticky right-0 bg-white dark:bg-gray-800 border-l border-gray-100 dark:border-gray-700 shadow-[-5px_0_10px_-5px_rgba(0,0,0,0.05)] group-hover:bg-gray-50 dark:group-hover:bg-gray-700/50">
                                    <button onclick="toggleEditRow({{ $registro->id }})" class="p-1 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded transition-all" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>

                            {{-- MODO EDICIÓN (Oculto por defecto, 'hidden') --}}
                            <tr id="row-edit-{{ $registro->id }}" class="bg-yellow-50/50 dark:bg-yellow-900/10 hidden">
                                <td class="px-2 py-2 text-yellow-600 font-mono text-xs text-center sticky left-0 bg-yellow-50 dark:bg-gray-800 border-r border-yellow-200 dark:border-gray-700">
                                    {{ $registro->id }}
                                </td>

                                @foreach($baseDatos->configuracion_tabla as $columna)
                                    <td class="px-2 py-1.5">
                                        @php 
                                            $tipo = $columna['tipo'];
                                            $valorActual = $registro->datos[$columna['nombre']] ?? '';
                                        @endphp

                                        @if($tipo === 'texto')
                                            <input type="text" form="form-edit-{{ $registro->id }}" name="datos[{{ $columna['nombre'] }}]" value="{{ $valorActual }}" required
                                                class="w-full text-xs px-2 py-1 rounded border border-yellow-300 dark:border-yellow-600 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 bg-white dark:bg-gray-900 dark:text-white">
                                        @elseif($tipo === 'numero')
                                            <input type="number" step="any" form="form-edit-{{ $registro->id }}" name="datos[{{ $columna['nombre'] }}]" value="{{ $valorActual }}" required
                                                class="w-full text-xs px-2 py-1 rounded border border-yellow-300 dark:border-yellow-600 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 bg-white dark:bg-gray-900 dark:text-white">
                                        @elseif($tipo === 'fecha')
                                            <input type="date" form="form-edit-{{ $registro->id }}" name="datos[{{ $columna['nombre'] }}]" value="{{ $valorActual }}" required
                                                class="w-full text-xs px-2 py-1 rounded border border-yellow-300 dark:border-yellow-600 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 bg-white dark:bg-gray-900 dark:text-white">
                                        @elseif($tipo === 'booleano')
                                            <select form="form-edit-{{ $registro->id }}" name="datos[{{ $columna['nombre'] }}]"
                                                class="w-full text-xs px-2 py-1 rounded border border-yellow-300 dark:border-yellow-600 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 bg-white dark:bg-gray-900 dark:text-white">
                                                <option value="1" {{ $valorActual == '1' ? 'selected' : '' }}>Sí</option>
                                                <option value="0" {{ $valorActual == '0' ? 'selected' : '' }}>No</option>
                                            </select>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="px-2 py-2 text-center sticky right-0 bg-yellow-50 dark:bg-gray-800 border-l border-yellow-200 dark:border-gray-700 flex gap-1 justify-center">
                                    <button type="submit" form="form-edit-{{ $registro->id }}" class="p-1 text-green-600 hover:bg-green-100 rounded" title="Guardar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </button>
                                    <button type="button" onclick="cancelEditRow({{ $registro->id }})" class="p-1 text-red-600 hover:bg-red-100 rounded" title="Cancelar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="100" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">
                                    No hay registros aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SCRIPTS PARA MANEJAR LA EDICIÓN EN LÍNEA --}}
    <script>
        function toggleEditRow(id) {
            // Ocultar la fila de vista
            document.getElementById(`row-view-${id}`).classList.add('hidden');
            // Mostrar la fila de edición
            document.getElementById(`row-edit-${id}`).classList.remove('hidden');
        }

        function cancelEditRow(id) {
            // Mostrar la fila de vista
            document.getElementById(`row-view-${id}`).classList.remove('hidden');
            // Ocultar la fila de edición
            document.getElementById(`row-edit-${id}`).classList.add('hidden');
        }
    </script>
</x-app-layout>