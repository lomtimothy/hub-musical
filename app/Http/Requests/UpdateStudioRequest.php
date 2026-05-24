<?php

namespace App\Http\Requests;

use App\Models\Studio;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateStudioRequest extends FormRequest
{
    public function authorize(): bool
    {
        $studio = $this->route('studio');

        return $studio instanceof Studio
            && ($this->user()?->can('update', $studio) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $studio = $this->route('studio');

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:120',
                Rule::unique('studios', 'name')->ignore($studio?->id),
            ],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'hourly_rate' => ['required', 'numeric', 'min:1', 'max:999999.99'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', Rule::exists('tags', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del estudio es obligatorio.',
            'name.unique' => 'Ya existe otro estudio con ese nombre.',
            'description.required' => 'La descripción es obligatoria.',
            'description.min' => 'La descripción debe tener al menos :min caracteres.',
            'address.required' => 'La dirección es obligatoria.',
            'hourly_rate.required' => 'La tarifa por hora es obligatoria.',
            'hourly_rate.numeric' => 'La tarifa por hora debe ser un número.',
            'capacity.required' => 'La capacidad es obligatoria.',
            'capacity.integer' => 'La capacidad debe ser un número entero.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $validated = $this->validated();

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $this->boolean('is_active');

        unset($validated['tags']);

        return $validated;
    }

    /**
     * IDs de etiquetas validados para sincronizar la relación polimórfica.
     *
     * @return array<int, int>
     */
    public function validatedTags(): array
    {
        return array_map('intval', $this->validated()['tags'] ?? []);
    }
}
