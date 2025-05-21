<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTrashedToFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docuflow_folders', function (Blueprint $table) {
            if (!Schema::hasColumn('folders', 'is_trashed')) {
                $table->boolean('is_trashed')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docuflow_folders', function (Blueprint $table) {
            if (Schema::hasColumn('docuflow_folders', 'is_trashed')) {
                $table->dropColumn('is_trashed');
            }
        });
    }
}