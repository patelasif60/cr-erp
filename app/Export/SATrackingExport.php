<?php

namespace App\Export;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SATrackingExport implements FromView
{
   protected $order_info,$package,$orderSummary;
   function __construct($order_info,$package,$orderSummary){
        $this->order_info = $order_info;
        $this->package = $package;
        $this->orderSummary = $orderSummary;
   }

   public function view(): View{
       return view('cranium.reports.SATrackingExport',['order_info' => $this->order_info,'package' => $this->package,'orderSummary' => $this->orderSummary]);
   }
}
