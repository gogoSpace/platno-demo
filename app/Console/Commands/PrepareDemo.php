<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

final class PrepareDemo extends Command
{
    protected $signature = 'demo:prepare';

    protected $description = 'Create the private demo registry and apply only host registry migrations';

    public function handle(Filesystem $files): int
    {
        $database = config('database.connections.registry.database');
        $files->ensureDirectoryExists(dirname($database), 0700);
        $files->ensureDirectoryExists(config('demo.storage_path').'/workspaces', 0700);
        if (! is_file($database)) {
            touch($database);
            chmod($database, 0600);
        }

        return $this->call('migrate', ['--database' => 'registry', '--force' => true]);
    }
}
