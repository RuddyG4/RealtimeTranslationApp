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
            $table->unsignedBigInteger('id')->primary();
            $table->text('message');
            $table->boolean('is_original');
            $table->foreignId('language_id')->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('id')->references('id')->on('messages')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
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
