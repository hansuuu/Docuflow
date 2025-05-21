<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('docuflow_users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('avatar')->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('remember_token');
            $table->boolean('is_deleted')->default(false)->after('is_active');
            $table->boolean('is_premium')->default(false)->after('is_deleted');
            $table->boolean('two_factor_enabled')->default(false)->after('is_premium');
            $table->timestamp('password_updated_at')->nullable()->after('two_factor_enabled');
            $table->timestamp('deactivated_at')->nullable()->after('password_updated_at');
            $table->timestamp('auto_reactivate_at')->nullable()->after('deactivated_at');
            $table->string('deactivation_reason')->nullable()->after('auto_reactivate_at');
            $table->timestamp('deleted_at')->nullable()->after('deactivation_reason');
            $table->string('deletion_reason')->nullable()->after('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('docuflow_users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'avatar',
                'is_active',
                'is_deleted',
                'is_premium',
                'two_factor_enabled',
                'password_updated_at',
                'deactivated_at',
                'auto_reactivate_at',
                'deactivation_reason',
                'deleted_at',
                'deletion_reason',
            ]);
        });
    }
};