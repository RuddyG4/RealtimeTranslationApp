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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id');
            $table->foreignId('user_id');
            $table->smallInteger('type')->default(0); // 0 = text, 1 = audio, 2 = image, 3 = video, 4 = document
            $table->timestamp('sent_at')->useCurrent();
            $table->foreign(['user_id', 'chat_id'])
                ->references(['user_id', 'chat_id'])
                ->on('chat_members');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
