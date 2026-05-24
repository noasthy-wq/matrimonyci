<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire les abonnements qui ont atteint la date d\'expiration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expired = Subscription::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        $this->info('Expired ' . $expired . ' subscriptions.');

        return Command::SUCCESS;
    }
}
