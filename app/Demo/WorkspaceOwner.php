<?php

declare(strict_types=1);

namespace App\Demo;

use App\Models\DemoWorkspace;
use Illuminate\Http\Request;

final class WorkspaceOwner
{
    public function find(Request $request): ?DemoWorkspace
    {
        $token = $request->cookie(config('demo.owner_cookie')) ?? $request->session()->get('demo_owner');
        if (! is_string($token) || preg_match('/\A[a-f0-9]{64}\z/', $token) !== 1) {
            return null;
        }

        return DemoWorkspace::query()->where('owner_hash', hash('sha256', $token))->first();
    }
}
