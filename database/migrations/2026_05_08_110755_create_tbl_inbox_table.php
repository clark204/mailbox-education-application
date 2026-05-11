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
        Schema::create('tbl_inbox', function (Blueprint $table) {
            $table->id();
            $table->string('inbox_id')->unique();
            $table->foreignId('compose_id')->constrained('tbl_compose')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['sender', 'receiver']);
            $table->boolean('is_read')->default(false);
            $table->boolean('is_important')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_trash')->default(false);
            $table->boolean('is_draft')->default(false);
            $table->timestamp('trashed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_inbox');
    }
};
