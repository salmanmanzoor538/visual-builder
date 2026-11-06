<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->string('canonical_url')->nullable()->after('meta_description');
            $table->string('social_image')->nullable()->after('canonical_url');
            $table->boolean('include_in_sitemap')->default(true)->after('is_indexable');
        });

        Schema::create('builder_templates', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('workspace_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('key')->nullable();
            $table->string('name');
            $table->string('category', 64)->default('custom');
            $table->string('scope', 16);
            $table->string('thumbnail')->nullable();
            $table->unsignedInteger('schema_version')->default(1);
            $table->json('snapshot');
            $table->boolean('is_builtin')->default(false);
            $table->timestamps();
            $table->unique(['workspace_id', 'key']);
            $table->index(['workspace_id', 'scope', 'category']);
        });

        Schema::create('global_sections', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('website_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('placement', 16)->nullable();
            $table->unsignedBigInteger('revision')->default(1);
            $table->json('snapshot');
            $table->timestamps();
            $table->index(['website_id', 'placement']);
        });

        Schema::create('forms', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('website_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('notification_email')->nullable();
            $table->string('success_message')->default('Thank you. Your submission was received.');
            $table->boolean('requires_consent')->default(false);
            $table->timestamps();
        });

        Schema::create('form_fields', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('form_id')->constrained()->cascadeOnDelete();
            $table->string('name', 64);
            $table->string('label');
            $table->string('type', 32);
            $table->boolean('required')->default(false);
            $table->json('options')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->unique(['form_id', 'name']);
        });

        Schema::create('form_submissions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('form_id')->constrained()->cascadeOnDelete();
            $table->json('payload');
            $table->string('ip_hash', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('consented_at')->nullable();
            $table->timestamps();
            $table->index(['form_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('global_sections');
        Schema::dropIfExists('builder_templates');
        Schema::table('pages', fn (Blueprint $table) => $table->dropColumn(['canonical_url', 'social_image', 'include_in_sitemap']));
    }
};
