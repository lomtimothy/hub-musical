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
        $studio = $this->route('studio');

        $maxAdditionalParticipants = $studio instanceof Studio
            ? max($studio->capacity - 1, 0)
            : 0;

        return [
            'title' => ['required', 'string', 'max:255'],
            'instrument' => ['required', 'string', 'max:100'],
            'payment_split' => ['required', 'numeric', 'min:0', 'max:100'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'notes' => ['nullable', 'string', 'max:2000'],

            'participants' => ['nullable', 'array', 'max:'.$maxAdditionalParticipants],
            'participants.*.user_id' => ['nullable', 'integer', 'exists:users,id', 'distinct'],
            'participants.*.instrument' => ['nullable', 'string', 'max:100'],
            'participants.*.payment_split' => ['nullable', 'numeric', 'min:0', 'max:100'],
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

                $participants = collect($this->input('participants', []))
                    ->filter(fn ($participant) => filled($participant['user_id'] ?? null));

                $participantUserIds = $participants
                    ->pluck('user_id')
                    ->map(fn ($userId) => (int) $userId);

                $currentUserId = (int) $this->user()?->id;

                if ($this->filled(['starts_at', 'ends_at'])) {
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
                }

                $totalParticipants = 1 + $participants->count();

                if ($totalParticipants > $studio->capacity) {
                    $validator->errors()->add(
                        'participants',
                        "Este estudio solo permite {$studio->capacity} personas. Actualmente intentas reservar para {$totalParticipants} personas."
                    );
                }

                if ($participantUserIds->contains($currentUserId)) {
                    $validator->errors()->add(
                        'participants',
                        'No puedes agregarte como músico adicional porque ya eres quien reserva la sesión.'
                    );
                }

                if ($participantUserIds->duplicates()->isNotEmpty()) {
                    $validator->errors()->add(
                        'participants',
                        'No puedes seleccionar el mismo músico adicional más de una vez.'
                    );
                }

                if ($this->filled(['starts_at', 'ends_at'])) {
                    $musicianUserIds = $participantUserIds
                        ->merge([$currentUserId])
                        ->unique()
                        ->values();

                    $musicianScheduleConflict = StudioSession::query()
                        ->where(function ($query): void {
                            $query
                                ->where('status', 'pending')
                                ->orWhere('status', 'confirmed');
                        })
                        ->where('starts_at', '<', $this->date('ends_at'))
                        ->where('ends_at', '>', $this->date('starts_at'))
                        ->where(function ($query) use ($musicianUserIds): void {
                            $query
                                ->whereIn('booked_by', $musicianUserIds)
                                ->orWhereHas('musicians', function ($query) use ($musicianUserIds): void {
                                    $query->whereIn('users.id', $musicianUserIds);
                                });
                        })
                        ->exists();

                    if ($musicianScheduleConflict) {
                        $validator->errors()->add(
                            'participants',
                            'Uno o más músicos ya tienen otra sesión reservada en ese horario.'
                        );
                    }
                }

                foreach ($participants as $index => $participant) {
                    if (blank($participant['instrument'] ?? null)) {
                        $validator->errors()->add(
                            "participants.{$index}.instrument",
                            'El instrumento o rol del músico adicional es obligatorio.'
                        );
                    }

                    if (blank($participant['payment_split'] ?? null)) {
                        $validator->errors()->add(
                            "participants.{$index}.payment_split",
                            'El split del músico adicional es obligatorio.'
                        );
                    }
                }

                $totalSplit = (float) $this->input('payment_split', 0)
                    + $participants->sum(fn ($participant) => (float) ($participant['payment_split'] ?? 0));

                if (abs($totalSplit - 100) > 0.01) {
                    $validator->errors()->add(
                        'payment_split',
                        'La suma de los splits debe ser exactamente 100%. Actualmente suma '.$totalSplit.'%.'
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
            'instrument.required' => 'Tu instrumento o rol musical es obligatorio.',
            'payment_split.required' => 'Tu porcentaje de split es obligatorio.',
            'payment_split.numeric' => 'Tu porcentaje de split debe ser numérico.',
            'payment_split.min' => 'Tu porcentaje de split no puede ser menor a 0.',
            'payment_split.max' => 'Tu porcentaje de split no puede ser mayor a 100.',
            'starts_at.required' => 'La fecha y hora de inicio es obligatoria.',
            'starts_at.after' => 'La sesión debe iniciar en una fecha futura.',
            'ends_at.required' => 'La fecha y hora de fin es obligatoria.',
            'ends_at.after' => 'La sesión debe terminar después de la hora de inicio.',
            'participants.max' => 'No puedes agregar más músicos de los que permite la capacidad del estudio.',
            'participants.*.user_id.exists' => 'Uno de los músicos seleccionados no existe.',
            'participants.*.user_id.distinct' => 'No puedes seleccionar el mismo músico más de una vez.',
            'participants.*.instrument.max' => 'El instrumento o rol del músico adicional no debe superar 100 caracteres.',
            'participants.*.payment_split.numeric' => 'El split del músico adicional debe ser numérico.',
            'participants.*.payment_split.min' => 'El split del músico adicional no puede ser menor a 0.',
            'participants.*.payment_split.max' => 'El split del músico adicional no puede ser mayor a 100.',
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
