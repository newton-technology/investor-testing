<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace Newton\InvestorTesting\Console;

use Newton\InvestorTesting\Console\Commands\ExportCommand;
use Newton\InvestorTesting\Console\Commands\ImportCommand;
use Newton\InvestorTesting\Console\Commands\UserPasswordChangeCommand;
use Newton\InvestorTesting\Console\Commands\UserPasswordRemoveCommand;
use Newton\InvestorTesting\Console\Commands\UserRoleAddCommand;
use Newton\InvestorTesting\Console\Commands\UserRoleRemoveCommand;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ExportCommand::class,
        ImportCommand::class,
        UserRoleAddCommand::class,
        UserRoleRemoveCommand::class,
        UserPasswordChangeCommand::class,
        UserPasswordRemoveCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
