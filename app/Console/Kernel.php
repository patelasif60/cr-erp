<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\UpdateSAInventoryTemplateFromDotRLGL::class,
        Commands\UpdateSAInventoryTemplateFromIPC::class,
        Commands\SendSAInventoryTemplate::class,
        Commands\GetReportFromRLGL::class,
        Commands\ImportSaInventoryTemplate::class,
        Commands\GenerateInventoryReport::class,
        Commands\ProductRestockCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:pull_report_from_rlgl')->dailyAt('10:30');
        $schedule->command('command:Update_SA_Inventory_Template_From_DotRLGL')->dailyAt('11:30');
        $schedule->command('command:Update_SA_Inventory_Template_From_IPC')->cron('0 12-21 * * *');
        $schedule->command('command:export_sa_inventory')->cron('30 11-23 * * *');
        $schedule->command('command:getProductReportDaily')->dailyAt('11:15');

        $schedule->command('command:import_sa_inventory')->everyFifteenMinutes();
        $schedule->command('command:incoming_order_processing')->everyFifteenMinutes();

        $schedule->command('command:exportUpdatedMasterProducts')->everyThirtyMinutes();
        $schedule->command('command:exportChannelInclusionTemplate')->everyThirtyMinutes();
        $schedule->command('command:update_inventory_report')->everyFifteenMinutes();
        $schedule->command('command:oos_notification')->dailyAt('9:20');
        $schedule->command('command:release_hold')->dailyAt('4:30');
        $schedule->command('command:generate_product_restock_report')->everyTwoMinutes();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
