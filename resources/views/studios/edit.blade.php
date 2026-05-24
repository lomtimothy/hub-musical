<x-layouts::app :title="__('Editar estudio')">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                Editar estudio
            </h1>
            <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                Actualiza la información pública de {{ $studio->name }}.
            </p>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <form method="POST" action="{{ route('studios.update', $studio) }}">
                @method('PUT')

                @include('studios._form', [
                    'buttonText' => 'Guardar cambios',
                    'selectedTags' => $selectedTags,
                ])
            </form>
        </div>
    </div>
</x-layouts::app>