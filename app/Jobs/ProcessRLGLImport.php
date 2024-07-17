<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Excel;
use App\Imports\RLGLImport;
use Log;

class ProcessRLGLImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $saved_file;
    public function __construct($saved_file)
    {
        $this->saved_file = $saved_file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            Excel::import(new RLGLImport, $this->saved_file);
            Log::channel('DotRLGLImport')->info('All jobs had been created from the downloaded excel file');
            @unlink($this->saved_file);
            Log::channel('DotRLGLImport')->info('Downloaded File Deleted',['saved_file' => $saved_file]);
        } catch (\throwable $e) {
            Log::channel('DotRLGLImport')->info('Erro Encountered While creating import job', [
                'e' => json_encode($e->getMessage()),
            ]);
        }
        
    }
}
