<x-layouts::app :title="__('Publicar estudio')">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                Publicar nuevo estudio
            </h1>
            <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                Completa la información para que los músicos puedan encontrar y reservar tu espacio.
            </p>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <form method="POST" action="{{ route('studios.store') }}">
                @include('studios._form', [
                    'buttonText' => 'Crear estudio',
                    'selectedTags' => [],
                ])
            </form>
        </div>
    </div>
</x-layouts::app>