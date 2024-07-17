<?php

use App\Client;
use App\MasterProduct;
use App\Supplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeSupplierClientNameToId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('id', function (Blueprint $table) {
            //
        });
        $this->changeSupplierClientId('master_product');       
        $this->changeSupplierClientId('master_product_queue');       
        $this->changeSupplierClientId('master_product_history');       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('id', function (Blueprint $table) {
            //
        });         
    }

    private function changeSupplierClientId($table_name) {

        $mps = DB::table($table_name)->whereNotNull('current_supplier')->get();

        if (isset($mps) && count($mps) > 0) {
            foreach($mps as $mp) {
                $id = 0;
                if ($mp->supplier_type == 'supplier') {
                    $sup = Supplier::where('name', $mp->current_supplier)->first();
                    if (isset($sup)) { $id = $sup->id; }                    
                } else if ($mp->supplier_type == 'client') {
                    $sup = Client::where('company_name', $mp->current_supplier)->first();
                    if (isset($sup)) { $id = $sup->id; }                    
                }

                if ($id > 0) {
                    DB::table($table_name)->where('id', $mp->id)->update(['client_supplier_id' => $id]);  
                }                
            }
        }
    }
}
