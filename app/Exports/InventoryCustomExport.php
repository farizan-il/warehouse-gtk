<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryCustomExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $selectedFields;

    public function __construct($data, $selectedFields)
    {
        $this->data = collect($data);
        $this->selectedFields = $selectedFields;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        $headers = [];
        foreach ($this->selectedFields as $field) {
            $headers[] = $field['label'];
        }
        return $headers;
    }

    public function map($item): array
    {
        $mapped = [];
        foreach ($this->selectedFields as $field) {
            $key = $field['key'];
            $value = $item[$key] ?? '-';
            
            // Format Qty if needed (backend already formatted most of it, but just in case)
            if ($key === 'qty') {
                $mapped[] = (float)$value;
            } else {
                $mapped[] = $value;
            }
        }
        return $mapped;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
