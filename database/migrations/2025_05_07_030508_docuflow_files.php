<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('docuflow_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('docuflow_users')->onDelete('cascade');
            $table->foreignId('folder_id')->nullable()->constrained('docuflow_folders')->nullOnDelete();
            $table->string('name');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_trashed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('docuflow_files');
    }
};