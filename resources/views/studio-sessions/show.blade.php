<x-layouts::app :title="$studioSession->title">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                {{ $studioSession->studio->name }}
            </p>

            <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                {{ $studioSession->title }}
            </h1>

            <div class="mt-6 grid gap-6 md:grid-cols-3">
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Inicio</p>
                    <p class="font-medium text-zinc-900 dark:text-white">
                        {{ $studioSession->starts_at->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Fin</p>
                    <p class="font-medium text-zinc-900 dark:text-white">
                        {{ $studioSession->ends_at->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Estado</p>
                    <p class="font-medium text-zinc-900 dark:text-white">
                        {{ ucfirst($studioSession->status) }}
                    </p>
                </div>
            </div>

            <div class="mt-6 grid gap-6 md:grid-cols-3">
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Reservado por</p>
                    <p class="font-medium text-zinc-900 dark:text-white">
                        {{ $studioSession->booker->name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Productor</p>
                    <p class="font-medium text-zinc-900 dark:text-white">
                        {{ $studioSession->studio->owner->name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total estimado</p>
                    <p class="font-medium text-zinc-900 dark:text-white">
                        ${{ number_format((float) $studioSession->total_price, 2) }}
                    </p>
                </div>
            </div>

            @if ($studioSession->notes)
                <div class="mt-6">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Notas</p>
                    <p class="mt-1 text-zinc-700 dark:text-zinc-300">
                        {{ $studioSession->notes }}
                    </p>
                </div>
            @endif

            <div class="mt-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">
                    Músicos participantes
                </h2>

                <div class="mt-4 space-y-3">
                    @foreach ($studioSession->musicians as $musician)
                        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                            <div class="flex flex-col justify-between gap-2 sm:flex-row">
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-white">
                                        {{ $musician->name }}
                                    </p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $musician->pivot->instrument }}
                                    </p>
                                </div>

                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    Split: {{ number_format((float) $musician->pivot->payment_split, 2) }}%
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                @can('cancel', $studioSession)
                    @if ($studioSession->status !== 'cancelled')
                        <form method="POST" action="{{ route('studio-sessions.destroy', $studioSession) }}" onsubmit="return confirm('¿Seguro que deseas cancelar esta sesión?');">
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-950"
                            >
                                Cancelar sesión
                            </button>
                        </form>
                    @endif
                @endcan

                <a
                    href="{{ route('studios.show', $studioSession->studio) }}"
                    class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                >
                    Volver al estudio
                </a>
            </div>
        </div>
    </div>
</x-layouts::app>