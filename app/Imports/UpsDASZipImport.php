<?php

namespace App\Imports;


use App\UpsDASZip;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UpsDASZipImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
        UpsDASZip::create([
            'das_zip' => $row['das_zip'] ?? '',
            'das_ext_zip' => $row['das_ext_zip'] ?? ''
        ]);
    }
}
