<x-layouts::public :title="$studio->name">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-start">
                <div>
                    <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                        {{ $studio->city }}, {{ $studio->state }}
                    </p>

                    <h1 class="mt-2 text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        {{ $studio->name }}
                    </h1>

                    <p class="mt-4 max-w-3xl text-zinc-600 dark:text-zinc-400">
                        {{ $studio->description }}
                    </p>
                </div>

                <div class="rounded-2xl bg-zinc-50 p-5 text-center dark:bg-zinc-800">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Tarifa
                    </p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">
                        ${{ number_format((float) $studio->hourly_rate, 2) }}
                    </p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        por hora
                    </p>
                </div>
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Dirección</p>
                    <p class="mt-1 text-zinc-900 dark:text-white">{{ $studio->address }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Capacidad</p>
                    <p class="mt-1 text-zinc-900 dark:text-white">{{ $studio->capacity }} personas</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Productor</p>
                    <p class="mt-1 text-zinc-900 dark:text-white">{{ $studio->owner->name }}</p>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-2">
                @foreach ($studio->tags as $tag)
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                        {{ $tag->name }}
                    </span>
                @endforeach
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                @auth
                    @can('create', [App\Models\StudioSession::class, $studio])
                        <a
                            href="{{ route('studios.sessions.create', $studio) }}"
                            class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700"
                        >
                            Reservar sesión
                        </a>
                    @endcan
                @endauth

                @auth
                    @can('update', $studio)
                        <a
                            href="{{ route('studios.edit', $studio) }}"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                        >
                            Editar estudio
                        </a>
                    @endcan

                    @can('delete', $studio)
                        <form method="POST" action="{{ route('studios.destroy', $studio) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este estudio?');">
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-950"
                            >
                                Eliminar estudio
                            </button>
                        </form>
                    @endcan
                @endauth

                <a
                    href="{{ route('studios.index') }}"
                    class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                >
                    Volver al catálogo
                </a>
            </div>
        </div>

        <div class="mt-8 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">
                Próximas sesiones
            </h2>

            <div class="mt-4 space-y-4">
                @forelse ($studio->studioSessions as $session)
                    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">
                                    {{ $session->title }}
                                </p>

                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    Reservado por {{ $session->booker->name }}
                                </p>

                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $session->starts_at->format('d/m/Y H:i') }}
                                    —
                                    {{ $session->ends_at->format('H:i') }}
                                </p>

                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    Estado: {{ ucfirst($session->status) }}
                                </p>
                            </div>

                            @auth
                                @can('view', $session)
                                    <a
                                        href="{{ route('studio-sessions.show', $session) }}"
                                        class="inline-flex items-center justify-center rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                    >
                                        Ver sesión
                                    </a>
                                @endcan
                            @endauth
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-600 dark:text-zinc-400">
                        Este estudio todavía no tiene sesiones registradas.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts::public>