<?php

namespace App\Console\Commands;

use App\Models\Violation;
use Illuminate\Console\Command;

class CheckSuspensions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moderation:check-suspensions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and lift expired suspensions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $violations = Violation::where('status', 'active')
            ->where('suspended_until', '<', now())
            ->get();

        foreach ($violations as $violation) {
            $violation->update(['status' => 'resolved']);
        }

        $this->info('Checked ' . $violations->count() . ' suspensions');
        return 0;
    }
}
