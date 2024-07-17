<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IceChartTemplateExport implements FromView
{
   protected $iceChartTemplate;
   protected $result;
   protected $iceEditArray;

   function __construct($iceChartTemplate,$result,$iceEditArray){
        $this->iceChartTemplate = $iceChartTemplate;
        $this->result = $result;
        $this->iceEditArray = $iceEditArray;
   }

   public function view(): View{
       return view('cranium.icechart.master_template_pdf_export',['iceChartTemplate' => $this->iceChartTemplate, 'result' => $this->result, 'iceEditArray' => $this->iceEditArray]);
   }
}
