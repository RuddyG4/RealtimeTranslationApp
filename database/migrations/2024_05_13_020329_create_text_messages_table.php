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
        Schema::create('text_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id');
            $table->text('content');
            $table->boolean('is_original');
            $table->foreignId('language_id')->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('message_id')->references('id')->on('messages')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->primary(['id', 'message_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('text_messages');
    }
};
