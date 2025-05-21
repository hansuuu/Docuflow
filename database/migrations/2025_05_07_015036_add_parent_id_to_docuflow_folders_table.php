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
            if (!Schema::hasColumn('docuflow_folders', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('user_id');
                $table->foreign('parent_id')->references('id')->on('docuflow_folders')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('docuflow_folders', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};