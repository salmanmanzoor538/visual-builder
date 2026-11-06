<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('disk', 32)->default('public');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('checksum', 64);
            $table->string('alt_text', 500)->nullable();
            $table->text('caption')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['workspace_id', 'created_at']);
            $table->unique(['workspace_id', 'checksum']);
        });

        Schema::create('design_tokens', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('website_id')->constrained()->cascadeOnDelete();
            $table->string('group', 32);
            $table->string('name', 80);
            $table->string('value', 500);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->unique(['website_id', 'group', 'name']);
        });

        Schema::create('cms_collections', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('website_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('status', 24)->default('active');
            $table->unsignedInteger('schema_version')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['website_id', 'slug']);
        });

        Schema::create('cms_fields', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cms_collection_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('key', 80);
            $table->string('type', 32);
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_unique')->default(false);
            $table->json('configuration')->nullable();
            $table->timestamps();
            $table->unique(['cms_collection_id', 'key']);
        });

        Schema::create('cms_items', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cms_collection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('slug');
            $table->string('status', 24)->default('draft');
            $table->unsignedBigInteger('revision')->default(1);
            $table->json('values');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cms_collection_id', 'slug']);
            $table->index(['cms_collection_id', 'status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_items');
        Schema::dropIfExists('cms_fields');
        Schema::dropIfExists('cms_collections');
        Schema::dropIfExists('design_tokens');
        Schema::dropIfExists('assets');
    }
};
