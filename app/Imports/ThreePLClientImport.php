<?php
  
namespace App\Imports;
  
use App\Jobs\JobProcessRLGLImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Log;
use App\ThreeplClientProduct;
use App\UploadHistory;  
use DB;
class ThreePLClientImport implements ToModel,WithChunkReading,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    protected $map_data,$client_id,$UploadHistoryID;
    function __construct($map_data,$client_id,$UploadHistoryID) {
        $this->map_data = $map_data;
        $this->client_id = $client_id;
        $this->UploadHistoryID = $UploadHistoryID;
        
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
            $rowPro = $prepared_data;
            if(isset($rowPro['supplier_product_number']) && $rowPro['supplier_product_number'] != ""){
                $check_upc_in_three_pl = ThreeplClientProduct::where('supplier_product_number',$rowPro['supplier_product_number'])->where('client_id',$client_id)->first();
                if($check_upc_in_three_pl){
                    $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                    $UploadHistory->dublicate_product = $UploadHistory->dublicate_product.'#'.$rowPro['supplier_product_number'];
                    $UploadHistory->save();
                    UploadHistory::find($this->UploadHistoryID)->increment('dublicate_product_count');
                    UploadHistory::find($this->UploadHistoryID)->increment('failed_products_count');
                }else{
                    $rowPro['client_id'] = $client_id;
                    $three_pl_product = DB::table('3pl_client_product')->insertGetId($rowPro);
                    $three_pl_pro = ThreeplClientProduct::find($three_pl_product);
                    $pro_rerult = $three_pl_pro->DraftMasterProduct();
                    $three_pl_pro->updateETIN($pro_rerult->ETIN);
                    $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                    $UploadHistory->increment('total_products');
                    
                }
            }else{
                $UploadHistory = UploadHistory::find($this->UploadHistoryID);
                $UploadHistory->increment('failed_products_count');
            }
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }
}