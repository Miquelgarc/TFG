<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura Comisión #{{ $commission->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .header, .footer { text-align: center; }
        .info, .details { margin-top: 20px; }
        .details table { width: 100%; border-collapse: collapse; }
        .details th, .details td { padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Factura de Comisión</h2>
        <p>Comisión ID: {{ $commission->id }}</p>
    </div>

    <div class="info">
        <p><strong>Afiliado:</strong> {{ $affiliate->name }}</p>
        <p><strong>Email:</strong> {{ $affiliate->email }}</p>
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($commission->generated_at)->format('d/m/Y') }}</p>
    </div>

    <div class="details">
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Reserva</th>
                    <th>Propiedad</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $commission->description }}</td>
                    <td>#{{ $commission->reservation_id ?? 'N/A' }}</td>
                    <td>{{ $reservation?->property?->title ?? 'N/A' }}</td>
                    <td>€{{ number_format($commission->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Gracias por formar parte de nuestro programa de afiliados.</p>
    </div>
</body>
</html>
