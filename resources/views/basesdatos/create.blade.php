<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                &larr; Volver
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Nueva Base de Datos
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="p-8">
                    
                    <form action="{{ route('basesdatos.store') }}" method="POST" id="dbForm">
                        @csrf

                        <div class="mb-8">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre del Proyecto</label>
                            <input type="text" name="nombre" id="nombre" placeholder="Ej: Inventario 2024" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-zinc-900 focus:ring-zinc-900 transition-colors text-lg py-3 px-4">
                            @error('nombre') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Estructura de Datos</h3>
                                <button type="button" onclick="addColumn()" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                    + Añadir Columna
                                </button>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6 border border-gray-100 dark:border-gray-700 space-y-4" id="columnsContainer">
                                <p id="emptyState" class="text-center text-gray-400 text-sm py-4">Define los campos de tu tabla (ej: Nombre, Precio...)</p>
                            </div>
                            @error('columnas') <span class="text-red-500 text-sm mt-1">Debes añadir al menos una columna.</span> @enderror
                        </div>

                        <div class="flex justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('dashboard') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-lg shadow-lg shadow-zinc-900/20 transition-all transform active:scale-95">
                                Crear Base de Datos
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Contador para índices únicos
        let colIndex = 0;

        function addColumn() {
            const container = document.getElementById('columnsContainer');
            const emptyState = document.getElementById('emptyState');
            
            // Ocultar mensaje vacío si existe
            if(emptyState) emptyState.style.display = 'none';

            // HTML de la nueva fila
            const rowHtml = `
                <div class="flex gap-4 items-start animate-fade-in-up" id="row-${colIndex}">
                    <div class="flex-1">
                        <input type="text" name="columnas[${colIndex}][nombre]" placeholder="Nombre del campo" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="w-1/3">
                        <select name="columnas[${colIndex}][tipo]" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="texto">Texto</option>
                            <option value="numero">Número</option>
                            <option value="fecha">Fecha</option>
                            <option value="booleano">Si/No</option>
                        </select>
                    </div>
                    <button type="button" onclick="removeColumn('row-${colIndex}')" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </div>
            `;

            // Insertar HTML
            container.insertAdjacentHTML('beforeend', rowHtml);
            colIndex++;
        }

        function removeColumn(rowId) {
            document.getElementById(rowId).remove();
        }

        // Añadir una columna por defecto al cargar
        document.addEventListener('DOMContentLoaded', () => {
            addColumn();
        });
    </script>
</x-app-layout>