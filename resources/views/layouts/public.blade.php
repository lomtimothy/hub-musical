@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-white">
        <header class="border-b border-zinc-200 bg-white/80 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/80">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('studios.index') }}" class="text-lg font-bold text-zinc-900 dark:text-white">
                    Hub Musical
                </a>

                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('studios.index') }}"
                        class="text-sm font-medium text-zinc-700 hover:text-indigo-600 dark:text-zinc-300 dark:hover:text-indigo-400"
                    >
                        Estudios
                    </a>

                    @auth
                        <a
                            href="{{ url('/settings/profile') }}"
                            class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        >
                            Configuraciones
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-950"
                            >
                                Cerrar sesión
                            </button>
                        </form>
                    @endauth

                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        >
                            Iniciar sesión
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                        >
                            Registrarse
                        </a>
                    @endauth
                </div>
            </nav>
        </header>

        <main>
            {{ $slot }}
        </main>
    </body>
</html>