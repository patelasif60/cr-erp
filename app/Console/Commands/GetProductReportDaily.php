<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MasterProduct;
use App\Exports\MasterProductExport;
use Excel;
use Log;
use App\MasterProductReport;
use App\MasterProductHistory;

class GetProductReportDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getProductReportDaily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send report to eTailer for Approved Products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $add_product_array = $edit_product_array = array();

        $new_products = $edit_products = array([
        'Product listing ETIN',
        'Alt ETIN',
        'Item Type',
        'Brand/Manufacture',
        'Vendor',
        'SKU',
        'UPC',
        'Case Barcode',
        'Unit Description',
        'Fullname',
        'Pick Name',
        'Unit Weight(lbs)',
        'Length(in)',
        'Width(in)',
        'Height(in)',
        'Item Description',
        'Additional Description']);

        $file = 'New_Products_'.date('d.m.Y').'.csv';
        $file_with_fol = 'reports/'.$file;
        $yesterday = date("Y-m-d", strtotime( '-1 days' ) );
        //Daily upload new products
        $data = MasterProduct::select('product_temperature','product_listing_ETIN', 'brand', 'current_supplier', 'ETIN', 'alternate_ETINs', 'upc', 'item_form_description', 'product_listing_name','weight', 'length', 'width', 'height')->whereDate('approved_date', $yesterday )->where('is_approve', 1)->get();

        //if(count($data) > 0){
            foreach($data as $row){
                $add_product_array['product_listing_ETIN'] = $row['product_listing_ETIN'];
                $add_product_array['alternate_ETINs'] = $row['alternate_ETINs'];
                if($row->product_temperature == 'Dry-Strong' || $row->product_temperature == 'Dry-Perishable' || $row->product_temperature == 'Dry-Fragile'){
                    $add_product_array['item_type'] = "Dry";
                }
                else{
                    $add_product_array['item_type'] = $row['product_temperature'];
                }
                $add_product_array['brand'] = $row['brand'];
                $add_product_array['current_supplier'] = $row['current_supplier'];
                $add_product_array['SKU'] = $row['ETIN'];
                $add_product_array['upc'] = $row['upc'];
                $add_product_array['case_barcode'] = ' - ';
                $add_product_array['item_form_description'] = "Each";
                $add_product_array['product_listing_name'] = $row['product_listing_name'];
                $add_product_array['pick_name'] = $row['product_listing_name'];
                $add_product_array['weight'] = $row['weight'];
                $add_product_array['length'] = $row['length'];
                $add_product_array['width'] = $row['width'];
                $add_product_array['height'] = $row['height'];
                $add_product_array['item_desc'] = substr($row['product_listing_name'], 0, 40);
                $add_product_array['additional'] = substr($row['product_listing_name'], 40);

                $new_products[] = $add_product_array;
            }

            Excel::store(new MasterProductExport($new_products), $file_with_fol,'real_public');
            $masterProductReport = new MasterProductReport;
            $masterProductReport->file_name = $file;
            $masterProductReport->report_type = 'daily_new_product';
            $masterProductReport->save();
        //}



        //Daily edit products
        $edit_file = 'Product_Edits_'.date('d.m.Y').'.csv';
        $file_with_fol = 'reports/'.$edit_file;

        $edit_data = MasterProduct::select('product_listing_ETIN','product_temperature', 'brand', 'current_supplier', 'ETIN', 'alternate_ETINs', 'upc', 'item_form_description', 'product_listing_name', 'product_listing_name as pick_name','weight', 'length', 'width', 'height')->whereDate('updated_at', $yesterday )->where('is_approve', 1)->get();

        //if(count($edit_data) > 0){
            foreach($edit_data as $row){
                $prodHistory = MasterProductHistory::where('ETIN', $row->ETIN)->orderBy('created_at', 'desc')->first();

                if($row['product_temperature'] != $prodHistory['product_temperature'] || $row['brand'] != $prodHistory['brand'] || $row['current_supplier'] != $prodHistory['current_supplier'] || $row['ETIN'] != $prodHistory['ETIN'] || $row['upc'] != $prodHistory['upc'] || $row['item_form_description'] != $prodHistory['item_form_description'] || $row['product_listing_name'] != $prodHistory['product_listing_name'] || $row['weight'] != $prodHistory['weight'] || $row['length'] != $prodHistory['length'] || $row['width'] != $prodHistory['width'] || $row['height'] != $prodHistory['height'])
                {
                    $edit_product_array['ETIN'] = $row['product_listing_ETIN'];
                    $edit_product_array['alternate_ETINs'] = $row['alternate_ETINs'];
                    if($row['product_temperature'] == 'Dry-Strong' || $row['product_temperature'] == 'Dry-Perishable' || $row['product_temperature'] == 'Dry-Fragile'){
                        $edit_product_array['item_type'] = "Dry";
                    }
                    else{
                        $edit_product_array['item_type'] = $row['product_temperature'];
                    }
                    $edit_product_array['brand'] = $row['brand'];
                    $edit_product_array['current_supplier'] = $row['current_supplier'];
                    $edit_product_array['SKU'] = $row['ETIN'];
                    $edit_product_array['upc'] = $row['upc'];
                    $edit_product_array['case_barcode'] = ' - ';
                    $edit_product_array['item_form_description'] = "Each";
                    $edit_product_array['product_listing_name'] = $row['product_listing_name'];
                    $edit_product_array['pick_name'] = $row['product_listing_name'];
                    $edit_product_array['weight'] = $row['weight'];
                    $edit_product_array['length'] = $row['length'];
                    $edit_product_array['width'] = $row['width'];
                    $edit_product_array['height'] = $row['height'];
                    $edit_product_array['item_desc'] = substr($row['product_listing_name'], 0, 40);
                    $edit_product_array['additional'] = substr($row['product_listing_name'], 40);

                    $edit_products[] = $edit_product_array;
                }
            }

            Excel::store(new MasterProductExport($edit_products), $file_with_fol,'real_public');
            $masterProductReport = new MasterProductReport;
            $masterProductReport->file_name = $edit_file;
            $masterProductReport->report_type = 'daily_edit_product';
            $masterProductReport->save();
        //}

        return 0;
    }
}
