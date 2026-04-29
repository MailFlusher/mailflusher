<?php

namespace App\Console\Commands\Alias;

use App\Models\Alias;
use Illuminate\Console\Command;

class ExpireBurnerAliases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailflusher:expire-burner-aliases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate burner aliases whose expires_at has passed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = Alias::dueForTimeExpiry()->update([
            'active' => false,
            'expired_at' => now(),
        ]);

        $this->info("Expired {$count} burner aliases.");

        return Command::SUCCESS;
    }
}
