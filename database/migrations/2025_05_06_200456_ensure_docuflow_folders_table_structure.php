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
        if (!Schema::hasTable('docuflow_folders')) {
            Schema::create('docuflow_folders', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->boolean('is_starred')->default(false);
                $table->boolean('is_trashed')->default(false);
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('docuflow_users')->onDelete('cascade');
                $table->foreign('parent_id')->references('id')->on('docuflow_folders')->onDelete('cascade');
            });
        } else {
            // Make sure all required columns exist
            Schema::table('docuflow_folders', function (Blueprint $table) {
                if (!Schema::hasColumn('docuflow_folders', 'is_trashed')) {
                    $table->boolean('is_trashed')->default(false);
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