<?php

 namespace App\Exports;

 use App\User;
 use Maatwebsite\Excel\Concerns\FromCollection;
 use Maatwebsite\Excel\Concerns\WithHeadings;

 class TotalOrderShipped implements FromCollection, WithHeadings
 {
     private $data;

     public function __construct($data)
     {
         $this->data = $data;
     }

     public function headings(): array
     {
         return [
             'Order Date',
             'E-Tailer Order Number',
             'Client',
             'Order Source',
             'Destination',
             'Channel Delivery Date',
             'Ship By',
             'Service Type',
             'Picker',
             'Order Status'
         ];
     }

     public function collection() {
         return collect($this->data);
     }
 }