<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Demo\CreateWorkspace;
use App\Demo\WorkspaceFiles;
use App\Demo\WorkspaceOwner;
use App\Http\Requests\StartDemoRequest;
use App\Services\DemoContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Platno\Models\Page;

final class DemoController
{
    public function __construct(private readonly WorkspaceOwner $owner, private readonly CreateWorkspace $creator, private readonly WorkspaceFiles $files) {}

    public function home(Request $request): View
    {
        $workspace = $this->owner->find($request);

        return view('home', ['templates' => DemoContent::templates(), 'ownedWorkspace' => $workspace?->isAvailable() ? $workspace : null]);
    }

    public function play(StartDemoRequest $request): RedirectResponse|View
    {
        $workspace = $this->owner->find($request);
        if ($workspace?->isAvailable()) {
            if ($workspace->template !== $request->validated('template')) {
                return view('restart-confirm', ['workspace' => $workspace, 'currentTemplate' => DemoContent::templates()[$workspace->template], 'requestedTemplate' => DemoContent::templates()[$request->validated('template')], 'template' => $request->validated('template')]);
            }

            return redirect()->route('demo.studio');
        }

        return $this->start($request);
    }

    public function restart(StartDemoRequest $request): RedirectResponse
    {
        $workspace = $this->owner->find($request);
        $lock = $workspace === null ? null : $this->files->lock($workspace->getKey(), true);
        abort_if($workspace?->isAvailable() && $lock === null, 503);
        try {
            $response = $this->start($request);
            $workspace?->update(['status' => 'expired']);

            return $response;
        } finally {
            if ($lock !== null) {
                $this->files->unlock($lock);
            }
        }
    }

    public function studio(Request $request): View
    {
        $workspace = $request->attributes->get('demoWorkspace');
        $page = Page::query()->findOrFail($workspace->initial_page_id);
        $adapter = $request->query('adapter', 'native');
        abort_unless(in_array($adapter, ['native', 'vue', 'react', 'livewire'], true), 404);

        return view('studio', [
            'page' => $page,
            'workspace' => $workspace,
            'editorAddress' => route('platno.pages.edit', $page->getKey()),
            'expiresAt' => $workspace->expires_at->toIso8601String(),
            'shareAddress' => route('platno.public.show', $page->slug),
            'templates' => DemoContent::templates(),
            'adapter' => $adapter,
        ]);
    }

    private function start(StartDemoRequest $request): RedirectResponse
    {
        $created = $this->creator->create($request->validated('template'));
        $request->session()->put('demo_owner', $created['ownerToken']);

        return redirect()->route('demo.studio')->withCookie(cookie(
            config('demo.owner_cookie'),
            $created['ownerToken'],
            config('demo.workspace_hours') * 60,
            '/',
            null,
            $request->isSecure(),
            true,
            false,
            'lax',
        ));
    }
}
