<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HelpReportExport implements FromCollection, WithHeadings
{
    private $data;
    // WithHeadings
    public function __construct($data)
    {
        $this->data = collect($data)->map(function ($d, $key) {
            return [
               'name' => $d['name'], 
               'type' => $d['type'],
               'level' => $d['urgent_level'],
               'desc' => $d['desc'],
               'date' => $d['date']
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Username',
            'Type',
            'Level',
            'Description',
            'Date'
        ];
    }

    public function collection() {
        return collect($this->data);
    }
}