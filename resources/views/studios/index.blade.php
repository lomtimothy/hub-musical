<x-layouts::public :title="__('Estudios')">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    Estudios Disponibles
                </h1>
                <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                    Encuentra espacios profesionales para grabar, mezclar o producir tu siguiente proyecto musical.
                </p>
            </div>

            @auth
                @can('create', App\Models\Studio::class)
                    <a
                        href="{{ route('studios.create') }}"
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                    >
                        Publicar estudio
                    </a>
                @endcan
            @endauth
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($studios as $studio)
                <article class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">
                                <a href="{{ route('studios.show', $studio) }}" class="hover:underline">
                                    {{ $studio->name }}
                                </a>
                            </h2>

                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $studio->city }}, {{ $studio->state }}
                            </p>
                        </div>

                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                            ${{ number_format((float) $studio->hourly_rate, 2) }}/hr
                        </span>
                    </div>

                    <p class="mt-4 line-clamp-3 text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $studio->description }}
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($studio->tags as $tag)
                            <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>

                    <div class="mt-6 flex items-center justify-between text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400">
                            Por {{ $studio->owner->name }}
                        </span>

                        <a href="{{ route('studios.show', $studio) }}" class="font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                            Ver detalle
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        No hay estudios publicados todavía.
                    </h2>
                    <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                        Cuando un productor publique un estudio, aparecerá aquí.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $studios->links() }}
        </div>
    </div>
</x-layouts::public>