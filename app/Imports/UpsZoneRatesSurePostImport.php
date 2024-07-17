<?php

namespace App\Imports;

use App\UpsZoneRatesSurePost;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UPSZoneRatesSurePostImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
        UpsZoneRatesSurePost::create([
            'zone2' => $row['2'],
            'zone3' => $row['3'],
            'zone4' => $row['4'],
            'zone5' => $row['5'],
            'zone6' => $row['6'],
            'zone7' => $row['7'],
            'zone8' => $row['8'],
            'zone44' => $row['44'],
            'zone45' => $row['45'],
            'zone46' => $row['46'],
        ]);
    }
}
