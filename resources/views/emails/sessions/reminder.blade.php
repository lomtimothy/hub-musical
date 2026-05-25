<x-mail::message>
# Recordatorio de sesión

Hola {{ $booker->name }},

Te recordamos que mañana tienes una sesión programada en **{{ $studio->name }}**.

<x-mail::panel>
**Sesión:** {{ $studioSession->title }}

**Inicio:** {{ $studioSession->starts_at->format('d/m/Y H:i') }}

**Fin:** {{ $studioSession->ends_at->format('d/m/Y H:i') }}

**Estudio:** {{ $studio->name }}

**Dirección:** {{ $studio->address }}, {{ $studio->city }}, {{ $studio->state }}

**Total estimado:** ${{ number_format((float) $studioSession->total_price, 2) }}
</x-mail::panel>

@if ($studioSession->notes)
**Notas:**

{{ $studioSession->notes }}
@endif

<x-mail::button :url="route('studio-sessions.show', $studioSession)">
Ver detalle de la sesión
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>