<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Demo\WorkspaceFiles;
use App\Models\DemoWorkspace;
use Illuminate\Console\Command;

final class CleanupDemos extends Command
{
    protected $signature = 'demo:cleanup {--limit=100 : Maximum expired workspaces to remove}';

    protected $description = 'Remove expired demo content and private uploads without touching active requests';

    public function handle(WorkspaceFiles $files): int
    {
        $limit = max(1, min(1000, (int) $this->option('limit')));
        $workspaces = DemoWorkspace::query()->where(function ($query): void {
            $query->where('expires_at', '<=', now())->orWhere('status', 'expired')
                ->orWhere(fn ($pending) => $pending->where('status', 'provisioning')->where('created_at', '<', now()->subMinutes(15)));
        })->orderBy('expires_at')->limit($limit)->get();
        $removed = 0;
        foreach ($workspaces as $workspace) {
            $lock = $files->lock($workspace->getKey(), true, false);
            if ($lock === null && is_dir($files->directory($workspace->getKey()))) {
                continue;
            }
            try {
                $current = $workspace->fresh();
                if ($current !== null && ! $current->isAvailable()) {
                    $files->delete($current->getKey());
                    $current->delete();
                    $removed++;
                }
            } finally {
                if ($lock !== null) {
                    $files->unlock($lock);
                }
            }
        }
        $this->info("Removed {$removed} expired demo workspaces.");

        return self::SUCCESS;
    }
}
