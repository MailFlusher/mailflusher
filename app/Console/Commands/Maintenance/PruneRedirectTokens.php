<?php

namespace App\Console\Commands\Maintenance;

use App\Models\RedirectToken;
use Illuminate\Console\Command;

class PruneRedirectTokens extends Command
{
    protected $signature = 'mailflusher:prune-redirect-tokens';

    protected $description = 'Delete redirect tokens whose expires_at has passed';

    public function handle(): int
    {
        $count = RedirectToken::where('expires_at', '<', now())->delete();

        $this->info("Pruned {$count} expired redirect tokens.");

        return Command::SUCCESS;
    }
}
