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
        Schema::table('docuflow_folders', function (Blueprint $table) {
            if (!Schema::hasColumn('docuflow_folders', 'is_starred')) {
                $table->boolean('is_starred')->default(false)->after('parent_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('docuflow_folders', function (Blueprint $table) {
            if (Schema::hasColumn('docuflow_folders', 'is_starred')) {
                $table->dropColumn('is_starred');
            }
        });
    }
};