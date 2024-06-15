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
        Schema::create('group_chat_members', function (Blueprint $table) {
            $table->foreignId('chat_id');
            $table->foreignId('user_id');
            $table->boolean('is_admin');
            $table->foreign(['user_id', 'chat_id'])
                ->references(['user_id', 'chat_id'])
                ->on('chat_members');
            $table->primary(['user_id', 'chat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_chat_members');
    }
};
