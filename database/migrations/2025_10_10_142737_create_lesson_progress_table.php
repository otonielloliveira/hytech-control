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
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_lesson_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->integer('watch_percentage')->default(0); // % assistido do vídeo
            $table->integer('watch_duration')->default(0); // Tempo assistido em segundos
            $table->json('quiz_answers')->nullable(); // Respostas do quiz
            $table->decimal('quiz_score', 5, 2)->nullable(); // Pontuação do quiz
            $table->text('notes')->nullable(); // Anotações do aluno
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_watched_at')->nullable();
            $table->timestamps();
            
            $table->unique(['course_enrollment_id', 'course_lesson_id']);
            $table->index(['is_completed', 'completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};
