<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'MyDatabase') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,800&display=swap" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                        },
                    }
                }
            }
        </script>
    @endif
</head>
<body class="bg-white text-zinc-900 antialiased font-sans min-h-screen flex flex-col selection:bg-zinc-900 selection:text-white">

    <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
        <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
    </div>

    <header class="absolute inset-x-0 top-0 z-50">
        <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <a href="#" class="-m-1.5 p-1.5 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6 text-zinc-900">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625v3.375m-16.5-3.375v3.375m16.5 3.375v3.375m-16.5-3.375v3.375" />
                    </svg>
                    <span class="font-bold text-lg tracking-tight">MultiDatos</span>
                </a>
            </div>
            
            @auth
                <div class="flex flex-1 justify-end">
                    <a href="{{ url('/dashboard') }}" class="text-sm font-semibold leading-6 text-zinc-900">Dashboard <span aria-hidden="true">&rarr;</span></a>
                </div>
            @endauth
        </nav>
    </header>

    <main class="relative isolate px-6 pt-14 lg:px-8 flex-grow flex flex-col justify-center">
        <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56 text-center">
            
            <div class="hidden sm:mb-8 sm:flex sm:justify-center">
                <div class="relative rounded-full px-3 py-1 text-sm leading-6 text-zinc-600 ring-1 ring-zinc-900/10 hover:ring-zinc-900/20">
                    Gestiona tu información de forma sencilla.
                </div>
            </div>

            <h1 class="text-5xl font-bold tracking-tight text-zinc-900 sm:text-7xl">
                Crea, organiza y gestiona tus datos.
            </h1>
            
            <p class="mt-6 text-lg leading-8 text-zinc-600">
                Una plataforma minimalista para diseñar estructuras de bases de datos, crear tablas personalizadas y almacenar registros al instante.
            </p>

            <div class="mt-10 flex items-center justify-center gap-x-6">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-md bg-zinc-900 px-6 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-zinc-600 transition-all">
                            Ir al Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md bg-zinc-900 px-8 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-zinc-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-zinc-600 transition-all">
                            Iniciar Sesión
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm font-semibold leading-6 text-zinc-900 hover:text-zinc-600 px-4 py-3 transition-all">
                                Registrarse <span aria-hidden="true">→</span>
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </main>
    
    <footer class="text-center py-6 text-sm text-zinc-500">
        <p>&copy; {{ date('Y') }} MultiDatos. Hecho con Laravel. Alejandro Caballero Luque.</p>
    </footer>

</body>
</html>