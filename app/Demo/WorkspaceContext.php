<?php

declare(strict_types=1);

namespace App\Demo;

use App\Models\DemoWorkspace;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

final class WorkspaceContext
{
    public function __construct(private readonly WorkspaceFiles $files) {}

    public function run(DemoWorkspace $workspace, Closure $callback): mixed
    {
        $previousDatabase = config('database.connections.workspace.database');
        $previousDisk = config('filesystems.disks.demo');
        $previousToken = URL::getDefaultParameters()['shareToken'] ?? null;
        $previousIdentifier = URL::getDefaultParameters()['workspaceIdentifier'] ?? null;
        $directory = $this->files->directory($workspace->getKey());
        abort_unless(is_file($directory.'/content.sqlite'), 410, 'This demo has expired.');

        config(['database.connections.workspace.database' => $directory.'/content.sqlite']);
        config(['filesystems.disks.demo' => ['driver' => 'local', 'root' => $directory.'/media', 'visibility' => 'private', 'throw' => true, 'serve' => false]]);
        DB::purge('workspace');
        Storage::forgetDisk('demo');
        URL::defaults(['shareToken' => $workspace->share_token, 'workspaceIdentifier' => $workspace->getKey()]);

        try {
            return $callback();
        } finally {
            DB::purge('workspace');
            Storage::forgetDisk('demo');
            config(['database.connections.workspace.database' => $previousDatabase, 'filesystems.disks.demo' => $previousDisk]);
            URL::defaults(['shareToken' => $previousToken, 'workspaceIdentifier' => $previousIdentifier]);
        }
    }
}
