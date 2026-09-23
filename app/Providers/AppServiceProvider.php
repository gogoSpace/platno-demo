<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\EnforceDemoQuotas;
use App\Http\Middleware\UseDemoWorkspace;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::setUpdateRoute(fn ($handle) => Route::post('/workspaces/{workspaceIdentifier}/livewire/update', $handle)->middleware(['web', UseDemoWorkspace::class, EnforceDemoQuotas::class]));

        RateLimiter::for('demo-create', fn (Request $request): array => [
            Limit::perMinute(6)->by('minute:'.$request->ip()),
            Limit::perHour(10)->by('hour:'.$request->ip()),
        ]);
    }
}
