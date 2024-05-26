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
        Schema::create('translated_audios', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('audio', 255);

            $table->foreign('id')->references('id')->on('audio_messages')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translated_audios');
    }
};
