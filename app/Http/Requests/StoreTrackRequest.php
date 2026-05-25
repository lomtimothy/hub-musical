<?php

namespace App\Http\Requests;

use App\Models\StudioSession;
use App\Models\Track;
use Illuminate\Foundation\Http\FormRequest;

class StoreTrackRequest extends FormRequest
{
    public function authorize(): bool
    {
        $studioSession = $this->route('studioSession');

        return $studioSession instanceof StudioSession
            && ($this->user()?->can('create', [Track::class, $studioSession]) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tracks' => ['required', 'array', 'min:1', 'max:5'],
            'tracks.*' => ['required', 'file', 'mimes:wav,mp3', 'max:51200'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tracks.required' => 'Debes seleccionar al menos un archivo de audio.',
            'tracks.array' => 'La carga de archivos no tiene el formato correcto.',
            'tracks.max' => 'Solo puedes subir hasta :max archivos a la vez.',
            'tracks.*.required' => 'Cada archivo es obligatorio.',
            'tracks.*.file' => 'Cada elemento debe ser un archivo válido.',
            'tracks.*.mimes' => 'Los archivos deben estar en formato WAV o MP3.',
            'tracks.*.max' => 'Cada archivo no debe pesar más de 50 MB.',
        ];
    }
}
