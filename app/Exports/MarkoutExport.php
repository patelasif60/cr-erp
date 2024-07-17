<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MarkoutExport implements FromView
{
   protected $result;
   function __construct($result){
        $this->result = $result;
   }

   public function view(): View{
       return view('cranium.reports.markout_products_export',['result' => $this->result]);
   }
}
