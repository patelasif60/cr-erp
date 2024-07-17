<?php

namespace App\Console\Commands;

use App\OrderSummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReleaseHold extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:release_hold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs Everyday and Releases the hold/review of a particular date';

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
        Log::channel('ReleaseHold')->info('Starting Release Hold Process');


        Log::channel('ReleaseHold')->info('Fetching records for: ' . date('Y-m-d'));
        $to_release = OrderSummary::where('release_date', date('Y-m-d'))->get();
        Log::channel('ReleaseHold')->info('Fetching records for: ' . date('Y-m-d') . ' complete');
        Log::channel('ReleaseHold')->info('Total Records: ' . count($to_release));

        if (count($to_release) <= 0) {
            Log::channel('ReleaseHold')->info('No Orders to Release Hold. Process Complete');
            return 0;
        }

        foreach($to_release as $os) {
            Log::channel('ReleaseHold')->info('Processing Summary Id: ' . $os->id);
            $old_status = $os->old_status;
            $os->order_status = 1;
            $os->old_status = null;
            $os->release_date = null;
            $os->save();
            Log::channel('ReleaseHold')->info('Processed Summary Id: ' . $os->id);
        }
        
        Log::channel('ReleaseHold')->info('Release Hold Process Complete');

        return 0;
    }
}
