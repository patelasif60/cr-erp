<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MasterProduct;
use App\UnitSize;
use App\WareHouse;
use Response;
use File;
use Illuminate\Http\Request;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportMPT;
use Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use phpseclib3\Crypt\PublicKeyLoader;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SFTP;
use Symfony\Component\Finder\Finder;

class ExportUpdatedMasterProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:exportUpdatedMasterProducts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Updated Products from Master Product Table';

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
		// Export Product Template CSV
		$this->exportProductTemplate();
		
		//Remove this line before live(Its for deleting file from local)
		//unlink('public/'.$pathAndFilename);
        return 0;
		
    }
	
	public function exportProductTemplate(){
		
		$add_product_array = $units = array();
		
		$halfHourBack = date("Y-m-d H:i:s", strtotime(' -30 minutes ') );
		
		$products = MasterProduct::select('ETIN', 'parent_ETIN', 'product_listing_name', 'full_product_desc', 'brand', 'product_category', 'upc', 'gtin','pack_form_count', 'unit_in_pack', 'unit_size', 'width', 'length', 'height', 'weight', 'warehouses_assigned', 'product_tags', 'about_this_item')->where('approved_date', '>' ,$halfHourBack )->where('is_approve', 1)->get();
		
		//Without Approved date Chheck
		// $products = MasterProduct::select('ETIN', 'parent_ETIN', 'product_listing_name', 'full_product_desc', 'brand', 'product_category', 'upc', 'gtin','pack_form_count', 'unit_in_pack', 'unit_size', 'width', 'length', 'height', 'weight', 'warehouses_assigned', 'product_tags', 'about_this_item')->where('is_approve', 1)->get();
		
		$productCount = count($products);
		if($productCount > 0){
			$csv_name = 'Product_Adds_'.date("Ymd_Hi", strtotime('now')).'.csv';
			
			$pathAndFilename =  "temp/".$csv_name;
			
			//Checking If TEMP folder exsists, or creating
			if (!File::exists(public_path()."/temp")) {
				File::makeDirectory(public_path() . "/temp");
			}		
			
			$new_products = $this->getMasterProductsArray($products);
			
			$path = 'public/'.$pathAndFilename;
			
			// Create Excel File in TEMP folder, Local
			Excel::store(new ExportMPT($new_products), $pathAndFilename,'real_public');		
			
			// Logging in SFTP
			$sftp = $this->establishSFTP();
			
			//Getting all files from local Temp
			$finder = new Finder();
			$finder->files()->in(public_path()."/temp");
			
			foreach ($finder as $file) {
				$sftp->put('/cranium-sftp-s3/TesttoSA/'.$file->getRelativePathname(),$file->getRealPath(),SFTP::SOURCE_LOCAL_FILE);
				//$sftp->put('/cranium-sftp-s3/ToSA/Pending/'.$file->getRelativePathname(),$file->getRealPath(),SFTP::SOURCE_LOCAL_FILE);
				//$sftp->put('/cranium-sftp-s3/Orders/FromSA/'.$file->getRelativePathname(),$file->getRealPath(),SFTP::SOURCE_LOCAL_FILE);
				
				unlink($file->getRealPath());
			}
			Log::channel('ExportUpdatedMasterProducts')->info($productCount . ' MasterProduct Updgradation found for last 30 mins.');
			unset($sftp);
		} else {
			Log::channel('ExportUpdatedMasterProducts')->info('No MasterProduct Updgradation found for last 30 mins.');
			unset($sftp);
		}
	}
	
	public function getMasterProductsArray($products){
		$new_products = [];
		foreach ($products as $product) {
			$about0 = null;
			$about1 = null;
			$about2 = null;
			$about3 = null;
			$about4 = null;
			
			$unit_size = $product->unit_size;
			$about_this_item = explode('#' , $product->about_this_item);
			
			if(isset($about_this_item[0])){
				$about0 = $about_this_item[0];
			}
			if(isset($about_this_item[1])){
				$about1 = $about_this_item[1];
			}
			if(isset($about_this_item[2])){
				$about2 = $about_this_item[2];
			}
			if(isset($about_this_item[3])){
				$about3 = $about_this_item[3];
			}
			if(isset($about_this_item[4])){
				$about4 = $about_this_item[4];
			}
			
			if($unit_size != ''){
				$units = explode('-', $unit_size);				
				$unitname=  DB::table('unit_sizes')->where('abbreviation',$units[1])->get();
				$getUnitOfContent = new UnitSize;
				$unitOfContent = $getUnitOfContent->getUnitOfContent($units[1]);
				$units[1] = $unitOfContent->unit_of_content;
			} else {
				$units[0] = '0';
				$units[1] = 'Null';
			}
			
			if($product->upc != ''){
				$product_id = $product->upc;
				$product_id_type = 'UPC';
			} elseif($product->gtin != '') {
				$product_id = $product->gtin;
				$product_id_type = 'GTIN';
			} else {
				$product_id = 'Null';
				$product_id_type = 'Null';
			}
			$wareHouseId = null;
			if($product->warehouses_assigned != ""){
				$getWareHouseId = new WareHouse;
				$wareHouseId = $getWareHouseId->getWareHouseId($product->warehouses_assigned);
			}
			
			$getimages = $images = null;
			$getimages = New MasterProduct;
			$images = $getimages->productImageFromProductImageTable($product->ETIN);

			$add_product_array['productdata.sku'] = $product->ETIN;
			$add_product_array['parentage'] = 'Single';
			$add_product_array['parent_sku'] = $product->parent_ETIN;
			$add_product_array['variation_types'] = 'Null';
			$add_product_array['variation_options'] = 'Null';
			$add_product_array['title'] = $product->product_listing_name;
			$add_product_array['description'] = $product->full_product_desc;
			$add_product_array['brand_name'] = $product->brand;
			$add_product_array['price'] = '0';
			$add_product_array['category_codes'] = $product->product_category;
			$add_product_array['image_url_1'] = $images['image_URL1'];
			$add_product_array['image_url_2'] = $images['image_URL2'];
			$add_product_array['image_url_3'] = $images['image_URL3'];
			$add_product_array['image_url_4'] = $images['image_URL4'];
			$add_product_array['image_url_5'] = $images['image_URL5'];
			$add_product_array['image_url_6'] = $images['image_URL6'];
			$add_product_array['image_url_7'] = $images['image_URL7'];
			$add_product_array['image_url_8'] = $images['image_URL8'];
			$add_product_array['image_url_9'] = $images['image_URL9'];
			$add_product_array['image_url_10'] = $images['image_URL10'];
			$add_product_array['product_id'] = $product_id;
			$add_product_array['product_id_type'] = $product_id_type;
			$add_product_array['quantity'] = $product->pack_form_count;
			$add_product_array['item_package_quantity'] = $product->unit_in_pack;
			$add_product_array['amount_of_content'] = $units[0];
			$add_product_array['unit_of_content'] = $units[1];
			$add_product_array['width'] = $product->width;
			$add_product_array['length'] = $product->length;
			$add_product_array['height'] = $product->height;
			$add_product_array['weight'] = $product->weight;
			$add_product_array['warehouse_id'] = $wareHouseId;
			$add_product_array['meta_title'] = '';
			$add_product_array['meta_description'] = '';
			$add_product_array['meta_keywords'] = $product->product_tags;
			$add_product_array['update_delete'] = '';
			$add_product_array['enable_product_update'] = 'Yes';
			$add_product_array['search_terms'] = '';
			$add_product_array['bullet_point_1'] = $about0;
			$add_product_array['bullet_point_2'] = $about1;
			$add_product_array['bullet_point_3'] = $about2;
			$add_product_array['bullet_point_4'] = $about3;
			$add_product_array['bullet_point_5'] = $about4;
			$add_product_array['search_term_1'] = '';
			$add_product_array['search_term_2'] = '';
			$add_product_array['search_term_3'] = '';
			$add_product_array['search_term_4'] = '';
			$add_product_array['search_term_5'] = '';
			$add_product_array['attr_name_1'] = '';
			$add_product_array['attr_value_1'] = '';
			$add_product_array['attr_name_2'] = '';
			$add_product_array['attr_value_2'] = '';
			$add_product_array['attr_name_3'] = '';
			$add_product_array['attr_value_3'] = '';
			$add_product_array['attr_name_4'] = '';
			$add_product_array['attr_value_4'] = '';
			$add_product_array['attr_name_5'] = '';
			$add_product_array['attr_value_5'] = '';
			$add_product_array['attr_name_6'] = '';
			$add_product_array['attr_value_6'] = '';
			$add_product_array['attr_name_7'] = '';
			$add_product_array['attr_value_7'] = '';
			$add_product_array['attr_name_8'] = '';
			$add_product_array['attr_value_8'] = '';
			$add_product_array['attr_name_9'] = '';
			$add_product_array['attr_value_9'] = '';
			
			array_push($new_products, $add_product_array);			
		}
		return $new_products;
	}
	
	public function establishSFTP(){
		$key_path = public_path('inventory/cranium_sftp.ppk');
        $key = PublicKeyLoader::load(file_get_contents($key_path), 'cranium');
        $host = 's-566f8c66397647d1b.server.transfer.us-east-2.amazonaws.com';
        $user = 'StoreAutomator';
	
        define('NET_SFTP_LOGGING', SFTP::LOG_COMPLEX);
        
		//Connecting to SFTP
        $sftp = new SFTP($host);
        
        if (!$sftp->login($user, $key)){
            Log::channel('ExportUpdatedMasterProducts')->info('SFTP login Failed from ExportUpdatedMasterProducts');
        }
		
        Log::channel('ExportUpdatedMasterProducts')->info('SFTP Loged in successfully from ExportUpdatedMasterProducts');
		
		return $sftp;
	}
}

