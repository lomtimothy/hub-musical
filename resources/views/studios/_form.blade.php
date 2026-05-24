@csrf

<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            Nombre del estudio
        </label>

        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $studio->name ?? '') }}"
            required
            minlength="3"
            maxlength="120"
            class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
        >

        @error('name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            Descripción
        </label>

        <textarea
            id="description"
            name="description"
            required
            minlength="20"
            maxlength="2000"
            rows="5"
            class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
        >{{ old('description', $studio->description ?? '') }}</textarea>

        @error('description')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            Dirección
        </label>

        <input
            id="address"
            name="address"
            type="text"
            value="{{ old('address', $studio->address ?? '') }}"
            required
            minlength="5"
            maxlength="255"
            class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
        >

        @error('address')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="city" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Ciudad
            </label>

            <input
                id="city"
                name="city"
                type="text"
                value="{{ old('city', $studio->city ?? 'Guadalajara') }}"
                required
                maxlength="100"
                class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
            >

            @error('city')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="state" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Estado
            </label>

            <input
                id="state"
                name="state"
                type="text"
                value="{{ old('state', $studio->state ?? 'Jalisco') }}"
                required
                maxlength="100"
                class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
            >

            @error('state')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="hourly_rate" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Tarifa por hora
            </label>

            <input
                id="hourly_rate"
                name="hourly_rate"
                type="number"
                value="{{ old('hourly_rate', $studio->hourly_rate ?? '') }}"
                required
                min="1"
                max="999999.99"
                step="0.01"
                class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
            >

            @error('hourly_rate')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="capacity" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Capacidad
            </label>

            <input
                id="capacity"
                name="capacity"
                type="number"
                value="{{ old('capacity', $studio->capacity ?? '') }}"
                required
                min="1"
                max="100"
                class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
            >

            @error('capacity')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <p class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            Etiquetas
        </p>

        <div class="mt-3 grid gap-3 sm:grid-cols-2 md:grid-cols-3">
            @foreach ($tags as $tag)
                <label class="flex items-center gap-2 rounded-lg border border-zinc-200 p-3 text-sm dark:border-zinc-700">
                    <input
                        type="checkbox"
                        name="tags[]"
                        value="{{ $tag->id }}"
                        @checked(in_array($tag->id, old('tags', $selectedTags ?? [])))
                        class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500"
                    >
                    <span>{{ $tag->name }}</span>
                </label>
            @endforeach
        </div>

        @error('tags')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror

        @error('tags.*')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="flex items-center gap-3">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old('is_active', $studio->is_active ?? true))
                class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500"
            >
            <span class="text-sm text-zinc-700 dark:text-zinc-300">
                Publicar estudio como activo
            </span>
        </label>

        @error('is_active')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center justify-end gap-3">
        <a
            href="{{ route('studios.index') }}"
            class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
        >
            Cancelar
        </a>

        <button
            type="submit"
            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
        >
            {{ $buttonText ?? 'Guardar' }}
        </button>
    </div>
</div>