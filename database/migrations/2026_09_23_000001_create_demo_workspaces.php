<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'registry';

    public function up(): void
    {
        Schema::connection('registry')->create('demo_workspaces', function (Blueprint $table): void {
            $table->string('id', 32)->primary();
            $table->string('owner_hash', 64)->unique();
            $table->string('share_token', 48)->unique();
            $table->string('template', 30);
            $table->string('status', 20)->default('provisioning');
            $table->unsignedBigInteger('initial_page_id')->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('registry')->dropIfExists('demo_workspaces');
    }
};
