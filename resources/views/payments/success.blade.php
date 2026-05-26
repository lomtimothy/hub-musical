<x-layouts::app :title="__('Pago confirmado')">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-green-200 bg-green-50 p-8 shadow-sm dark:border-green-900 dark:bg-green-950">
            <p class="text-sm font-medium text-green-700 dark:text-green-300">
                Pago procesado
            </p>

            <h1 class="mt-2 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                Reserva pagada correctamente
            </h1>

            <p class="mt-4 text-zinc-700 dark:text-zinc-300">
                Tu pago para la sesión <strong>{{ $studioSession->title }}</strong> fue registrado correctamente.
            </p>

            <div class="mt-6 rounded-lg bg-white p-4 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Estudio</p>
                <p class="font-medium text-zinc-900 dark:text-white">
                    {{ $studioSession->studio->name }}
                </p>

                <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">Total pagado</p>
                <p class="font-medium text-zinc-900 dark:text-white">
                    ${{ number_format((float) $studioSession->amount_paid, 2) }}
                </p>
            </div>

            <div class="mt-8 flex gap-3">
                <a
                    href="{{ route('studio-sessions.show', $studioSession) }}"
                    class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800"
                >
                    Ver sesión
                </a>

                <a
                    href="{{ route('studios.index') }}"
                    class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                >
                    Ver estudios
                </a>
            </div>
        </div>
    </div>
</x-layouts::app>