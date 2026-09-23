<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class DemoWorkspace extends Model
{
    protected $connection = 'registry';

    protected $guarded = [];

    protected $hidden = ['owner_hash'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return ['expires_at' => 'immutable_datetime', 'initial_page_id' => 'integer'];
    }

    public function isAvailable(): bool
    {
        return $this->status === 'ready' && $this->expires_at->isFuture();
    }
}
