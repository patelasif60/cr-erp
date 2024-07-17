<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessRLGLImport;
use phpseclib3\Net\SFTP;
use Log;
class GetReportFromRLGL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:pull_report_from_rlgl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Reports from RLGL DOT using FTP and Store into database';

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
        Log::channel('DotRLGLImport')->info('Report Downloading Starting');
        
        $host   = 'sftp2.dotfoods.com';
        $username = 'Etailer';
        $password   = 'V4dsHTH^MQf+P^_b';
        $port   = '22';
        $remote_file   = 'FromDot';
        $sftp = new SFTP($host);
        if(!$sftp->login($username, $password)){
            Log::channel('DotRLGLImport')->info('Cannote login into FTP',[
                'username' => $username,
                'password' => $password
            ]);
        }
        Log::channel('DotRLGLImport')->info('Logged in successfully');
        $list = $sftp->nlist($remote_file);
        Log::channel('DotRLGLImport')->info('Retrived Folder List from FTP');
        if ($list === false)
        {
            Log::channel('DotRLGLImport')->info('Cannote Retrive Folder from FTP',['folder' => $remote_file]);
        }
        $tempArray = [];
        $today = date('Ymd');
        if($list){
            foreach($list as $row_temp){
                if (preg_match('/RLGL'.$today.'.*?\..*?$/i', $row_temp)){
                    $tempArray[]=$row_temp;
                }
            }
        }
        if($tempArray){
            rsort($tempArray);
            $filename = $tempArray[0];
            Log::channel('DotRLGLImport')->info('Retrived File is ',['file_name' => $filename]);
            $filepath = $remote_file."/".$filename;
            $saved_file = public_path('inventory/'.$filename);
            if (!$sftp->get($filepath, $saved_file))
            {
                Log::channel('DotRLGLImport')->info('Error downloading file ',['filepath' => $filepath]);
            }
            Log::channel('DotRLGLImport')->info('File downloaded...');
            Log::channel('DotRLGLImport')->info('Creation of jobs starting to import in the table');
            ProcessRLGLImport::dispatch($saved_file);
        }else{
            Log::channel('DotRLGLImport')->info('File not found on the server');
        }
        return true;

    }
}
