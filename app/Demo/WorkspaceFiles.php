<?php

declare(strict_types=1);

namespace App\Demo;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use RuntimeException;

final class WorkspaceFiles
{
    public function __construct(private readonly Filesystem $files) {}

    public function directory(string $identifier): string
    {
        if (preg_match('/\A[a-f0-9]{32}\z/', $identifier) !== 1) {
            throw new InvalidArgumentException('Invalid workspace identifier.');
        }

        return rtrim(config('demo.storage_path'), '/').'/workspaces/'.$identifier;
    }

    public function prepare(string $identifier): void
    {
        $directory = $this->directory($identifier);
        $this->files->makeDirectory($directory.'/media', 0700, true);
        if (file_put_contents($directory.'/content.sqlite', '') === false) {
            throw new RuntimeException('Cannot create workspace database.');
        }
        chmod($directory.'/content.sqlite', 0600);
    }

    /** @return resource|null */
    public function lock(string $identifier, bool $exclusive, bool $wait = true)
    {
        $directory = $this->directory($identifier);
        if (! is_dir($directory)) {
            return null;
        }
        $handle = fopen($directory.'/.lock', 'c');
        if ($handle === false) {
            throw new RuntimeException('Cannot lock workspace.');
        }
        $deadline = microtime(true) + ($wait ? 5 : 0);
        do {
            if (flock($handle, ($exclusive ? LOCK_EX : LOCK_SH) | LOCK_NB)) {
                return $handle;
            }
            if (microtime(true) >= $deadline) {
                fclose($handle);

                return null;
            }
            usleep(25000);
        } while (true);
    }

    /** @param resource $handle */
    public function unlock($handle): void
    {
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    public function delete(string $identifier): void
    {
        $directory = $this->directory($identifier);
        if (is_dir($directory) && ! $this->files->deleteDirectory($directory)) {
            throw new RuntimeException('Cannot remove expired workspace files.');
        }
    }
}
