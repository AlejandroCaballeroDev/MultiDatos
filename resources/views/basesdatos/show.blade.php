<x-app-layout>
    {{-- CSS ADICIONAL --}}
    <style>
        .input-ghost {
            background: transparent;
            border: none;
            border-bottom: 1px solid transparent;
            padding: 0.5rem 0;
            margin: 0;
            width: 100%;
            outline: none;
            box-shadow: none;
            transition: border-color 0.2s;
        }
        .input-ghost:focus {
            box-shadow: none;
            border-bottom: 2px solid #6366f1;
        }
        .custom-scrollbar::-webkit-scrollbar { height: 10px; width: 10px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 99px; border: 2px solid #f8fafc; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #475569; border-color: #0f172a; }
        
        .pop-in { animation: popIn 0.15s ease-out forwards; transform-origin: top center; }
        @keyframes popIn { from { opacity: 0; transform: scale(0.95) translateY(-5px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    </style>

    <div class="min-h-screen bg-gray-50 dark:bg-[#0f172a] flex flex-col">
        
        {{-- HEADER --}}
        <div class="bg-white dark:bg-[#1e293b] border-b border-gray-200 dark:border-gray-700 px-8 py-5 flex justify-between items-center shrink-0 shadow-sm z-30 relative">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-indigo-500 transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white leading-none tracking-tight">{{ $baseDatos->nombre }}</h2>
                    <span class="text-xs font-mono text-gray-400 mt-1 block">/{{ $baseDatos->slug }}</span>
                </div>
            </div>
            <div class="text-sm font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 px-4 py-1.5 rounded-full border border-indigo-100 dark:border-indigo-800">
                {{ $registros->count() }} Registros
            </div>
        </div>

        {{-- AREA DE TRABAJO --}}
        <div class="flex-1 p-6 w-full overflow-hidden flex flex-col">
            
            {{-- Formularios Ocultos Lógica --}}
            <form id="inline-create-form" action="{{ route('registros.store', $baseDatos) }}" method="POST">@csrf</form>
            <form id="columnColorForm" method="POST" action="{{ route('basesdatos.column.config', $baseDatos) }}">
                @csrf <input type="hidden" name="columna" id="colColorName"> <input type="hidden" name="color_class" id="colColorClass">
            </form>
            <form id="rowColorForm" method="POST" action="">
                @csrf @method('PUT') <input type="hidden" name="_row_theme" id="rowColorValue">
            </form>

            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 flex-1 flex flex-col w-full overflow-hidden relative">
                
                {{-- TABLA --}}
                <div class="overflow-auto w-full h-full custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        
                        {{-- CABECERA --}}
                        <thead class="bg-gray-50 dark:bg-[#161e31] sticky top-0 z-20 shadow-sm border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                @foreach($baseDatos->configuracion_tabla as $columna)
                                    @php $estiloColumna = $columna['estilo'] ?? ''; @endphp
                                    <th class="py-3 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group relative {{ $estiloColumna }}">
                                        <div class="flex items-center justify-between">
                                            <a href="{{ route('basesdatos.show', ['baseDatos' => $baseDatos, 'sort_by' => $columna['nombre'], 'sort_order' => ($sortBy === $columna['nombre'] && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-2 select-none w-full">
                                                {{ $columna['nombre'] }}
                                                @if($sortBy === $columna['nombre'])
                                                    <span class="text-indigo-500">
                                                        <svg class="w-3 h-3 transform {{ $sortOrder === 'desc' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" /></svg>
                                                    </span>
                                                @endif
                                            </a>
                                            <button onclick="openColorPicker(event, 'column', '{{ $columna['nombre'] }}')" class="opacity-0 group-hover:opacity-100 p-1 hover:bg-black/10 rounded">
                                                <div class="w-2 h-2 rounded-full bg-gradient-to-r from-pink-500 to-indigo-500"></div>
                                            </button>
                                        </div>
                                    </th>
                                @endforeach
                                <th class="py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center w-24 bg-gray-50 dark:bg-[#161e31]">Acción</th>
                            </tr>

                            {{-- FILA DE CREACIÓN --}}
                            <tr class="bg-indigo-50/40 dark:bg-indigo-900/10 relative z-30">
                                @foreach($baseDatos->configuracion_tabla as $columna)
                                    <td class="py-1 px-4 border-b border-indigo-100 dark:border-indigo-900 relative">
                                        @php $tipo = $columna['tipo']; @endphp
                                        <div class="relative z-40 min-w-[100px]">
                                            @if($tipo === 'texto')
                                                <input type="text" form="inline-create-form" name="datos[{{ $columna['nombre'] }}]" placeholder="..." required class="input-ghost text-sm text-indigo-700 dark:text-indigo-300 placeholder-indigo-300/50 bg-transparent">
                                            @elseif($tipo === 'numero')
                                                <input type="number" step="any" form="inline-create-form" name="datos[{{ $columna['nombre'] }}]" placeholder="0" required class="input-ghost text-sm text-indigo-700 dark:text-indigo-300 placeholder-indigo-300/50 bg-transparent">
                                            @elseif($tipo === 'fecha')
                                                <input type="date" form="inline-create-form" name="datos[{{ $columna['nombre'] }}]" required class="input-ghost text-sm text-indigo-700 dark:text-indigo-300 bg-transparent">
                                            @elseif($tipo === 'booleano')
                                                <select form="inline-create-form" name="datos[{{ $columna['nombre'] }}]" class="input-ghost text-sm text-indigo-700 dark:text-indigo-300 cursor-pointer bg-transparent">
                                                    <option value="" disabled selected>Elegir</option>
                                                    <option value="1">Sí</option>
                                                    <option value="0">No</option>
                                                </select>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                                <td class="py-1 px-2 border-b border-indigo-100 dark:border-indigo-900 text-center align-middle relative z-40">
                                    <button type="submit" form="inline-create-form" class="text-[10px] font-bold bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded shadow-sm transition-colors w-full">
                                        CREAR
                                    </button>
                                </td>
                            </tr>
                        </thead>

                        {{-- CUERPO --}}
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-[#1e293b]">
                            @forelse($registros as $registro)
                                @php $rowTheme = $registro->datos['_row_theme'] ?? ''; @endphp
                                
                                <form id="form-edit-{{ $registro->id }}" action="{{ route('registros.update', ['baseDatos' => $baseDatos, 'registro' => $registro]) }}" method="POST">@csrf @method('PUT')</form>

                                {{-- FILA VISTA --}}
                                <tr id="row-view-{{ $registro->id }}" class="group hover:bg-gray-50 dark:hover:bg-zinc-900/50 transition-colors {{ $rowTheme }}">
                                    @foreach($baseDatos->configuracion_tabla as $columna)
                                        <td class="py-4 px-4 text-sm text-gray-700 dark:text-gray-300 truncate max-w-[200px] {{ $columna['estilo'] ?? '' }}" title="{{ $registro->datos[$columna['nombre']] ?? '' }}">
                                            @php 
                                                $valor = $registro->datos[$columna['nombre']] ?? '-';
                                                $tipo = $columna['tipo'];
                                            @endphp
                                            
                                            @if($tipo === 'booleano')
                                                <div class="flex items-center gap-2">
                                                    <div class="w-2 h-2 rounded-full {{ $valor == '1' || $valor == 'Sí' ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                                                    <span class="text-xs font-medium opacity-80">{{ $valor == '1' || $valor == 'Sí' ? 'Sí' : 'No' }}</span>
                                                </div>
                                            @elseif($tipo === 'numero')
                                                <span class="font-mono text-indigo-600 dark:text-indigo-400 font-medium">{{ $valor }}</span>
                                            @else
                                                {{ $valor }}
                                            @endif
                                        </td>
                                    @endforeach

                                    {{-- ACCIONES --}}
                                    <td class="py-4 px-3 text-center bg-white/0">
                                        <div class="flex justify-center items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button onclick="openColorPicker(event, 'row', {{ $registro->id }})" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg">
                                                <div class="w-3 h-3 rounded-full border border-current"></div>
                                            </button>
                                            <button onclick="toggleEditRow({{ $registro->id }})" class="p-2 text-gray-400 hover:text-indigo-500 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                            <button onclick="openDetailsModal({{ $registro->id }})" class="p-2 text-gray-400 hover:text-purple-500 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- FILA EDICIÓN --}}
                                <tr id="row-edit-{{ $registro->id }}" class="hidden bg-amber-50/50 dark:bg-amber-900/10">
                                    @foreach($baseDatos->configuracion_tabla as $columna)
                                        <td class="py-3 px-4 align-top">
                                            @php $tipo = $columna['tipo']; $valorActual = $registro->datos[$columna['nombre']] ?? ''; @endphp
                                            @if($tipo === 'booleano')
                                                <select form="form-edit-{{ $registro->id }}" name="datos[{{ $columna['nombre'] }}]" class="w-full text-sm rounded-md border-amber-300 focus:ring-amber-500 bg-white py-1.5"><option value="1" {{ $valorActual == '1' ? 'selected' : '' }}>Sí</option><option value="0" {{ $valorActual == '0' ? 'selected' : '' }}>No</option></select>
                                            @else
                                                <input type="{{ $tipo == 'numero' ? 'number' : ($tipo == 'fecha' ? 'date' : 'text') }}" form="form-edit-{{ $registro->id }}" name="datos[{{ $columna['nombre'] }}]" value="{{ $valorActual }}" class="w-full text-sm px-3 py-1.5 rounded-md border border-amber-300 dark:border-amber-700 bg-white dark:bg-black focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="py-3 px-3 text-center align-middle">
                                        <div class="flex justify-center gap-2">
                                            <button type="submit" form="form-edit-{{ $registro->id }}" class="text-green-600 hover:bg-green-100 p-1.5 rounded-md"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg></button>
                                            <button type="button" onclick="cancelEditRow({{ $registro->id }})" class="text-red-600 hover:bg-red-100 p-1.5 rounded-md"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100" class="py-20 text-center">
                                        <div class="inline-block p-4 rounded-full bg-gray-50 dark:bg-zinc-800 mb-3">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                        </div>
                                        <p class="text-gray-500 text-sm">No hay registros aún.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETALLES --}}
    <div id="detailsModalOverlay" class="fixed inset-0 bg-gray-900/50 z-40 hidden backdrop-blur-sm transition-opacity" onclick="closeDetailsModal()"></div>
    <div id="detailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-2xl w-full max-w-lg pointer-events-auto transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]" id="detailsModalContent">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 dark:text-white">Detalles del Registro</h3>
                <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            <div class="p-5 overflow-y-auto custom-scrollbar flex-1">
                <form id="detailsForm" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    <div class="space-y-3">
                        @foreach($baseDatos->configuracion_tabla as $columna)
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">{{ $columna['nombre'] }}</label>
                                @php $tipo = $columna['tipo']; @endphp
                                @if($tipo === 'booleano') <select name="datos[{{ $columna['nombre'] }}]" id="modal_field_{{ $columna['nombre'] }}" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-black dark:text-white text-sm"><option value="1">Sí</option><option value="0">No</option></select>
                                @else <input type="{{ $tipo == 'numero' ? 'number' : ($tipo == 'fecha' ? 'date' : 'text') }}" name="datos[{{ $columna['nombre'] }}]" id="modal_field_{{ $columna['nombre'] }}" step="any" class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-black dark:text-white text-sm">
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-4 mt-4">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-xs font-bold text-indigo-500 uppercase">Extras</h4>
                            <button type="button" onclick="addExtraField()" class="text-xs text-indigo-600 hover:underline">+ Añadir</button>
                        </div>
                        <div id="extraFieldsContainer" class="space-y-2"></div>
                        <div id="noExtrasMsg" class="text-center py-4 border border-dashed border-gray-200 rounded-lg text-xs text-gray-400">Sin datos extra</div>
                    </div>
                    <div class="pt-4 flex justify-end gap-2 sticky bottom-0 bg-white dark:bg-zinc-900 pb-1">
                        <button type="button" onclick="closeDetailsModal()" class="px-4 py-2 text-xs font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-xs font-bold rounded-lg hover:bg-black shadow-lg">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- POPUP COLOR --}}
    <div id="colorPicker" class="fixed z-50 hidden pop-in bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 p-3 w-48">
        <div class="text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">Seleccionar Estilo</div>
        <div class="grid grid-cols-4 gap-2">
            <button onclick="applyColor('')" class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50"><svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            <button onclick="applyColor('bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300')" class="w-8 h-8 rounded-full bg-red-100 border border-red-200 hover:scale-110 transition-transform"></button>
            <button onclick="applyColor('bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300')" class="w-8 h-8 rounded-full bg-blue-100 border border-blue-200 hover:scale-110 transition-transform"></button>
            <button onclick="applyColor('bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300')" class="w-8 h-8 rounded-full bg-green-100 border border-green-200 hover:scale-110 transition-transform"></button>
            <button onclick="applyColor('bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300')" class="w-8 h-8 rounded-full bg-amber-100 border border-amber-200 hover:scale-110 transition-transform"></button>
            <button onclick="applyColor('bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300')" class="w-8 h-8 rounded-full bg-purple-100 border border-purple-200 hover:scale-110 transition-transform"></button>
            <button onclick="applyColor('bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')" class="w-8 h-8 rounded-full bg-gray-200 border border-gray-300 hover:scale-110 transition-transform"></button>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        const recordsData = @json($registros->pluck('datos', 'id'));
        const officialColumns = @json(collect($baseDatos->configuracion_tabla)->pluck('nombre'));
        const updateUrlBase = "{{ route('registros.update', ['baseDatos' => $baseDatos->id, 'registro' => 'REGISTRO_ID']) }}";

        let activeType = null;
        let activeTarget = null;

        function toggleEditRow(id) {
            document.getElementById(`row-view-${id}`).classList.add('hidden');
            document.getElementById(`row-edit-${id}`).classList.remove('hidden');
        }
        function cancelEditRow(id) {
            document.getElementById(`row-view-${id}`).classList.remove('hidden');
            document.getElementById(`row-edit-${id}`).classList.add('hidden');
        }

        function openDetailsModal(id) {
            const data = recordsData[id] || {};

            document.getElementById('detailsModalOverlay').classList.remove('hidden');
            const modal = document.getElementById('detailsModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('detailsModalContent').classList.remove('scale-95', 'opacity-0');
                document.getElementById('detailsModalContent').classList.add('scale-100', 'opacity-100');
            }, 10);

            document.getElementById('detailsForm').action = updateUrlBase.replace('REGISTRO_ID', id);

            officialColumns.forEach(colName => {
                const input = document.getElementById(`modal_field_${colName}`);
                if (input) input.value = data[colName] !== undefined ? data[colName] : '';
            });

            const container = document.getElementById('extraFieldsContainer');
            const noExtrasMsg = document.getElementById('noExtrasMsg');
            container.innerHTML = '';
            let hasExtras = false;

            for (const [key, value] of Object.entries(data)) {
                if (!officialColumns.includes(key) && key !== '_row_theme') {
                    addExtraRowHTML(key, value);
                    hasExtras = true;
                }
            }
            noExtrasMsg.style.display = hasExtras ? 'none' : 'block';
        }

        function closeDetailsModal() {
            const content = document.getElementById('detailsModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                document.getElementById('detailsModal').classList.add('hidden');
                document.getElementById('detailsModalOverlay').classList.add('hidden');
            }, 300);
        }

        function addExtraField() {
            document.getElementById('noExtrasMsg').style.display = 'none';
            addExtraRowHTML('', '');
        }

        function addExtraRowHTML(key = '', value = '') {
            const rowId = 'extra_' + Date.now() + Math.random().toString(36).substr(2, 5);
            const html = `
                <div class="flex gap-2 items-center animate-fade-in-up mb-2" id="${rowId}">
                    <input type="text" name="extras_keys[]" value="${key}" placeholder="Clave" class="w-1/3 text-xs px-2 py-1.5 bg-gray-50 border border-gray-200 rounded focus:ring-indigo-500">
                    <input type="text" name="extras_values[]" value="${value}" placeholder="Valor" class="flex-1 text-xs px-2 py-1.5 border border-gray-200 rounded focus:ring-indigo-500">
                    <button type="button" onclick="document.getElementById('${rowId}').remove()" class="text-gray-400 hover:text-red-500"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                </div>`;
            document.getElementById('extraFieldsContainer').insertAdjacentHTML('beforeend', html);
        }

        function openColorPicker(event, type, elementOrId) {
            event.stopPropagation();
            const picker = document.getElementById('colorPicker');
            activeType = type;
            activeTarget = elementOrId;

            // Obtenemos las coordenadas del botón pulsado
            const rect = event.currentTarget.getBoundingClientRect();
            
            // CORRECCIÓN: Al ser 'fixed', NO sumamos window.scrollY ni window.scrollX
            picker.style.top = (rect.bottom + 5) + 'px'; 
            
            // Centramos un poco el popup respecto al botón (ajusta el -80 según veas)
            // Si quieres que se alinee a la derecha del botón usa: rect.right - picker.offsetWidth
            picker.style.left = (rect.left + (rect.width / 2) - 96) + 'px'; // 96 es la mitad del ancho del popup (w-48 = 192px)

            picker.classList.remove('hidden');
            document.addEventListener('click', closeColorPickerOutside);
        }

        function closeColorPickerOutside(event) {
            const picker = document.getElementById('colorPicker');
            if (!picker.contains(event.target)) {
                picker.classList.add('hidden');
                document.removeEventListener('click', closeColorPickerOutside);
            }
        }

        function applyColor(colorClass) {
            if (activeType === 'column') {
                document.getElementById('colColorName').value = activeTarget;
                document.getElementById('colColorClass').value = colorClass;
                document.getElementById('columnColorForm').submit();
            } else if (activeType === 'row') {
                const form = document.getElementById('rowColorForm');
                form.action = updateUrlBase.replace('REGISTRO_ID', activeTarget);
                form.innerHTML = ''; 
                const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}'; form.appendChild(csrf);
                const method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='PUT'; form.appendChild(method);
                
                const colorInput = document.createElement('input'); 
                colorInput.type = 'hidden'; colorInput.name = 'datos[_row_theme]'; colorInput.value = colorClass; 
                form.appendChild(colorInput);

                const currentData = recordsData[activeTarget] || {};
                for (const [key, value] of Object.entries(currentData)) {
                    if (key !== '_row_theme') {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `datos[${key}]`;
                        input.value = value;
                        form.appendChild(input);
                    }
                }
                form.submit();
            }
            document.getElementById('colorPicker').classList.add('hidden');
        }
    </script>
</x-app-layout>