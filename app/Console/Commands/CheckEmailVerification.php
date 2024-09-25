<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckEmailVerification extends Command
{
    protected $signature = 'app:check-email-verification';

    protected $description = 'Command description';

    public function handle()
    {
        User::where('email_verified_at', null)->delete();
    }
}
