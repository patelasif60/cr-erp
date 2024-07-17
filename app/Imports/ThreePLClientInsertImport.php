<?php
  
namespace App\Imports;
  
use App\Jobs\JobProcessRLGLImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Log;
use App\ThreeplClientProduct;
use App\UploadHistory;  
use App\MasterProduct;
use DB;
class ThreePLClientInsertImport implements ToModel,WithChunkReading,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    protected $client_id,$UploadHistoryID,$map_data;
    function __construct($map_data,$client_id,$UploadHistoryID) {
        $this->client_id = $client_id;
        $this->UploadHistoryID = $UploadHistoryID;
        $this->map_data = $map_data;
    }

    public function model(array $row)
    {
        $map_fields = $this->map_data;
        $client_id = $this->client_id;
        $prepared_data = [];
        if($map_fields){
            foreach($map_fields as $key => $row_map_fields){
                if(isset($row[$row_map_fields])){
                    $prepared_data[$key] = $row[$row_map_fields];
                }
            }
        }
        if($prepared_data){
            $brand = NULL;
            $unit_size = NULL;
            $category = NULL;
            $product_temperature = NULL;
            $supplier_product_number = NULL;
            $upc = NULL;
            $supplier_status = NULL;
            $cost = NULL;
            $unit_description = NULL;

            if(isset($prepared_data['brand'])) $brand = $prepared_data['brand'];
            if(isset($prepared_data['unit_size'])) $unit_size = $prepared_data['unit_size'];
            if(isset($prepared_data['category'])) $category = $prepared_data['category'];
            // if(isset($prepared_data['product_temperature'])) $product_temperature = $prepared_data['product_temperature'];
            if(isset($prepared_data['supplier_product_number'])) $supplier_product_number = $prepared_data['supplier_product_number'];
            if(isset($prepared_data['upc_case'])) $upc_case = $prepared_data['upc_case'];
            if(isset($prepared_data['supplier_status'])) $supplier_status = $prepared_data['supplier_status'];
            if(isset($prepared_data['cost'])) $cost = $prepared_data['cost'];
            if(isset($prepared_data['unit_description'])) $unit_description = $prepared_data['unit_description'];

            if($brand == '' || $unit_size == '' || $category == '' ||  $supplier_product_number == '' || $upc_case == '' || $supplier_status == '' || $cost == '' || $unit_description == ''){
                UploadHistory::find($this->UploadHistoryID)->increment('failed_products_count');
            }else{
                $check_supplier_product_number_in_three_pl = ThreeplClientProduct::where('supplier_product_number',$supplier_product_number)->where('client_id',$client_id)->first();
                $check_upc_in_master_product_table = MasterProduct::where('upc',$upc_case)->first();

                if(empty($check_supplier_product_number_in_three_pl) && empty($check_upc_in_master_product_table)){
                    $prepared_data['client_id'] = $this->client_id;
                    $three_pl_product = DB::table('3pl_client_product')->insertGetId($prepared_data);
                    $three_pl_pro = ThreeplClientProduct::find($three_pl_product);
                    $pro_rerult = $three_pl_pro->addMasterProduct();
                    $three_pl_pro->updateETIN($pro_rerult->ETIN);
                    $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                    $UploadHistory->increment('total_products');
                }else{
                    UploadHistory::find($this->UploadHistoryID)->increment('failed_products_count');
                    UploadHistory::find($this->UploadHistoryID)->increment('dublicate_product_count');
                }
            }
        }

        

    }

    public function chunkSize(): int
    {
        return 500;
    }
}