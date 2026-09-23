<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Demo\CreateWorkspace;
use App\Demo\WorkspaceContext;
use App\Demo\WorkspaceFiles;
use App\Models\DemoWorkspace;
use Illuminate\Http\UploadedFile;
use Platno\Models\Asset;
use Platno\Models\Page;
use Platno\Publishing\PagePublisher;
use Tests\TestCase;

final class WorkspaceIsolationTest extends TestCase
{
    public function test_visitors_cannot_read_or_mutate_each_others_documents_or_private_media(): void
    {
        $first = app(CreateWorkspace::class)->create('studio');
        $second = app(CreateWorkspace::class)->create('story');
        $firstWorkspace = $first['workspace'];
        $secondWorkspace = $second['workspace'];
        $editorPrefix = '/workspaces/'.$secondWorkspace->id.'/platno';
        $firstAsset = app(WorkspaceContext::class)->run($firstWorkspace, fn () => Asset::query()->firstOrFail());
        $firstPage = app(WorkspaceContext::class)->run($firstWorkspace, fn () => Page::query()->firstOrFail());
        $secondPage = app(WorkspaceContext::class)->run($secondWorkspace, fn () => Page::query()->firstOrFail());
        $this->assertSame($firstPage->getKey(), $secondPage->getKey());

        $this->withCookie(config('demo.owner_cookie'), $second['ownerToken'])
            ->getJson($editorPrefix.'/media/'.$firstAsset->getKey())->assertNotFound();
        $this->get($editorPrefix.'/pages/'.$secondPage->getKey().'/edit')->assertOk()->assertSee($secondPage->title)->assertDontSee($firstPage->title);
        $this->put($editorPrefix.'/pages/'.$secondPage->getKey(), [
            'revision' => $secondPage->revision,
            'title' => 'Only the second visitor',
            'document' => json_encode($secondPage->document, JSON_THROW_ON_ERROR),
        ])->assertRedirect();
        $this->assertSame($firstPage->title, app(WorkspaceContext::class)->run($firstWorkspace, fn () => Page::query()->firstOrFail()->title));
        $this->assertSame('Only the second visitor', app(WorkspaceContext::class)->run($secondWorkspace, fn () => Page::query()->firstOrFail()->title));
        $this->assertSame(':memory:', config('database.connections.workspace.database'));
    }

    public function test_share_urls_are_read_only_and_expose_only_currently_published_media(): void
    {
        $created = app(CreateWorkspace::class)->create('product');
        $workspace = $created['workspace'];
        $editorPrefix = '/workspaces/'.$workspace->id.'/platno';
        $addresses = app(WorkspaceContext::class)->run($workspace, function (): array {
            $page = Page::query()->firstOrFail();
            $asset = Asset::query()->firstOrFail();

            return ['page' => route('platno.public.show', $page->slug, false), 'asset' => route('platno.public.asset', $asset->getKey(), false), 'pageIdentifier' => $page->getKey(), 'revision' => $page->revision];
        });
        $this->assertStringStartsWith('/share/'.$workspace->share_token.'/pages/', $addresses['page']);
        $this->assertStringStartsWith('/share/'.$workspace->share_token.'/pages/media/', $addresses['asset']);
        $this->get($addresses['page'])->assertNotFound();
        $this->get($addresses['asset'])->assertNotFound();
        $this->withCookie(config('demo.owner_cookie'), $created['ownerToken'])
            ->post($editorPrefix.'/pages/'.$addresses['pageIdentifier'].'/publish', ['revision' => $addresses['revision']])->assertRedirect();
        $this->flushSession();
        $this->withCookie(config('demo.owner_cookie'), '');
        $this->get($addresses['page'])->assertOk();
        $this->get($addresses['asset'])->assertOk();
        $this->post($addresses['page'])->assertStatus(405);

        $this->flushSession();
        $this->withCookie(config('demo.owner_cookie'), $workspace->share_token);
        $this->getJson($editorPrefix)->assertStatus(410);
        $this->postJson($editorPrefix.'/canvas', ['document' => ['version' => 1, 'blocks' => []]])->assertStatus(410);
        $this->postJson($editorPrefix.'/pages/'.$addresses['pageIdentifier'].'/publish', ['revision' => 2])->assertStatus(410);
    }

