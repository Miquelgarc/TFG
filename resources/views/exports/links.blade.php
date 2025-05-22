<table>
    <thead>
        <tr>
            <th>Afiliado</th>
            <th>URL Original</th>
            <th>URL Generada</th>
            <th>Clicks</th>
            <th>Conversiones</th>
            <th>Fecha de Creación</th>
        </tr>
    </thead>
    <tbody>
        @foreach($links as $link)
            <tr>
                <td>{{ $link->affiliate->name ?? '—' }}</td>
                <td>{{ $link->target_url }}</td>
                <td>{{ $link->generated_url }}</td>
                <td>{{ $link->clicks }}</td>
                <td>{{ $link->conversions }}</td>
                <td>{{ $link->created_at->format('d/m/Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
