<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Ichtrojan\Otp\Models\Otp;
use Illuminate\Console\Command;

class DeleteExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-expired-otps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Otp::where('created_at', '<', Carbon::now()->subHour())
            ->delete();
    }
}
