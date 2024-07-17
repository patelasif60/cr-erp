<?php
namespace App\Console\Commands;

use phpseclib3\Net\SFTP;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use phpseclib3\Crypt\PublicKeyLoader;
use App\Client;
use App\WareHouse;
use App\MasterProduct;
use App\SaveStoreAutomatorExportStatus;
use App\OrderDetail;
use App\OrderSummary;
use App\ClientChannelConfiguration;
use App\Exports\ExportCIT;
use App\Exports\ExportIT;
use Illuminate\Support\Facades\Storage;
use File;
use Illuminate\Http\Request;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use Symfony\Component\Finder\Finder;

class ExportChannelInclusionTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:exportChannelInclusionTemplate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Updated Products from Master Product Table as Channel Inclusion Template';

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
		$fol = 'temp' . DIRECTORY_SEPARATOR;        
        if (!file_exists(public_path($fol))) {
            mkdir(public_path($fol));
        }
		
		$sftp = $this->establishSFTP();
		
        //$sftp->chdir('ToSA/Processed/');
        $sftp->chdir('TesttoSA/');
        
        $elements = $sftp->nlist('.');
		//dd($elements);
		$latestModTime = 0;
		$filePath = "";
		$fileName = "";
		$fileFound = 0;
		$fileString = array();
		$status = '';
		foreach ($elements as $element) {
			
            //if (str_contains(strtolower($element), '_result.txt') && $sftp->is_file('/cranium-sftp-s3/ToSA/Processed/' . $element)) {
            if (str_contains(strtolower($element), '_result.txt') && $sftp->is_file('/cranium-sftp-s3/TesttoSA/' . $element)) {
				//echo $element;		
							
                //$time = $sftp->filemtime('/cranium-sftp-s3/ToSA/Processed/' . $element);
                $time = $sftp->filemtime('/cranium-sftp-s3/TesttoSA/' . $element);
                if ($time > $latestModTime) {
                    $latestModTime = $time;
                    //$filePath = '/cranium-sftp-s3/ToSA/Processed/' . $element;
                    $filePath = '/cranium-sftp-s3/TesttoSA/' . $element;
                    $fileName = $element;
					
					$fileFound = 1;
                }
            }
        }
		
		if($fileFound != 1){				
			Log::channel('ExportChannelInclusionTemplate')->info('No UnProcessed TXT File found in ToSA/ProcessedTrackings Folder..');
			$error_log[] = 'No UnProcessed txt File found in ToSA/ProcessedTrackings Folder..';
			//exit;
			dd($error_log);
		}
		$downFile = $fol . $fileName;
		$sftp->get($filePath, public_path($downFile)); 
		$content = fopen(public_path($downFile), "r");

        while(!feof($content)){
            $line = fgets($content);			
			$fileStringArray[] = trim($line);
        }

        fclose($content);
		$localFilePath = $downFile;
		
		
		if (in_array("Overall result: Success", $fileStringArray)) {
			$status = 'Success';
		} elseif (in_array("Overall result: Fail", $fileStringArray)) {
			$status = 'Fail';
		} else {
			$status = 'Untracked';
		}
		$this->updateStatusTable($fileName, $localFilePath , $fileStringArray, $status);
		if($status = 'Success'){
			$this->createChannelTemplate($sftp);
			$this->createInventoryTemplate($sftp);
			unset($sftp);
		} else {
			Log::channel('ExportChannelInclusionTemplate')->info('Failed result found in SFTP, Filepath: '.$downFile);
			unset($sftp);
		}
        return 0;
    }
	
	
	public function updateStatusTable($fileName , $localFilePath , $fileStringArray , $status){
		
		//Need to check this explode logic
		$fileTimeExtract = explode (' - ' , $fileStringArray[0]);
		$time = substr(last($fileTimeExtract) , 0 , 20);
		
		$ifStatusExsists = SaveStoreAutomatorExportStatus::where('file_name', $fileName)->first();
		if ($ifStatusExsists === null) {
			$saveStatus = new SaveStoreAutomatorExportStatus;
			$saveStatus->file_name = $fileName;
			$saveStatus->file_path = $localFilePath;
			$saveStatus->process_time = date("Y-m-d H:i:s", strtotime($time));
			$saveStatus->status = $status;
			$saveStatus->save();
		}
	}
	
	public function createChannelTemplate($sftp){
		$add_product_array = array();
		
		$halfHourBack = date("Y-m-d H:i:s", strtotime(' -30 minutes ') );
		
		//$products = MasterProduct::select('ETIN', 'parent_ETIN', 'product_listing_name', 'full_product_desc', 'brand', 'product_category', 'upc', 'gtin','pack_form_count', 'unit_in_pack', 'unit_size', 'width', 'length', 'height', 'weight', 'warehouses_assigned', 'product_tags', 'about_this_item')->where('approved_date', '>' ,$halfHourBack )->where('is_approve', 1)->get();
		
		//Without Approved date Chheck
		$products = MasterProduct::select('ETIN', 'chanel_ids')->where('is_approve', 1)->get();
		
		$csv_name = 'channel_inclusion_'.date("mdYHis", strtotime('now')).'.csv';
		
		$pathAndFilename =  "temp/".$csv_name;
		
		//Checking If TEMP folder exsists, or creating
		if (!File::exists(public_path()."/temp")) {
            File::makeDirectory(public_path() . "/temp");
        }
		
		$new_products = $this->getInventoryTemplateProducts($products);
		
		$path = 'public/'.$pathAndFilename;
		
		// Create Excel File in TEMP folder, Local
		Excel::store(new ExportCIT($new_products), $pathAndFilename,'real_public');		

		
		//Getting all files from local Temp
		$finder = new Finder();
		$finder->files()->in(public_path()."/temp");
				foreach ($finder as $file) {
			$sftp->put('/cranium-sftp-s3/TesttoSA/'.$file->getRelativePathname(),$file->getRealPath(),SFTP::SOURCE_LOCAL_FILE);
			//$sftp->put('/cranium-sftp-s3/ToSA/Pending/'.$file->getRelativePathname(),$file->getRealPath(),SFTP::SOURCE_LOCAL_FILE);
			//$sftp->put('/cranium-sftp-s3/Orders/FromSA/'.$file->getRelativePathname(),$file->getRealPath(),SFTP::SOURCE_LOCAL_FILE);
			
			unlink($file->getRealPath());
		}
	}
	
	public function createInventoryTemplate($sftp){
		$add_product_array = array();
		
		$halfHourBack = date("Y-m-d H:i:s", strtotime(' -30 minutes ') );
		
		//$products = MasterProduct::select('ETIN', 'parent_ETIN', 'product_listing_name', 'full_product_desc', 'brand', 'product_category', 'upc', 'gtin','pack_form_count', 'unit_in_pack', 'unit_size', 'width', 'length', 'height', 'weight', 'warehouses_assigned', 'product_tags', 'about_this_item')->where('approved_date', '>' ,$halfHourBack )->where('is_approve', 1)->get();
		
		//Without Approved date Chheck
		$products = MasterProduct::select('ETIN', 'parent_ETIN', 'product_listing_name', 'full_product_desc', 'brand', 'product_category', 'upc', 'gtin','pack_form_count', 'unit_in_pack', 'unit_size', 'width', 'length', 'height', 'weight', 'warehouses_assigned', 'product_tags', 'about_this_item')->where('is_approve', 1)->get();
		//dd($products);
		
		$csv_name = 'inventory_'.date("mdYHis", strtotime('now')).'.csv';
		
		$pathAndFilename =  "temp/".$csv_name;
		
		//Checking If TEMP folder exsists, or creating
		if (!File::exists(public_path()."/temp")) {
            File::makeDirectory(public_path() . "/temp");
        }
		
		$new_products = $this->getChannelInclusionProducts($products);
		
		
		$path = 'public/'.$pathAndFilename;
		
		// Create Excel File in TEMP folder, Local
		Excel::store(new ExportIT($new_products), $pathAndFilename,'real_public');		
		
		//Getting all files from local Temp
		$finder = new Finder();
		$finder->files()->in(public_path()."/temp");
				foreach ($finder as $file) {
			$sftp->put('/cranium-sftp-s3/TesttoSA/'.$file->getRelativePathname(),$file->getRealPath(),SFTP::SOURCE_LOCAL_FILE);
			//$sftp->put('/cranium-sftp-s3/ToSA/Pending/'.$file->getRelativePathname(),$file->getRealPath(),SFTP::SOURCE_LOCAL_FILE);
			//$sftp->put('/cranium-sftp-s3/Orders/FromSA/'.$file->getRelativePathname(),$file->getRealPath(),SFTP::SOURCE_LOCAL_FILE);
			
			unlink($file->getRealPath());
		}
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
            Log::channel('ExportChannelInclusionTemplate')->info('SFTP login Failed from ExportChannelInclusionTemplate');
        }
		
        Log::channel('ExportChannelInclusionTemplate')->info('SFTP Loged in successfully from ExportChannelInclusionTemplate');
		
		return $sftp;
	}
	
	public function getChannelInclusionProducts($products){
		$new_products = [];
		foreach ($products as $product) {
			$wareHouseId = null;
			if($product->warehouses_assigned != ""){
				$getWareHouseId = new WareHouse;
				$wareHouseId = $getWareHouseId->getWareHouseId($product->warehouses_assigned);
			}
			
			$add_product_array['warehouseproduct.sku'] = $product->ETIN;
			$add_product_array['warehouse_id'] = $wareHouseId;
			$add_product_array['on_hand_quantity'] = 'Will be added in Future';
			
			array_push($new_products, $add_product_array);
		}
		return $new_products;
	}
	
	public function getInventoryTemplateProducts($products){
		$new_products = [];
		foreach ($products as $product) {
			
			$channel_name = null;
			if($product->chanel_ids != ""){
				$getChannelName = new MasterProduct;
				$channel_name = $getChannelName->getChannelName($product->chanel_ids);
			}
			
			$add_product_array['0.channelinclusions.sku'] = $product['ETIN'];
			$add_product_array['channel_name'] = $channel_name;
			$add_product_array['inclusion'] = 'Include';
			
			array_push($new_products, $add_product_array);
		}
		return $new_products;
	}
}
