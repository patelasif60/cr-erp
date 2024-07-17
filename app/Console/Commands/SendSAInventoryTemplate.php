<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Excel;
use App\Export\SAInvenrotyExport;
use App\SAInventoryTemplate;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SFTP;
use Log;
class SendSAInventoryTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:export_sa_inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Log::channel('SendSAInventoryTemplate')->info('Starting Sending Template');
        $file = 'Inventory_'.date('Ymd.Hi').'.csv';
        $file_with_fol = 'inventory/'.$file;
        Log::channel('SendSAInventoryTemplate')->info('Collecting data from sa inventory _template');
        $data = SAInventoryTemplate::select('sku','warehouse_code','on_hand_quantity')->get();
        Excel::store(new SAInvenrotyExport($data), $file_with_fol,'real_public');
        Log::channel('SendSAInventoryTemplate')->info('CSV Created From Data Collected at location',['location' => $file_with_fol ]);
        $key_path = public_path('inventory/cranium_sftp.ppk');
        $local_path = public_path('inventory/'.$file);
        $key = PublicKeyLoader::load(file_get_contents($key_path),'cranium');
        $host = 's-566f8c66397647d1b.server.transfer.us-east-2.amazonaws.com';
        $user = 'StoreAutomator';
        define('NET_SFTP_LOGGING', SFTP::LOG_COMPLEX);
        $sftp = new SFTP($host);
        if (!$sftp->login($user, $key)){
            Log::channel('SendSAInventoryTemplate')->info('SFTP login Failed');
        }
        Log::channel('SendSAInventoryTemplate')->info('Loged in successfully');
        $sftp->chdir('ToSA');
        Log::channel('SendSAInventoryTemplate')->info('Moved into ToSA folder');
        $sftp->chdir('Pending');
        Log::channel('SendSAInventoryTemplate')->info('Moved into Pending folder');
        $sftp->put($file, $local_path,SFTP::SOURCE_LOCAL_FILE);
        Log::channel('SendSAInventoryTemplate')->info('File Uploaded on the folder');
        @unlink($local_path);
        Log::channel('SendSAInventoryTemplate')->info('Deleted CSV generated from path',['path' => $local_path]);
        return 0;
    }
}
