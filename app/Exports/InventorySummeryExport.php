<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventorySummeryExport implements FromCollection,WithHeadings
{
    private $data;
    // WithHeadings
    public function __construct($data)
    {
        $this->data = $data;     //Inject data
    }

    public function headings(): array
    {
        return [
            'ETIN',
            'parent_ETIN',
            'wi_qty',
            'wi_each_qty',
            'wi_orderable_qty',
            'wi_fulfilled_qty',
            'wi_open_order_qty',
            'pa_qty',
            'pa_each_qty',
            'pa_orderable_qty',
            'pa_fulfilled_qty',
            'pa_open_order_qty',
            'nv_qty',
            'nv_each_qty',
            'nv_orderable_qty',
            'nv_fulfilled_qty',
            'nv_open_order_qty',
            'okc_qty',
            'okc_each_qty',
            'okc_orderable_qty',
            'okc_fulfilled_qty',
            'okc_open_order_qty'
        ];
    }

    public function collection()
    {
        return collect($this->data);
    }
}
