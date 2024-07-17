<?php

namespace App\Imports;

use DB;
use Log;
use App\MaterialType;
use App\MasterProduct;
use App\SupplierDot;
use App\SupplierKehe;
use App\SupplierMars;
use App\UploadHistory;
use App\SupplierNestle;
use App\PackagingMaterials;
use App\SupplierHershey;
use App\ThreeplClientProduct;
use App\SupplierMiscellaneous;
use App\Jobs\JobProcessRLGLImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SupplierProductImport implements ToModel,WithChunkReading,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    protected $map_data,$supplier_id,$UploadHistoryID,$table_name;
    function __construct($map_data,$supplier_id,$UploadHistoryID,$table_name) {
        $this->map_data = $map_data;
        $this->supplier_id = $supplier_id;
        $this->UploadHistoryID = $UploadHistoryID;
        $this->table_name = $table_name;

    }

    public function model(array $row)
    {

        $map_fields = $this->map_data;
        $supplier_id = $this->supplier_id;
        $table_name = $this->table_name;
        // dd($table_name);
        $prepared_data = [];

        if($map_fields){
            foreach($map_fields as $key => $row_map_fields){
                if(isset($row[$row_map_fields])){
                    $prepared_data[$key] = $row[$row_map_fields];
                }
            }
        }
        // dump($prepared_data);
        if($prepared_data){
            $rowPro = $prepared_data;
            if($table_name == 'supplier_dot'){
                // dd($rowPro);
                if(isset($rowPro['dot_item']) && $rowPro['dot_item'] != ""){
                    $check_upc = SupplierDot::where('dot_item',$rowPro['dot_item'])->first();
                    // dd($check_upc);
                    if($check_upc){
                        $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                        $UploadHistory->dublicate_product = $UploadHistory->dublicate_product.'#'.$rowPro['dot_item'];
                        $UploadHistory->save();
                        UploadHistory::find($this->UploadHistoryID)->increment('dublicate_product_count');
                        UploadHistory::find($this->UploadHistoryID)->increment('failed_products_count');
                    }else{
                        $rowPro['supplier_id'] = $supplier_id;
                        $pro_id = DB::table($table_name)->insertGetId($rowPro);
                        $supplier_pro = SupplierDot::find($pro_id);
                        $pro_rerult = $supplier_pro->DraftMasterProduct($supplier_id);
                        $supplier_pro->updateETIN($pro_rerult->ETIN);
                        $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                        $UploadHistory->increment('total_products');
                    }
                }else{
                    $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                    $UploadHistory->increment('failed_products_count');
                }
            }elseif($table_name == 'supplier_dryers'){

            }elseif($table_name == 'supplier_kehe'){
                if(isset($rowPro['item_number']) && $rowPro['item_number'] != ""){

                    $check_upc = SupplierKehe::where('item_number',$rowPro['item_number'])->first();
                    if($check_upc){
                        $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                        $UploadHistory->dublicate_product = $UploadHistory->dublicate_product.'#'.$rowPro['item_number'];
                        $UploadHistory->save();
                        UploadHistory::find($this->UploadHistoryID)->increment('dublicate_product_count');
                        UploadHistory::find($this->UploadHistoryID)->increment('failed_products_count');
                    }else{
                        $rowPro['supplier_id'] = $supplier_id;
                        $pro_id = DB::table($table_name)->insertGetId($rowPro);
                        $supplier_pro = SupplierKehe::find($pro_id);
                        $pro_rerult = $supplier_pro->DraftMasterProduct($supplier_id);
                        $supplier_pro->updateETIN($pro_rerult->ETIN);
                        $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                        $UploadHistory->increment('total_products');
                    }
                }else{
                    $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                    $UploadHistory->increment('failed_products_count');
                }

            }elseif($table_name == 'supplier_miscellaneous'){
                if(isset($rowPro['supplier_product_number']) && $rowPro['supplier_product_number'] != ""){
                    $check_upc = SupplierMiscellaneous::where('supplier_product_number',$rowPro['supplier_product_number'])->first();
                    if($check_upc){
                        $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                        $UploadHistory->dublicate_product = $UploadHistory->dublicate_product.'#'.$rowPro['supplier_product_number'];
                        $UploadHistory->save();
                        UploadHistory::find($this->UploadHistoryID)->increment('dublicate_product_count');
                        UploadHistory::find($this->UploadHistoryID)->increment('failed_products_count');
                    }else{
                        $rowPro['supplier_ID'] = $supplier_id;
                        $pro_id = DB::table($table_name)->insertGetId($rowPro);
                        $supplier_pro = SupplierMiscellaneous::find($pro_id);
                        $pro_rerult = $supplier_pro->DraftMasterProduct($supplier_id);
                        $supplier_pro->updateETIN($pro_rerult->ETIN);
                        $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                        $UploadHistory->increment('total_products');
                    }
                }else{
                    $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                    $UploadHistory->increment('failed_products_count');
                }
            }elseif($table_name == 'supplier_hersley'){
                if(isset($rowPro['item_no']) && $rowPro['item_no'] != ""){
                    $check_upc = SupplierHershey::where('item_no',$rowPro['item_no'])->first();
                    if($check_upc){
                        $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                        $UploadHistory->dublicate_product = $UploadHistory->dublicate_product.'#'.$rowPro['item_no'];
                        $UploadHistory->save();
                        UploadHistory::find($this->UploadHistoryID)->increment('dublicate_product_count');
                        UploadHistory::find($this->UploadHistoryID)->increment('failed_products_count');
                    }else{
                        $rowPro['supplier_id'] = $supplier_id;
                        $pro_id = DB::table('supplier_hersley')->insertGetId($rowPro);
                        $supplier_pro = SupplierHershey::find($pro_id);
                        $pro_rerult = $supplier_pro->DraftMasterProduct($supplier_id);
                        $supplier_pro->updateETIN($pro_rerult->ETIN);
                        $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                        $UploadHistory->increment('total_products');
                    }
                }else{
                    $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                    $UploadHistory->increment('failed_products_count');
                }

            }elseif($table_name == 'supplier_mars'){
                if(isset($rowPro['ITEM_NO']) && $rowPro['ITEM_NO'] != ""){
                    $check_upc = SupplierMars::where('ITEM_NO',$rowPro['ITEM_NO'])->first();
                    if($check_upc){
                        $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                        $UploadHistory->dublicate_product = $UploadHistory->dublicate_product.'#'.$rowPro['ITEM_NO'];
                        $UploadHistory->save();
                        UploadHistory::find($this->UploadHistoryID)->increment('dublicate_product_count');
                        UploadHistory::find($this->UploadHistoryID)->increment('failed_products_count');
                    }else{
                        $rowPro['supplier_id'] = $supplier_id;
                        $pro_id = DB::table($table_name)->insertGetId($rowPro);
                        $supplier_pro = SupplierMars::find($pro_id);
                        $pro_rerult = $supplier_pro->DraftMasterProduct($supplier_id);
                        $supplier_pro->updateETIN($pro_rerult->ETIN);
                        $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                        $UploadHistory->increment('total_products');
                    }
                }else{
                    $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                    $UploadHistory->increment('failed_products_count');
                }
            }elseif($table_name == 'supplier_nestle'){
                if(isset($rowPro['material_number']) && $rowPro['material_number'] != ""){
                    $check_upc = SupplierNestle::where('material_number',$rowPro['material_number'])->first();
                    if($check_upc){
                        $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                        $UploadHistory->dublicate_product = $UploadHistory->dublicate_product.'#'.$rowPro['material_number'];
                        $UploadHistory->save();
                        UploadHistory::find($this->UploadHistoryID)->increment('dublicate_product_count');
                        UploadHistory::find($this->UploadHistoryID)->increment('failed_products_count');
                    }else{
                        $rowPro['supplier_id'] = $supplier_id;
                        $pro_id = DB::table($table_name)->insertGetId($rowPro);
                        $supplier_pro = SupplierNestle::find($pro_id);
                        $pro_rerult = $supplier_pro->DraftMasterProduct($supplier_id);
                        // $supplier_pro->updateETIN($pro_rerult->ETIN);
                        $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                        $UploadHistory->increment('total_products');
                    }
                }else{
                    $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                    $UploadHistory->increment('failed_products_count');
                }

            }
            elseif($table_name == 'packaging_materials'){
                $rowPro['supplier_id'] = $supplier_id;
                $count = 0;
                if (!empty($rowPro['product_description'])) {
                    $count = PackagingMaterials::where('product_description',$rowPro['product_description'])->  where('supplier_id',$rowPro['supplier_id'])->count();
                }                
                $material_type_id = in_array('material_type_id', $rowPro) ? MaterialType::where('material_type',$rowPro['material_type_id'])->first() : null;
                $rowPro['material_type_id']= $material_type_id ? $material_type_id->id :null ;
                $mstrProd = new MasterProduct();
                $type ='package';
                $rowPro['ETIN'] =  !isset($rowPro['ETIN']) ? $mstrProd->getETIN('','package') : $rowPro['ETIN'];
                if($count == 0){
                    $packagingMaterials = PackagingMaterials::create($rowPro);
                    $packagingMaterials->save();
                    $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                    $UploadHistory->increment('total_products');
                }
            }

        }
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
