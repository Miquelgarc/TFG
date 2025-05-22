<?php

namespace App\Exports;

use App\Models\Link;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Illuminate\Http\Request;

class LinksExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, ShouldQueue
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Link::query();

        if (!empty($this->filters['search'])) {
            $query->where('target_url', 'like', '%' . $this->filters['search'] . '%');
        }

        if (!empty($this->filters['date_start']) && !empty($this->filters['date_end'])) {
            $query->whereBetween('created_at', [$this->filters['date_start'], $this->filters['date_end']]);
        }

        if (!empty($this->filters['order_by']) && !empty($this->filters['order_dir'])) {
            $query->orderBy($this->filters['order_by'], $this->filters['order_dir']);
        }

        return $query->with('affiliate')->get();
    }

    public function headings(): array
    {
        return [
            'Afiliado',
            'URL de destino',
            'URL generada',
            'Clicks',
            'Conversiones',
            'Fecha de creación',
        ];
    }

    public function map($link): array
    {
        return [
            optional($link->affiliate)->name ?? 'N/A',
            $link->target_url,
            $link->generated_url,
            $link->clicks,
            $link->conversions,
            $link->created_at->format('d/m/Y'),
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }
}
