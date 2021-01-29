<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Jobs\ExportOrganizationData;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SendUNSOSDataCron::class,
        Commands\SendOIOSIDDataCron::class,
        Commands\TestCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $organization_groups = [
        //     'UNSOS' => [
        //         'UNSOS',
        //         'UNSOS (SO)',
        //         'UNSOA',
        //         'UNSOM'
        //     ],
        //     'UNICEF'    =>  [
        //         'UNICEF (ESARO) (RO)',
        //         'UNICEF (KCO)',
        //         'UNICEF USSC SO'
        //     ]
        // ];

        // foreach ($organization_groups as $group => $organizations) {
        //     $schedule->job(new ExportOrganizationData($organizations, $group), 'organization_data')->dailyAt('19:20');
        // }
        $schedule->command("senddata:unsos")->daily();
        $schedule->command("senddata:oiosid")->daily();
        $schedule->command("senddata:unicef-so")->daily();
        $schedule->command("senddata:undp-kco")->daily();
        $schedule->command("senddata:unhcr-kco")->daily();
        $schedule->command("senddata:unicef-kco")->daily();
        $schedule->command("senddata:unicef-ro")->daily();
        $schedule->command("adobesign:checkstatus")->everyFiveMinutes();
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
