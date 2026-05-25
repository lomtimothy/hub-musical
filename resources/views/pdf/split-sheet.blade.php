<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Split Sheet - {{ $studioSession->title }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #18181b;
            font-size: 12px;
            line-height: 1.5;
        }

        .header {
            border-bottom: 2px solid #18181b;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .brand {
            font-size: 14px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 8px;
        }

        h1 {
            font-size: 26px;
            margin: 0;
        }

        h2 {
            font-size: 16px;
            margin-top: 24px;
            margin-bottom: 8px;
            border-bottom: 1px solid #d4d4d8;
            padding-bottom: 4px;
        }

        .muted {
            color: #71717a;
        }

        .grid {
            width: 100%;
            margin-top: 12px;
        }

        .grid td {
            width: 50%;
            vertical-align: top;
            padding: 6px 8px 6px 0;
        }

        .label {
            font-weight: bold;
            color: #3f3f46;
        }

        table.participants {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        table.participants th,
        table.participants td {
            border: 1px solid #d4d4d8;
            padding: 8px;
            text-align: left;
        }

        table.participants th {
            background-color: #f4f4f5;
            font-weight: bold;
        }

        .summary {
            margin-top: 20px;
            padding: 12px;
            border: 1px solid #d4d4d8;
            background-color: #fafafa;
        }

        .signatures {
            margin-top: 40px;
            width: 100%;
        }

        .signature-box {
            height: 70px;
            border-bottom: 1px solid #18181b;
            margin-bottom: 6px;
        }

        .footer {
            margin-top: 36px;
            font-size: 10px;
            color: #71717a;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $statusLabel = [
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'cancelled' => 'Cancelada',
            'completed' => 'Completada',
        ][$studioSession->status] ?? ucfirst($studioSession->status);

        $totalSplit = $studioSession->musicians->sum(fn ($musician) => (float) $musician->pivot->payment_split);
    @endphp

    <div class="header">
        <div class="brand">{{ config('app.name') }}</div>
        <h1>Split Sheet</h1>
        <p class="muted">
            Documento generado para la sesión musical #{{ $studioSession->id }}.
        </p>
    </div>

    <h2>Información de la sesión</h2>

    <table class="grid">
        <tr>
            <td>
                <span class="label">Sesión:</span><br>
                {{ $studioSession->title }}
            </td>
            <td>
                <span class="label">Estado:</span><br>
                {{ $statusLabel }}
            </td>
        </tr>

        <tr>
            <td>
                <span class="label">Estudio:</span><br>
                {{ $studioSession->studio->name }}
            </td>
            <td>
                <span class="label">Productor:</span><br>
                {{ $studioSession->studio->owner->name }}
            </td>
        </tr>

        <tr>
            <td>
                <span class="label">Reservado por:</span><br>
                {{ $studioSession->booker->name }}
            </td>
            <td>
                <span class="label">Total estimado:</span><br>
                ${{ number_format((float) $studioSession->total_price, 2) }}
            </td>
        </tr>

        <tr>
            <td>
                <span class="label">Inicio:</span><br>
                {{ $studioSession->starts_at->format('d/m/Y H:i') }}
            </td>
            <td>
                <span class="label">Fin:</span><br>
                {{ $studioSession->ends_at->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    @if ($studioSession->notes)
        <h2>Notas</h2>
        <p>{{ $studioSession->notes }}</p>
    @endif

    <h2>Participantes y porcentajes</h2>

    <table class="participants">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Instrumento / Rol</th>
                <th>Split</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($studioSession->musicians as $musician)
                <tr>
                    <td>{{ $musician->name }}</td>
                    <td>{{ $musician->pivot->instrument }}</td>
                    <td>{{ number_format((float) $musician->pivot->payment_split, 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <strong>Total de splits:</strong> {{ number_format($totalSplit, 2) }}%

        @if ($totalSplit !== 100.0)
            <br>
            <span class="muted">
                Nota: el total de splits no suma exactamente 100%. Verificar antes de usar este documento como acuerdo final.
            </span>
        @endif
    </div>

    <h2>Firmas</h2>

    <table class="signatures">
        @foreach ($studioSession->musicians as $musician)
            <tr>
                <td style="width: 50%; padding-right: 20px;">
                    <div class="signature-box"></div>
                    {{ $musician->name }}
                </td>
                <td style="width: 50%;">
                    <div class="signature-box"></div>
                    Fecha
                </td>
            </tr>
        @endforeach
    </table>

    <div class="footer">
        Este documento fue generado automáticamente por {{ config('app.name') }}.
        No sustituye asesoría legal ni contratos profesionales.
    </div>
</body>
</html>