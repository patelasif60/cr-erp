<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
class UploadHistory extends Model
{
    protected $table = "upload_history";
    protected $fillable = [
        'client_id', 'failed_products_count', 'dublicate_product_count','failed_products','dublicate_product'
    ];


}