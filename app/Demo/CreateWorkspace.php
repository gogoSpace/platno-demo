<?php

declare(strict_types=1);

namespace App\Demo;

use App\Models\DemoWorkspace;
use App\Services\DemoContent;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Platno\Platno;
use ReflectionClass;
use Throwable;

final class CreateWorkspace
{
    public function __construct(private readonly WorkspaceFiles $files, private readonly WorkspaceContext $context, private readonly DemoContent $content) {}

    /** @return array{workspace: DemoWorkspace, ownerToken: string} */
    public function create(string $template): array
    {
        return Cache::store('file')->lock('demo-workspace-creation', 60)->block(5, function () use ($template): array {
            abort_if(DemoWorkspace::query()->count() >= config('demo.max_workspaces'), 503, 'The playground is full. Please try again shortly.');
            $ownerToken = bin2hex(random_bytes(32));
            $workspace = DemoWorkspace::query()->create([
                'id' => bin2hex(random_bytes(16)),
                'owner_hash' => hash('sha256', $ownerToken),
                'share_token' => bin2hex(random_bytes(24)),
                'template' => $template,
                'status' => 'provisioning',
                'expires_at' => now()->addHours(config('demo.workspace_hours')),
            ]);

            $lock = null;
            try {
                $this->files->prepare($workspace->getKey());
                $lock = $this->files->lock($workspace->getKey(), true);
                if ($lock === null) {
                    throw new \RuntimeException('Cannot prepare the workspace lock.');
                }
                $this->context->run($workspace, function () use ($workspace, $template): void {
                    $packageRoot = dirname((new ReflectionClass(Platno::class))->getFileName(), 2);
                    $exitCode = Artisan::call('migrate', ['--database' => 'workspace', '--path' => $packageRoot.'/database/migrations', '--realpath' => true, '--force' => true]);
                    if ($exitCode !== 0) {
                        throw new \RuntimeException('Cannot prepare a demo database.');
                    }
                    $page = $this->content->seed($template);
                    $workspace->update(['status' => 'ready', 'initial_page_id' => $page->getKey()]);
                });
            } catch (Throwable $exception) {
                $this->files->delete($workspace->getKey());
                $workspace->delete();
                throw $exception;
            } finally {
                if ($lock !== null) {
                    $this->files->unlock($lock);
                }
            }

            return ['workspace' => $workspace, 'ownerToken' => $ownerToken];
        });
    }
}
