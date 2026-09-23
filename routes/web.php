<?php

declare(strict_types=1);

use App\Http\Controllers\DemoController;
use App\Http\Middleware\EnforceDemoQuotas;
use App\Http\Middleware\UseDemoWorkspace;
use Illuminate\Support\Facades\Route;
use Platno\Platno;

Route::get('/', [DemoController::class, 'home'])->name('demo.home');
Route::view('/developers', 'developers')->name('demo.developers');
Route::post('/play', [DemoController::class, 'play'])->middleware('throttle:demo-create')->block(10, 10)->name('demo.play');
Route::post('/restart', [DemoController::class, 'restart'])->middleware('throttle:demo-create')->block(10, 10)->name('demo.restart');
Route::middleware([UseDemoWorkspace::class, EnforceDemoQuotas::class])->group(function (): void {
    Route::get('/studio', [DemoController::class, 'studio'])->name('demo.studio');
    Route::prefix('workspaces/{workspaceIdentifier}')->group(function (): void {
        Platno::editorRoutes();
    });
});
Route::prefix('share/{shareToken}')->middleware(UseDemoWorkspace::class.':public')->group(function (): void {
    Platno::publicRoutes();
});
