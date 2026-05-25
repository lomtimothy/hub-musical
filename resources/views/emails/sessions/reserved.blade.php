<x-mail::message>
# Nueva reserva de sesión

Hola {{ $studio->owner->name }},

{{ $booker->name }} ha reservado una sesión en tu estudio **{{ $studio->name }}**.

@php
    $statusLabel = [
        'pending' => 'Pendiente',
        'confirmed' => 'Confirmada',
        'cancelled' => 'Cancelada',
        'completed' => 'Completada',
    ][$studioSession->status] ?? ucfirst($studioSession->status);
@endphp

<x-mail::panel>
**Sesión:** {{ $studioSession->title }}

**Inicio:** {{ $studioSession->starts_at->format('d/m/Y H:i') }}

**Fin:** {{ $studioSession->ends_at->format('d/m/Y H:i') }}

**Total estimado:** ${{ number_format((float) $studioSession->total_price, 2) }}

**Estado:** {{ $statusLabel }}
</x-mail::panel>

@if ($studioSession->notes)
**Notas del músico:**

{{ $studioSession->notes }}
@endif

<x-mail::button :url="route('studio-sessions.show', $studioSession)">
Ver detalle de la sesión
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>