    public function test_expiration_is_fixed_and_revokes_all_endpoints_before_cleanup(): void
    {
        $created = app(CreateWorkspace::class)->create('studio');
        $workspace = $created['workspace'];
        $editorPrefix = '/workspaces/'.$workspace->id.'/platno';
        $expiresAt = $workspace->expires_at;
        $asset = app(WorkspaceContext::class)->run($workspace, function () use ($workspace): Asset {
            app(PagePublisher::class)->publish($workspace->initial_page_id, 1);

            return Asset::query()->firstOrFail();
        });
        $this->withCookie(config('demo.owner_cookie'), $created['ownerToken']);
        $this->get($editorPrefix)->assertOk();
        $this->assertTrue($workspace->fresh()->expires_at->equalTo($expiresAt));
        $this->travelTo($expiresAt->addSecond());
        $this->get('/studio')->assertStatus(410);
        foreach ([$editorPrefix, $editorPrefix.'/pages/1/edit', $editorPrefix.'/pages/1/history', $editorPrefix.'/pages/1/preview', $editorPrefix.'/media/'.$asset->id, '/share/'.$workspace->share_token.'/pages/atelier-noma'] as $address) {
            $this->getJson($address)->assertStatus(410);
        }
        $this->postJson($editorPrefix.'/canvas', ['document' => ['version' => 1, 'blocks' => []]])->assertStatus(410);
        $this->postJson($editorPrefix.'/pages/1/publish', ['revision' => 2])->assertStatus(410);
        $this->assertDirectoryExists(app(WorkspaceFiles::class)->directory($workspace->id));
        $this->artisan('demo:cleanup')->assertSuccessful();
        $this->assertNull(DemoWorkspace::query()->find($workspace->id));
        $this->assertDirectoryDoesNotExist(app(WorkspaceFiles::class)->directory($workspace->id));
    }

    public function test_cleanup_skips_locked_workspaces_and_active_visitors(): void
    {
        $expired = app(CreateWorkspace::class)->create('studio')['workspace'];
        $active = app(CreateWorkspace::class)->create('product')['workspace'];
        $expired->update(['expires_at' => now()->subMinute()]);
        $files = app(WorkspaceFiles::class);
        $lock = $files->lock($expired->id, false);
        try {
            $this->artisan('demo:cleanup')->assertSuccessful();
            $this->assertNotNull($expired->fresh());
        } finally {
            $files->unlock($lock);
        }
        $this->artisan('demo:cleanup')->assertSuccessful();
        $this->assertNull($expired->fresh());
        $this->assertTrue($active->fresh()->isAvailable());
        $this->assertDirectoryExists($files->directory($active->id));
    }

    public function test_creation_resume_and_restart_preserve_ownership_and_require_explicit_confirmation(): void
    {
        $this->get('/studio')->assertRedirect('/#templates');
        $this->getJson('/workspaces/'.str_repeat('a', 32).'/platno')->assertStatus(410);
        $this->post('/play', ['template' => 'studio'])->assertRedirect('/studio')->assertCookie(config('demo.owner_cookie'));
        $workspace = DemoWorkspace::query()->firstOrFail();
        $this->post('/play', ['template' => 'studio'])->assertRedirect('/studio');
        $this->assertSame(1, DemoWorkspace::query()->count());
        $this->postJson('/restart', ['template' => 'story'])->assertUnprocessable();
        $this->assertTrue($workspace->fresh()->isAvailable());
        config(['demo.max_workspaces' => 1]);
        $this->postJson('/restart', ['template' => 'story', 'confirmed' => '1'])->assertStatus(503);
        $this->assertTrue($workspace->fresh()->isAvailable());
        $this->assertSame(1, DemoWorkspace::query()->count());
        config(['demo.max_workspaces' => 200]);
        $this->post('/restart', ['template' => 'story', 'confirmed' => '1'])->assertRedirect('/studio');
        $this->assertFalse($workspace->fresh()->isAvailable());
        $this->assertSame(2, DemoWorkspace::query()->count());
    }

