<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MasterProductExport implements FromCollection
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
            'Item Type',
            'Brand/Manufacture',
            'Vendor',
            'SKU',
            'UPC',
            'Case Barcode',
            'Unit Description',
            'Fullname',
            'Pick Name',
            'Unit Weight(lbs)',
            'Length(in)',
            'Width(in)',
            'Height(in)',
            'Item Description',
            'Additional Description',
        ];
    }

    public function collection()
    {
        return collect($this->data);
    }
}
