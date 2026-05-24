<?php

namespace App\Http\Requests;

use App\Models\Studio;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreStudioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Studio::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:120', 'unique:studios,name'],
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
            'name.unique' => 'Ya existe un estudio con ese nombre.',
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
