<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportIT implements FromCollection, WithHeadings
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
			"warehouseproduct.sku",
			"warehouse_id",
			"on_hand_quantity"
        ];
    }
	
    public function collection()
    {
        return collect($this->data);
    }
}
