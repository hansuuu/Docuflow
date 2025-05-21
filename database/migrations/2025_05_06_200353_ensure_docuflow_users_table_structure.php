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
        if (!Schema::hasTable('docuflow_users')) {
            Schema::create('docuflow_users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->integer('total_storage')->default(256); // Default storage in MB
                $table->integer('used_storage')->default(0);
                $table->float('used_storage_percentage')->default(0);
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            // Make sure all required columns exist
            Schema::table('docuflow_users', function (Blueprint $table) {
                if (!Schema::hasColumn('docuflow_users', 'total_storage')) {
                    $table->integer('total_storage')->default(256);
                }
                if (!Schema::hasColumn('docuflow_users', 'used_storage')) {
                    $table->integer('used_storage')->default(0);
                }
                if (!Schema::hasColumn('docuflow_users', 'used_storage_percentage')) {
                    $table->float('used_storage_percentage')->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop the table in the down method
    }
};