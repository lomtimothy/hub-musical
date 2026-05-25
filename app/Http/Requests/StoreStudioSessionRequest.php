<?php

namespace App\Http\Requests;

use App\Models\Studio;
use App\Models\StudioSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreStudioSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $studio = $this->route('studio');

        return $studio instanceof Studio
            && ($this->user()?->can('create', [StudioSession::class, $studio]) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'instrument' => ['required', 'string', 'min:2', 'max:80'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $studio = $this->route('studio');

                if (! $studio instanceof Studio) {
                    return;
                }

                if (! $this->filled(['starts_at', 'ends_at'])) {
                    return;
                }

                $hasConflict = StudioSession::query()
                    ->where('studio_id', $studio->id)
                    ->where(function ($query): void {
                        $query
                            ->where('status', 'pending')
                            ->orWhere('status', 'confirmed');
                    })
                    ->where('starts_at', '<', $this->date('ends_at'))
                    ->where('ends_at', '>', $this->date('starts_at'))
                    ->exists();

                if ($hasConflict) {
                    $validator->errors()->add(
                        'starts_at',
                        'El estudio ya tiene una sesión reservada en ese horario.'
                    );
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título de la sesión es obligatorio.',
            'title.min' => 'El título debe tener al menos :min caracteres.',
            'starts_at.required' => 'La fecha y hora de inicio son obligatorias.',
            'starts_at.after' => 'La sesión debe iniciar en una fecha futura.',
            'ends_at.required' => 'La fecha y hora de finalización son obligatorias.',
            'ends_at.after' => 'La hora de finalización debe ser posterior al inicio.',
            'instrument.required' => 'El instrumento o rol musical es obligatorio.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function sessionData(float $hourlyRate): array
    {
        $validated = $this->validated();

        $startsAt = $this->date('starts_at');
        $endsAt = $this->date('ends_at');

        $hours = $startsAt->floatDiffInHours($endsAt);

        return [
            'title' => $validated['title'],
            'notes' => $validated['notes'] ?? null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'pending',
            'total_price' => round($hourlyRate * $hours, 2),
        ];
    }
}
