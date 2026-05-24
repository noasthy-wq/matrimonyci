<?php

namespace App\Console\Commands;

use App\Models\Violation;
use Illuminate\Console\Command;

class ResolveSuspensions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moderation:resolve-suspensions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Résout les suspensions expirées';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $resolved = Violation::where('type', 'suspension')
            ->where('status', 'active')
            ->where('suspended_until', '<=', now())
            ->update(['status' => 'resolved']);

        $this->info('Resolved ' . $resolved . ' suspensions.');

        return Command::SUCCESS;
    }
}
