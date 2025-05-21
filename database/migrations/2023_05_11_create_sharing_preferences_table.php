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
        Schema::create('sharing_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('docuflow_users')->onDelete('cascade');
            $table->string('default_permission')->default('view');
            $table->boolean('notify_on_access')->default(true);
            $table->boolean('password_protect_by_default')->default(false);
            $table->integer('default_expiration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sharing_preferences');
    }
};
