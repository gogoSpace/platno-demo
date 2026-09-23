<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected string $temporaryDirectory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withCredentials();
        if (! app()->environment('testing')) {
            throw new \RuntimeException('Demo tests require the testing environment.');
        }
        $this->temporaryDirectory = sys_get_temp_dir().'/platno-demo-test-'.bin2hex(random_bytes(12));
        mkdir($this->temporaryDirectory, 0700, true);
        file_put_contents($this->temporaryDirectory.'/.disposable-test', 'isolated');
        config([
            'demo.storage_path' => $this->temporaryDirectory,
            'database.connections.registry.database' => $this->temporaryDirectory.'/registry.sqlite',
            'database.connections.workspace.database' => ':memory:',
            'cache.stores.file.path' => $this->temporaryDirectory.'/cache',
            'cache.stores.file.lock_path' => $this->temporaryDirectory.'/cache',
            'session.driver' => 'array',
        ]);
        DB::purge('registry');
        DB::purge('workspace');
        $this->artisan('demo:prepare')->assertSuccessful();
    }

    protected function tearDown(): void
    {
        DB::purge('registry');
        DB::purge('workspace');
        if (isset($this->temporaryDirectory) && str_starts_with($this->temporaryDirectory, sys_get_temp_dir().'/platno-demo-test-') && is_file($this->temporaryDirectory.'/.disposable-test')) {
            (new Filesystem)->deleteDirectory($this->temporaryDirectory);
        }
        parent::tearDown();
    }
}
