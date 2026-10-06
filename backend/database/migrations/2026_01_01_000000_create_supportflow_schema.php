<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("organizations", function (Blueprint $t) {
            $t->id();
            $t->string("name");
            $t->string("slug")->unique();
            $t->string("timezone")->default("Europe/Madrid");
            $t->jsonb("settings")->nullable();
            $t->timestamps();
        });
        Schema::create("users", function (Blueprint $t) {
            $t->id();
            $t->foreignId("organization_id")->constrained()->cascadeOnDelete();
            $t->string("name");
            $t->string("email")->unique();
            $t->timestamp("email_verified_at")->nullable();
            $t->string("password");
            $t->boolean("active")->default(true)->index();
            $t->rememberToken();
            $t->timestamps();
            $t->index(["organization_id", "email"]);
        });
        Schema::create("password_reset_tokens", function (Blueprint $t) {
            $t->string("email")->primary();
            $t->string("token");
            $t->timestamp("created_at")->nullable();
        });
        Schema::create("sessions", function (Blueprint $t) {
            $t->string("id")->primary();
            $t->foreignId("user_id")->nullable()->index();
            $t->string("ip_address", 45)->nullable();
            $t->text("user_agent")->nullable();
            $t->longText("payload");
            $t->integer("last_activity")->index();
        });
        Schema::create("personal_access_tokens", function (Blueprint $t) {
            $t->id();
            $t->morphs("tokenable");
            $t->string("name");
            $t->string("token", 64)->unique();
            $t->text("abilities")->nullable();
            $t->timestamp("last_used_at")->nullable();
            $t->timestamp("expires_at")->nullable()->index();
            $t->timestamps();
        });
        Schema::create("roles", function (Blueprint $t) {
            $t->id();
            $t->string("name");
            $t->string("slug")->unique();
            $t->timestamps();
        });
        Schema::create("permissions", function (Blueprint $t) {
            $t->id();
            $t->string("name");
            $t->string("slug")->unique();
        });
        Schema::create("role_user", function (Blueprint $t) {
            $t->foreignId("role_id")->constrained()->cascadeOnDelete();
            $t->foreignId("user_id")->constrained()->cascadeOnDelete();
            $t->primary(["role_id", "user_id"]);
        });
        Schema::create("permission_role", function (Blueprint $t) {
            $t->foreignId("permission_id")->constrained()->cascadeOnDelete();
            $t->foreignId("role_id")->constrained()->cascadeOnDelete();
            $t->primary(["permission_id", "role_id"]);
        });
        Schema::create("teams", function (Blueprint $t) {
            $t->id();
            $t->foreignId("organization_id")->constrained()->cascadeOnDelete();
            $t->string("name");
            $t->string("slug");
            $t->boolean("active")->default(true);
            $t->timestamps();
            $t->unique(["organization_id", "slug"]);
        });
        Schema::create("team_user", function (Blueprint $t) {
            $t->foreignId("team_id")->constrained()->cascadeOnDelete();
            $t->foreignId("user_id")->constrained()->cascadeOnDelete();
            $t->boolean("is_lead")->default(false);
            $t->timestamps();
            $t->primary(["team_id", "user_id"]);
        });
        Schema::create("categories", function (Blueprint $t) {
            $t->id();
            $t->foreignId("organization_id")->constrained()->cascadeOnDelete();
            $t->string("name");
            $t->string("slug");
            $t->boolean("active")->default(true);
            $t->timestamps();
            $t->unique(["organization_id", "slug"]);
        });
        Schema::create("priorities", function (Blueprint $t) {
            $t->id();
            $t->foreignId("organization_id")->constrained()->cascadeOnDelete();
            $t->string("name");
            $t->string("slug");
            $t->unsignedTinyInteger("level");
            $t->string("color", 20);
            $t->boolean("active")->default(true);
            $t->timestamps();
            $t->unique(["organization_id", "slug"]);
        });
        Schema::create("sla_rules", function (Blueprint $t) {
            $t->id();
            $t->foreignId("organization_id")->constrained()->cascadeOnDelete();
            $t->foreignId("priority_id")->constrained()->cascadeOnDelete();
            $t->string("name");
            $t->unsignedInteger("first_response_minutes");
            $t->unsignedInteger("resolution_minutes");
            $t->boolean("active")->default(true);
            $t->timestamps();
            $t->index(["organization_id", "active"]);
        });
        Schema::create("tickets", function (Blueprint $t) {
            $t->id();
            $t->foreignId("organization_id")->constrained()->cascadeOnDelete();
            $t->string("code")->unique();
            $t->foreignId("requester_id")
                ->constrained("users")
                ->restrictOnDelete();
            $t->foreignId("category_id")->constrained()->restrictOnDelete();
            $t->foreignId("priority_id")->constrained()->restrictOnDelete();
            $t->foreignId("team_id")->nullable()->constrained()->nullOnDelete();
            $t->foreignId("assignee_id")
                ->nullable()
                ->constrained("users")
                ->nullOnDelete();
            $t->string("title", 180);
            $t->text("description");
            $t->string("status", 20)->default("nuevo");
            $t->text("resolution")->nullable();
            $t->timestampTz("first_response_at")->nullable();
            $t->timestampTz("resolved_at")->nullable();
            $t->timestampTz("closed_at")->nullable();
            $t->timestampTz("sla_due_at")->nullable()->index();
            $t->timestampsTz();
            $t->softDeletesTz();
            $t->index(["organization_id", "status", "updated_at"]);
            $t->index(["organization_id", "requester_id"]);
            $t->index(["organization_id", "assignee_id", "status"]);
        });
        Schema::create("ticket_comments", function (Blueprint $t) {
            $t->id();
            $t->foreignId("ticket_id")->constrained()->cascadeOnDelete();
            $t->foreignId("user_id")->constrained()->restrictOnDelete();
            $t->text("body");
            $t->boolean("is_internal")->default(false)->index();
            $t->timestampsTz();
        });
        Schema::create("attachments", function (Blueprint $t) {
            $t->id();
            $t->foreignId("ticket_id")->constrained()->cascadeOnDelete();
            $t->foreignId("comment_id")
                ->nullable()
                ->constrained("ticket_comments")
                ->cascadeOnDelete();
            $t->foreignId("uploaded_by")
                ->constrained("users")
                ->restrictOnDelete();
            $t->string("disk")->default("local");
            $t->string("path");
            $t->string("original_name");
            $t->string("mime_type", 120);
            $t->unsignedBigInteger("size");
            $t->string("sha256", 64);
            $t->timestampsTz();
        });
        Schema::create("ticket_assignments", function (Blueprint $t) {
            $t->id();
            $t->foreignId("ticket_id")->constrained()->cascadeOnDelete();
            $t->foreignId("assigned_by")
                ->nullable()
                ->constrained("users")
                ->nullOnDelete();
            $t->foreignId("user_id")
                ->nullable()
                ->constrained("users")
                ->nullOnDelete();
            $t->foreignId("team_id")->nullable()->constrained()->nullOnDelete();
            $t->string("reason")->nullable();
            $t->timestampTz("created_at")->useCurrent();
            $t->index(["ticket_id", "created_at"]);
        });
        Schema::create("audit_events", function (Blueprint $t) {
            $t->id();
            $t->foreignId("organization_id")->constrained()->cascadeOnDelete();
            $t->foreignId("ticket_id")
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $t->foreignId("actor_id")
                ->nullable()
                ->constrained("users")
                ->nullOnDelete();
            $t->string("action")->index();
            $t->jsonb("old_values")->nullable();
            $t->jsonb("new_values")->nullable();
            $t->ipAddress("ip_address")->nullable();
            $t->string("user_agent", 500)->nullable();
            $t->timestampTz("created_at")->useCurrent();
            $t->index(["organization_id", "created_at"]);
        });
        Schema::create("notifications", function (Blueprint $t) {
            $t->uuid("id")->primary();
            $t->string("type");
            $t->morphs("notifiable");
            $t->text("data");
            $t->timestamp("read_at")->nullable();
            $t->timestamps();
        });
        Schema::create("knowledge_categories", function (Blueprint $t) {
            $t->id();
            $t->foreignId("organization_id")->constrained()->cascadeOnDelete();
            $t->string("name");
            $t->string("slug");
            $t->timestamps();
            $t->unique(["organization_id", "slug"]);
        });
        Schema::create("knowledge_articles", function (Blueprint $t) {
            $t->id();
            $t->foreignId("organization_id")->constrained()->cascadeOnDelete();
            $t->foreignId("category_id")
                ->constrained("knowledge_categories")
                ->cascadeOnDelete();
            $t->foreignId("author_id")
                ->constrained("users")
                ->restrictOnDelete();
            $t->string("title");
            $t->string("slug");
            $t->text("excerpt")->nullable();
            $t->longText("body");
            $t->string("status", 20)->default("draft");
            $t->timestampTz("published_at")->nullable();
            $t->unsignedBigInteger("views")->default(0);
            $t->timestampsTz();
            $t->unique(["organization_id", "slug"]);
            $t->index(["organization_id", "status", "published_at"]);
        });
        Schema::create("cache", function (Blueprint $t) {
            $t->string("key")->primary();
            $t->mediumText("value");
            $t->integer("expiration");
        });
        Schema::create("cache_locks", function (Blueprint $t) {
            $t->string("key")->primary();
            $t->string("owner");
            $t->integer("expiration");
        });
        Schema::create("jobs", function (Blueprint $t) {
            $t->id();
            $t->string("queue")->index();
            $t->longText("payload");
            $t->unsignedTinyInteger("attempts");
            $t->unsignedInteger("reserved_at")->nullable();
            $t->unsignedInteger("available_at");
            $t->unsignedInteger("created_at");
        });
        Schema::create("job_batches", function (Blueprint $t) {
            $t->string("id")->primary();
            $t->string("name");
            $t->integer("total_jobs");
            $t->integer("pending_jobs");
            $t->integer("failed_jobs");
            $t->longText("failed_job_ids");
            $t->mediumText("options")->nullable();
            $t->integer("cancelled_at")->nullable();
            $t->integer("created_at");
            $t->integer("finished_at")->nullable();
        });
        Schema::create("failed_jobs", function (Blueprint $t) {
            $t->id();
            $t->string("uuid")->unique();
            $t->text("connection");
            $t->text("queue");
            $t->longText("payload");
            $t->longText("exception");
            $t->timestamp("failed_at")->useCurrent();
        });
    }
    public function down(): void
    {
        Schema::dropAllTables();
    }
};
