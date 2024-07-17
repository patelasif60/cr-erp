<?php

namespace App\Imports;


use App\UpsZipZoneByWH;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UpsZipZoneByWHImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
        UpsZipZoneByWH::create([
            'state' => $row['state'] ?? '',
            'zip_3' => $row['zip_3'] ?? '',
            'zone_wi' => $row['zone_wi'] ?? '',
            'transit_days_wi' => $row['transit_days_wi'] ?? '',
            'zone_pa' => $row['zone_pa'] ?? '',
            'transit_days_pa' => $row['transit_days_pa'] ?? '',
            'zone_nv' => $row['zone_nv'] ?? '',
            'transit_days_nv' => $row['transit_days_nv'] ?? '',
            'zone_ok' => $row['zone_ok'] ?? '',
            'transit_days_ok' => $row['transit_days_ok'] ?? '',
        ]);
    }
}
