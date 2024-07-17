<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MasterProductExcelExport implements FromView
{
   protected $product_data;
   protected $filter_val;
   protected $visible_columns;

   function __construct($product_data,$filter_val,$visible_columns,$mastrpro){
        $this->product_data = $product_data;
        $this->filter_val = $filter_val;
        $this->visible_columns = $visible_columns;
        $this->mastrpro = $mastrpro;
   }

   public function view(): View{
       return view('cranium.exports.master_product_excel_export',['product_data' => $this->product_data, 'filter_val' => $this->filter_val, 'visible_columns' => $this->visible_columns,'mastrpro' => $this->mastrpro]);
   }
}
