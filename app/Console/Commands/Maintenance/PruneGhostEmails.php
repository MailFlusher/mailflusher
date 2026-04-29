<?php

namespace App\Console\Commands\Maintenance;

use App\Models\StoredEmail;
use Illuminate\Console\Command;

class PruneGhostEmails extends Command
{
    protected $signature = 'mailflusher:prune-ghost-emails {--days=30}';

    protected $description = 'Delete ghost-inbox stored emails older than N days (default 30)';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($days);

        $count = StoredEmail::where('received_at', '<', $cutoff)->delete();

        $this->info("Pruned {$count} ghost-inbox emails older than {$days} days.");

        return Command::SUCCESS;
    }
}
