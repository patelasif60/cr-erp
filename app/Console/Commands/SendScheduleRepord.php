<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\{ScheduleReports};
use Carbon\Carbon;
use App\Jobs\SendSechduleReportJob;
use App\Http\Controllers\ReportBuilderController;

class SendScheduleRepord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:export_mail_schedule_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reports mail send to user .';

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
        $today = Carbon::now();
        $scheduleReports = ScheduleReports::where('status',1)->get();
        foreach($scheduleReports as $key => $val){
            if($val->schedule_type == 'daily'){
                (new ReportBuilderController())->generateScheduleReport($val);
                //dispatch(new SendSechduleReportJob($val));
            }
            else if($val->schedule_type == 'weekly' && $today->englishDayOfWeek == $val->schedule_value){
                (new ReportBuilderController())->generateScheduleReport($val);
                //dispatch(new SendSechduleReportJob($val));
            }
            else if($val->schedule_type == 'monthly' && $today->day == $val->schedule_value){
                (new ReportBuilderController())->generateScheduleReport($val);
                //dispatch(new SendSechduleReportJob($val));   
            }
        }
    }
}
