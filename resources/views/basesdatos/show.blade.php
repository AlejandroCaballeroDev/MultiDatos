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

            <button onclick="openModal()" class="px-4 py-2 bg-zinc-900 text-white text-sm font-medium rounded-lg hover:bg-zinc-800 shadow-lg shadow-zinc-900/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Registro
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700">
                
                @if($registros->isEmpty())
                    <div class="p-12 text-center flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">La tabla está vacía</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Añade tu primer registro para empezar.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-4 font-medium w-16">#ID</th>
                                    @foreach($baseDatos->configuracion_tabla as $columna)
                                        <th class="px-6 py-4 font-medium">{{ $columna['nombre'] }}</th>
                                    @endforeach
                                    <th class="px-6 py-4 font-medium text-right">Creado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($registros as $registro)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 text-gray-400 font-mono text-xs">
                                            {{ $registro->id }}
                                        </td>
                                        
                                        @foreach($baseDatos->configuracion_tabla as $columna)
                                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                                @php 
                                                    $valor = $registro->datos[$columna['nombre']] ?? '-';
                                                    // Si es booleano, lo mostramos bonito
                                                    if($columna['tipo'] === 'booleano') {
                                                        $valor = $valor == '1' ? 'Sí' : 'No';
                                                    }
                                                @endphp
                                                
                                                @if($columna['tipo'] === 'booleano')
                                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $valor === 'Sí' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                        {{ $valor }}
                                                    </span>
                                                @else
                                                    {{ $valor }}
                                                @endif
                                            </td>
                                        @endforeach
                                        
                                        <td class="px-6 py-4 text-right text-gray-400 text-xs">
                                            {{ $registro->created_at->format('d M H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="modalOverlay" class="fixed inset-0 bg-black/50 z-40 hidden backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div id="insertModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg pointer-events-auto transform scale-95 opacity-0 transition-all duration-300" id="modalContent">
            
            <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nuevo Registro</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('registros.store', $baseDatos) }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                @foreach($baseDatos->configuracion_tabla as $columna)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $columna['nombre'] }}
                        </label>
                        
                        @if($columna['tipo'] === 'texto')
                            <input type="text" name="datos[{{ $columna['nombre'] }}]" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                        
                        @elseif($columna['tipo'] === 'numero')
                            <input type="number" step="any" name="datos[{{ $columna['nombre'] }}]" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                        
                        @elseif($columna['tipo'] === 'fecha')
                            <input type="date" name="datos[{{ $columna['nombre'] }}]" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                        
                        @elseif($columna['tipo'] === 'booleano')
                            <select name="datos[{{ $columna['nombre'] }}]" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        @endif
                    </div>
                @endforeach

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-zinc-900 text-white text-sm font-medium rounded-lg hover:bg-zinc-800 shadow-lg">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalOverlay').classList.remove('hidden');
            const modal = document.getElementById('insertModal');
            modal.classList.remove('hidden');
            // Pequeño timeout para la animación CSS
            setTimeout(() => {
                document.getElementById('modalContent').classList.remove('scale-95', 'opacity-0');
                document.getElementById('modalContent').classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeModal() {
            const content = document.getElementById('modalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                document.getElementById('insertModal').classList.add('hidden');
                document.getElementById('modalOverlay').classList.add('hidden');
            }, 300);
        }
    </script>
</x-app-layout>