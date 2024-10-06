<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckEmailVerification extends Command
{
    protected $signature = 'app:check-email-verification';

    protected $description = 'Command description';

    public function handle()
    {
        User::whereNull('email_verified_at')
            ->where('created_at', '<=', Carbon::now()->subHour())
            ->delete();
    }
}
