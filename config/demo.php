<?php

declare(strict_types=1);

return [
    'storage_path' => env('DEMO_STORAGE_PATH', storage_path('app/demo')),
    'workspace_hours' => (int) env('DEMO_WORKSPACE_HOURS', 24),
    'max_workspaces' => (int) env('DEMO_MAX_WORKSPACES', 200),
    'max_pages' => 3,
    'max_assets' => 20,
    'max_asset_bytes' => 15 * 1024 * 1024,
    'max_publications' => 30,
    'max_document_bytes' => 200 * 1024,
    'owner_cookie' => 'platno_demo_owner',
];
