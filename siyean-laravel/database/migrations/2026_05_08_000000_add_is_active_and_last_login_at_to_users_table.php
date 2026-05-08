<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 2 of user-management hardening: lifecycle columns.
 *
 *  - is_active     gate logins for disabled accounts without deleting the row
 *                  (preserves order history, audit, etc.).
 *  - last_login_at observability for ops + the upcoming admin UI.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_active')->default(true)->after('password');
            $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['is_active', 'last_login_at']);
        });
    }
};
