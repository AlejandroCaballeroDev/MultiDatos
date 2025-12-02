<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Bases de Datos') }}
            </h2>
            <span class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">
                {{ $basesDatos->count() }} Proyectos
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <a href="{{ route('basesdatos.create') }}" class="group relative flex flex-col items-center justify-center h-64 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-900 dark:focus:ring-zinc-600">
                    <div class="w-14 h-14 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center group-hover:bg-white dark:group-hover:bg-gray-600 shadow-sm transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <span class="mt-4 font-semibold text-gray-500 dark:text-gray-400 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">
                        Crear nueva base de datos
                    </span>
                    <span class="mt-1 text-xs text-gray-400">Haz clic para configurar</span>
                </a>

                @foreach ($basesDatos as $bd)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700 transition-all duration-300 flex flex-col h-64 relative group">
                        
                        <a href="{{ route('basesdatos.show', $bd) }}" class="absolute inset-0 z-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500">
                            <span class="sr-only">Ver {{ $bd->nombre }}</span>
                        </a>

                        <div class="p-6 flex-1 relative pointer-events-none"> <div class="flex justify-between items-start mb-4">
                                @php
                                    $colors = [
                                        'text-indigo-600 bg-indigo-50 dark:text-indigo-400 dark:bg-indigo-900/20',
                                        'text-emerald-600 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-900/20',
                                        'text-purple-600 bg-purple-50 dark:text-purple-400 dark:bg-purple-900/20',
                                        'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20',
                                        'text-rose-600 bg-rose-50 dark:text-rose-400 dark:bg-rose-900/20',
                                    ];
                                    $theme = $colors[$bd->id % count($colors)];
                                @endphp
                                
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $theme }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625v3.375m-16.5-3.375v3.375m16.5 3.375v3.375m-16.5-3.375v3.375" />
                                    </svg>
                                </div>

                                <div class="relative z-10 pointer-events-auto">
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                                </svg>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">
                                            <form method="POST" action="{{ route('basesdatos.destroy', $bd) }}" onsubmit="return confirm('¿Estás seguro? Se borrarán todos los registros de esta base de datos.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                    Eliminar Base de Datos
                                                </button>
                                            </form>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1 group-hover:text-zinc-700 dark:group-hover:text-zinc-300 transition-colors">
                                {{ $bd->nombre }}
                            </h3>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-2 font-mono">
                                /{{ $bd->slug }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                Creado {{ $bd->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 rounded-b-xl flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 relative pointer-events-none">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                </svg>
                                <span>{{ $bd->registros_count }} Registros</span>
                            </div>
                            <span>{{ count($bd->configuracion_tabla) }} Campos</span>
                        </div>
                    </div>
                @endforeach

            </div>

            @if($basesDatos->isEmpty())
                <div class="text-center mt-12">
                    <p class="text-gray-500 dark:text-gray-400">Aún no has creado ninguna base de datos.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>