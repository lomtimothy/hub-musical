<x-layouts::app :title="__('Reservar sesión')">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                {{ $studio->name }}
            </p>

            <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                Reservar sesión
            </h1>

            <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                Selecciona fecha, horario y tu rol musical para reservar este estudio.
            </p>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <form method="POST" action="{{ route('studios.sessions.store', $studio) }}">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Título de la sesión
                        </label>

                        <input
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title') }}"
                            required
                            minlength="3"
                            maxlength="120"
                            class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                        >

                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="instrument" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Instrumento o rol musical
                        </label>

                        <input
                            id="instrument"
                            name="instrument"
                            type="text"
                            value="{{ old('instrument') }}"
                            required
                            minlength="2"
                            maxlength="80"
                            placeholder="Ej. Voz, Guitarra, Producción, Mezcla"
                            class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                        >

                        @error('instrument')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_split" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Tu porcentaje de split
                        </label>

                        <input
                            id="payment_split"
                            name="payment_split"
                            type="number"
                            min="0"
                            max="100"
                            step="0.01"
                            value="{{ old('payment_split', 100) }}"
                            required
                            class="mt-2 block w-full rounded-lg border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                        >

                        @error('payment_split')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-800">
                        <div>
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                                Músicos adicionales
                            </h2>

                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                Puedes agregar hasta 10 músicos más. La suma de splits debe ser 100%.
                            </p>
                        </div>

                        @error('participants')
                            <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="mt-4 space-y-4">
                            @foreach (range(0, 9) as $index)
                                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                                    <p class="mb-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                        Músico adicional {{ $index + 1 }}
                                    </p>

                                    <div class="grid gap-4 md:grid-cols-3">
                                        <div>
                                            <label for="participants_{{ $index }}_user_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                                Músico
                                            </label>

                                            <select
                                                id="participants_{{ $index }}_user_id"
                                                name="participants[{{ $index }}][user_id]"
                                                class="mt-2 block w-full rounded-lg border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                                            >
                                                <option value="">Sin músico</option>

                                                @foreach ($musicians as $musician)
                                                    <option
                                                        value="{{ $musician->id }}"
                                                        @selected(old("participants.{$index}.user_id") == $musician->id)
                                                    >
                                                        {{ $musician->name }} — {{ $musician->email }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error("participants.{$index}.user_id")
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="participants_{{ $index }}_instrument" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                                Instrumento o rol
                                            </label>

                                            <input
                                                id="participants_{{ $index }}_instrument"
                                                name="participants[{{ $index }}][instrument]"
                                                type="text"
                                                value="{{ old("participants.{$index}.instrument") }}"
                                                placeholder="Ej. Bajo, Batería, Producción"
                                                class="mt-2 block w-full rounded-lg border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                                            >

                                            @error("participants.{$index}.instrument")
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="participants_{{ $index }}_payment_split" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                                Split
                                            </label>

                                            <input
                                                id="participants_{{ $index }}_payment_split"
                                                name="participants[{{ $index }}][payment_split]"
                                                type="number"
                                                min="0"
                                                max="100"
                                                step="0.01"
                                                value="{{ old("participants.{$index}.payment_split") }}"
                                                placeholder="Ej. 25"
                                                class="mt-2 block w-full rounded-lg border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                                            >

                                            @error("participants.{$index}.payment_split")
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="starts_at" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Inicio
                            </label>

                            <input
                                id="starts_at"
                                name="starts_at"
                                type="datetime-local"
                                value="{{ old('starts_at') }}"
                                required
                                min="{{ now()->format('Y-m-d\TH:i') }}"
                                class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                            >

                            @error('starts_at')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="ends_at" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Fin
                            </label>

                            <input
                                id="ends_at"
                                name="ends_at"
                                type="datetime-local"
                                value="{{ old('ends_at') }}"
                                required
                                min="{{ now()->format('Y-m-d\TH:i') }}"
                                class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                            >

                            @error('ends_at')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Notas adicionales
                        </label>

                        <textarea
                            id="notes"
                            name="notes"
                            rows="4"
                            maxlength="2000"
                            class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                        >{{ old('notes') }}</textarea>

                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-lg bg-zinc-50 p-4 text-sm text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                        Tarifa del estudio:
                        <strong>${{ number_format((float) $studio->hourly_rate, 2) }} por hora</strong>.
                        El total se calculará automáticamente según la duración.
                    </div>

                    <div class="flex justify-end gap-3">
                        <a
                            href="{{ route('studios.show', $studio) }}"
                            class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                        >
                            Reservar sesión
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>