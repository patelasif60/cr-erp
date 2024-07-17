<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class ReportExport implements FromView, WithEvents
{
   protected $result;
   function __construct($result,$request,$type){
        $this->result = $result;
        $this->request = $request;
        $this->type = $type;
   }

   public function view(): View{
     if($this->type == 'product_report')
     {
          ini_set('max_execution_time', 500);
          if(count($this->result) > 0)
          {
               $fieldname = ["ETIN","parent_ETIN","lobs","product_listing_name","full_product_desc","about_this_item","manufacturer","brand","flavor", "product_type","unit_size", "unit_description", "pack_form_count", "unit_in_pack", "item_form_description", "total_ounces", "product_category", "product_subcategory1", "product_subcategory2", "product_subcategory3", "key_product_attributes_diet", "product_tags", "MFG_shelf_life", "hazardous_materials", "storage", "ingredients", "allergens", "prop_65_flag", "prop_65_ingredient", "product_temperature", "supplier_product_number", "manufacture_product_number", "upc", "gtin", "asin", "GPC_code", "GPC_class", "HS_code", "weight", "dimensional_weight", "length", "width", "height", "country_of_origin", "package_information", "cost", "acquisition_cost", "new_cost", "new_cost_date", "status", "etailer_availability", "dropship_available", "channel_listing_restrictions", "POG_flag", "consignment", "warehouses_assigned", "status_date", "current_supplier", "supplier_status", "supplier_description", "created_at", "updated_at", "is_approve", "approved_date", "is_edit", "cancel_reason", "product_listing_ETIN", "alternate_ETINs", "product_edit_request", "queue_status", "updated_by", "inserted_by", "product_subcategory4", "product_subcategory5", "product_subcategory6", "product_subcategory7", "product_subcategory8", "product_subcategory9", "week_worth_qty", "min_order_qty", "chanel_ids", "lead_time", "supplier_type", "upc_scanable", "gtin_scanable", "unit_upc_scanable", "unit_gtin_scanable", "is_bl", "is_wl", "client_supplier_id"];
               return view('cranium.reports.export.product_report_export',['result' => $this->result,'fieldname'=>$fieldname,'request'=>$this->request]);
          }
          return view('cranium.reports.export.product_report_export',['result' => $this->result,'fieldname'=>false,'request'=>$this->request]);
     }
     else if($this->type == 'inventory_receiving'){
          if($this->request->report_type == 'restoke')
          {
               return view('cranium.reports.export.inventory_restoke_report_export',['result' => $this->result,'request'=>$this->request]);     
          }
          if($this->request->report_type == 'transfer')
          {
               return view('cranium.reports.export.inventory_transfer_report_export',['result' => $this->result,'request'=>$this->request]);     
          }
          if($this->request->report_type == 'inventory_adjustment')
          {
               return view('cranium.reports.export.inventory_adjustment_report_export',['result' => $this->result,'request'=>$this->request]);     
          }
          if($this->request->report_type == 'inventory')
          {
               return view('cranium.reports.export.inventory_report_export',['result' => $this->result,'request'=>$this->request]);     
          }
          if($this->request->report_type == 'own_inventory')
          {
               return view('cranium.reports.export.own_inventory_report_export',['result' => $this->result,'request'=>$this->request]);
          }
          if($this->request->report_type == 'perpetual'){
               return view('cranium.reports.export.perpectual_inventory_report_export',['result' => $this->result,'request'=>$this->request]);
          }
          return view('cranium.reports.export.inventory_receiving_report_export',['result' => $this->result,'request'=>$this->request]);
     }
     else if($this->type == 'order_report'){
          if($this->request->report_type == 'open_order'   || $this->request->report_type == 'all_order' || $this->request->report_type == 'unfulfill_order')
          {
               return view('cranium.reports.export.open_order_report_export',['result' => $this->result,'request'=>$this->request]);
          }
          if($this->request->report_type == 'shipped_line_order' || $this->request->report_type == 'shipped_order'){
			return view('cranium.reports.export.shipped_line_order_report_export',['result' => $this->result,'request'=>$this->request]);
		}
          if($this->request->report_type == 'own_order'){
               return view('cranium.reports.export.own_order_report_export',['result' => $this->result,'request'=>$this->request]);
          }
     }
     else if($this->type == 'billing_report'){
          if($this->request->report_type == 'billing_shipped_order'){
			return view('cranium.reports.export.billing_order_report_export',['result' => $this->result,'request'=>$this->request]);
		}
     }  
   }
   public function registerEvents(): array
   {
     return [
          BeforeSheet::class => function (BeforeSheet $event) {
              $columnIndex = Coordinate::columnIndexFromString('I'); // Modify the column letter based on your requirement
              $columnIndexJ = Coordinate::columnIndexFromString('J');
              $columnIndexM = Coordinate::columnIndexFromString('M');
              $startRow = 1; // Modify the starting row based on your requirement
              $endRow = 5000; //$event->sheet->getHighestRow(); // Get the highest row in the sheet
              //$cellRange = Coordinate::stringFromColumnIndex($columnIndex) . $startRow . ':' . Coordinate::stringFromColumnIndex($columnIndex) . $endRow;
             // $event->sheet->getStyle($cellRange)->getAlignment()->setWrapText(true);
              $event->sheet->getStyleByColumnAndRow($columnIndex, $startRow, $columnIndex, $endRow)->getAlignment()->setWrapText(true);
              $event->sheet->getStyleByColumnAndRow($columnIndexJ, $startRow, $columnIndexJ, $endRow)->getAlignment()->setWrapText(true);



               for ($row = $startRow; $row <= $endRow; $row++) {
                    $event->sheet->getColumnDimensionByColumn($columnIndex)->setAutoSize(false);
                    $event->sheet->getColumnDimensionByColumn($columnIndex)->setWidth(23); // Adjust the width based on your requirement
                    $event->sheet->getColumnDimensionByColumn($columnIndexJ)->setAutoSize(false);
                    $event->sheet->getColumnDimensionByColumn($columnIndexJ)->setWidth(15);

                    $event->sheet->getColumnDimensionByColumn($columnIndexM)->setAutoSize(false);
                    $event->sheet->getColumnDimensionByColumn($columnIndexM)->setWidth(20);
               }
          },
      ];
   }
}
