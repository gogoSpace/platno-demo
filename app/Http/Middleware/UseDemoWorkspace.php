<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Demo\WorkspaceContext;
use App\Demo\WorkspaceFiles;
use App\Demo\WorkspaceOwner;
use App\Models\DemoWorkspace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class UseDemoWorkspace
{
    public function __construct(private readonly WorkspaceOwner $owner, private readonly WorkspaceFiles $files, private readonly WorkspaceContext $context) {}

    public function handle(Request $request, Closure $next, string $access = 'owner'): Response
    {
        if ($access === 'public') {
            abort_unless($request->isMethod('GET') || $request->isMethod('HEAD'), 405);
            $token = $request->route('shareToken');
            abort_unless(is_string($token) && preg_match('/\A[a-f0-9]{48}\z/', $token) === 1, 404);
            $workspace = DemoWorkspace::query()->where('share_token', $token)->first();
            $request->route()->forgetParameter('shareToken');
        } else {
            $workspace = $this->owner->find($request);
            $identifier = $request->route('workspaceIdentifier');
            if ($identifier !== null) {
                abort_unless($workspace !== null && is_string($identifier) && hash_equals($workspace->getKey(), $identifier), 410, 'This editor belongs to an older demo. Open your current workspace.');
                $request->route()->forgetParameter('workspaceIdentifier');
            }
        }
        if ($workspace === null) {
            if ($request->isMethod('GET') && $request->routeIs('demo.studio') && $request->cookie(config('demo.owner_cookie')) === null && ! $request->session()->has('demo_owner')) {
                return redirect('/#templates')->with('status', 'Choose a template to open your own editor.');
            }

            return $request->expectsJson()
                ? response()->json(['message' => 'Start a new demo to open the editor.'], 410)
                : response()->view('expired', ['shared' => $access === 'public'], 410);
        }
        $lock = $this->files->lock($workspace->getKey(), ! $request->isMethodSafe());
        abort_if($lock === null, $workspace->isAvailable() ? 503 : 410, 'The demo is unavailable. Please try again.');

        try {
            $workspace = $workspace->fresh();
            if ($workspace === null || ! $workspace->isAvailable()) {
                return $request->expectsJson()
                    ? response()->json(['message' => 'This demo has expired. Start a new one to continue.'], 410)
                    : response()->view('expired', ['shared' => $access === 'public'], 410);
            }
            $request->attributes->set('demoWorkspace', $workspace);

            return $this->context->run($workspace, function () use ($request, $next): Response {
                $response = $next($request);
                $response->headers->set('Cache-Control', 'private, no-store');
                $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
                $response->headers->set('Referrer-Policy', 'same-origin');

                return $response;
            });
        } finally {
            $this->files->unlock($lock);
        }
    }
}
