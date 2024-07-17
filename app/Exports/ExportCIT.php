<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportCIT implements FromCollection,WithHeadings
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
			"productdata.sku",
			"channel_name",
			"inclusion",
        ];
    }
	
    public function collection()
    {
        return collect($this->data);
    }
}
