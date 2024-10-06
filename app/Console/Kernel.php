<?php

namespace App\Console;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:delete-expired-otps')->everyMinute();
        $schedule->command('app:check-email-verification')->everyMinute();
        $schedule->command('model:prune', [
            '--model' => [Offer::class, Product::class, Warehouse::class],
        ])->hourly();
        $schedule->command('queue:work')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
