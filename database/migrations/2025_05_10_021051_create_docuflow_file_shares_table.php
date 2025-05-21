<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocuflowFileSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docuflow_file_shares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id');
            $table->unsignedBigInteger('user_id');
            $table->string('shared_with')->nullable(); // Email address or null for public links
            $table->enum('permission', ['view', 'edit'])->default('view');
            $table->string('token', 64)->unique();
            $table->string('password')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_public')->default(false);
            $table->text('message')->nullable();
            $table->timestamps();
            
            $table->foreign('file_id')->references('id')->on('docuflow_files')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('docuflow_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docuflow_file_shares');
    }
}