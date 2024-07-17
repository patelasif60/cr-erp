<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseOrderExport implements FromCollection, WithHeadings
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
            'Product Number',
            'Product Listing Name',
            'Status',
            'Lead Time',
            'Product Availability',
            'On Hand QTY',
            'On Order QTY',
            'Weeks Worth QTY',
            'Min Order QTY',
            'Suggested Order QTY',
            'Order QTY'
        ];
    }

    public function collection() {
        return collect($this->data);
    }
}