    public function test_host_quotas_reject_extra_writes_without_changing_content(): void
    {
        $created = app(CreateWorkspace::class)->create('studio');
        $workspace = $created['workspace'];
        $editorPrefix = '/workspaces/'.$workspace->id.'/platno';
        $this->withCookie(config('demo.owner_cookie'), $created['ownerToken']);
        config(['demo.max_pages' => 1, 'demo.max_assets' => 1, 'demo.max_publications' => 0]);
        $this->postJson($editorPrefix.'/pages', ['title' => 'Excess', 'slug' => 'excess', 'document' => ['version' => 1, 'blocks' => []]])->assertUnprocessable();
        $this->postJson($editorPrefix.'/media', ['file' => UploadedFile::fake()->image('upload.jpg')])->assertUnprocessable();
        $this->postJson($editorPrefix.'/media', ['file' => [UploadedFile::fake()->image('array.jpg')]])->assertUnprocessable();
        $this->postJson($editorPrefix.'/pages/1/publish', ['revision' => 1])->assertUnprocessable();
        $this->putJson($editorPrefix.'/pages/1', ['revision' => 1, 'title' => 'Oversized', 'document' => str_repeat('x', config('demo.max_document_bytes') + 1)])->assertUnprocessable();
        app(WorkspaceContext::class)->run($workspace, function (): void {
            $this->assertSame(1, Page::query()->count());
            $this->assertSame(1, Page::query()->firstOrFail()->revision);
            $this->assertNull(Page::query()->firstOrFail()->publication_id);
        });
        config(['demo.max_workspaces' => 1]);
        $this->flushSession();
        $this->withCookie(config('demo.owner_cookie'), '');
        $this->postJson('/play', ['template' => 'story'])->assertStatus(503);
        $this->assertSame(1, DemoWorkspace::query()->count());
    }

    public function test_restarted_workspace_rejects_stale_editor_requests_and_unused_livewire_endpoints(): void
    {
        $this->post('/play', ['template' => 'studio'])->assertRedirect('/studio');
        $previous = DemoWorkspace::query()->firstOrFail();
        $previousPrefix = '/workspaces/'.$previous->id.'/platno';
        $previousPage = app(WorkspaceContext::class)->run($previous, fn () => Page::query()->firstOrFail());
        $previousAsset = app(WorkspaceContext::class)->run($previous, fn () => Asset::query()->firstOrFail());
        $this->get($previousPrefix.'/pages/1/edit')->assertOk()->assertSee($previousPrefix.'/pages/1');
        $this->post('/restart', ['template' => 'product', 'confirmed' => '1'])->assertRedirect('/studio');
        $current = DemoWorkspace::query()->where('id', '!=', $previous->id)->firstOrFail();
        $this->withCookie(config('demo.owner_cookie'), session('demo_owner'));
        $this->putJson($previousPrefix.'/pages/1', ['revision' => 1, 'title' => 'Stale tab overwrites new page', 'document' => $previousPage->document])->assertStatus(410);
        $this->postJson($previousPrefix.'/pages/1/publish', ['revision' => 1])->assertStatus(410);
        $this->postJson($previousPrefix.'/canvas', ['document' => $previousPage->document])->assertStatus(410);
        $this->getJson($previousPrefix.'/media/'.$previousAsset->id)->assertStatus(410);
        $this->postJson('/workspaces/'.$previous->id.'/livewire/update', ['components' => []])->assertStatus(410);
        $this->get('/workspaces/'.$current->id.'/platno/pages/1/edit')->assertOk();
        app(WorkspaceContext::class)->run($current, function (): void {
            $page = Page::query()->firstOrFail();
            $this->assertSame(1, $page->revision);
            $this->assertNotSame('Stale tab overwrites new page', $page->title);
        });
        $this->postJson(route('default-livewire.update'), ['components' => []])->assertNotFound();
        $this->postJson(route('livewire.upload-file'))->assertNotFound();
        $this->getJson(route('livewire.preview-file', ['filename' => 'anything']))->assertNotFound();
    }

    public function test_livewire_bootstrap_and_refresh_keep_the_workspace_specific_update_address(): void
    {
        $created = app(CreateWorkspace::class)->create('studio');
        $workspace = $created['workspace'];
        $this->withCookie(config('demo.owner_cookie'), $created['ownerToken']);
        $response = $this->get('/studio?adapter=livewire')->assertOk();
        $document = new \DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new \DOMXPath($document);
        $script = $xpath->query('//script[@data-update-uri]')->item(0);
        $component = $xpath->query('//*[@*[name()="wire:snapshot"]]')->item(0);
        $this->assertInstanceOf(\DOMElement::class, $script);
        $this->assertInstanceOf(\DOMElement::class, $component);
        $updateAddress = '/workspaces/'.$workspace->id.'/livewire/update';
        $this->assertSame(url($updateAddress), $script->getAttribute('data-update-uri'));
        $this->postJson($updateAddress, ['components' => [[
            'snapshot' => $component->getAttribute('wire:snapshot'),
            'updates' => new \stdClass,
            'calls' => [['path' => '', 'method' => '$refresh', 'params' => []]],
        ]]], ['X-Livewire' => 'true'])->assertOk()->assertJsonStructure(['components' => [['snapshot', 'effects']]]);
        $this->assertSame(':memory:', config('database.connections.workspace.database'));
    }
}
