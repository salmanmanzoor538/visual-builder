<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('workspace_user', function (Blueprint $table): void {
            $table->foreignUlid('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 32);
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->primary(['workspace_id', 'user_id']);
            $table->index(['user_id', 'role']);
        });

        Schema::create('websites', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->string('name');
            $table->string('slug');
            $table->string('status', 24)->default('draft');
            $table->json('settings')->nullable();
            $table->ulid('active_publish_version_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['workspace_id', 'slug']);
            $table->index(['workspace_id', 'status']);
        });

        Schema::create('pages', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('website_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedBigInteger('revision')->default(1);
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_homepage')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_indexable')->default(true);
            $table->string('seo_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['website_id', 'slug']);
            $table->index(['website_id', 'position']);
        });

        Schema::create('builder_nodes', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('page_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('parent_id')->nullable()->constrained('builder_nodes')->cascadeOnDelete();
            $table->string('type', 64);
            $table->unsignedInteger('position')->default(0);
            $table->json('content')->nullable();
            $table->json('settings')->nullable();
            $table->json('styles')->nullable();
            $table->json('responsive')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
            $table->index(['page_id', 'parent_id', 'position'], 'builder_tree_order_index');
        });

        Schema::create('page_versions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('page_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('revision');
            $table->string('reason', 64)->default('checkpoint');
            $table->json('snapshot');
            $table->timestamps();
            $table->unique(['page_id', 'revision']);
        });

        Schema::create('publish_versions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('website_id')->constrained()->cascadeOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('version');
            $table->string('status', 24)->default('ready');
            $table->json('manifest');
            $table->timestamp('published_at');
            $table->timestamps();
            $table->unique(['website_id', 'version']);
        });

        Schema::create('published_pages', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('publish_version_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('page_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->json('snapshot');
            $table->longText('rendered_html');
            $table->longText('rendered_css')->nullable();
            $table->timestamps();
            $table->unique(['publish_version_id', 'slug']);
        });

        Schema::table('websites', function (Blueprint $table): void {
            $table->foreign('active_publish_version_id')->references('id')->on('publish_versions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('websites', fn (Blueprint $table) => $table->dropForeign(['active_publish_version_id']));
        Schema::dropIfExists('published_pages');
        Schema::dropIfExists('publish_versions');
        Schema::dropIfExists('page_versions');
        Schema::dropIfExists('builder_nodes');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('websites');
        Schema::dropIfExists('workspace_user');
        Schema::dropIfExists('workspaces');
    }
};
