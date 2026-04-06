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
        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'boolean', 'text'])->default('multiple_choice');
            $table->json('options')->nullable(); // для множественного выбора
            $table->text('correct_answer'); // правильный ответ
            $table->integer('points')->default(1);
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_questions');
    }
};
