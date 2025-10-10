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
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_module_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // Nome da aula
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('type', ['video', 'text', 'quiz', 'assignment', 'live'])->default('video');
            $table->string('video_url')->nullable(); // Link do vídeo
            $table->string('video_platform')->nullable(); // youtube, vimeo, etc
            $table->string('video_id')->nullable(); // ID do vídeo na plataforma
            $table->integer('video_duration')->nullable(); // Duração em segundos
            $table->text('content')->nullable(); // Conteúdo textual
            $table->json('quiz_data')->nullable(); // Dados do quiz
            $table->boolean('is_free')->default(false); // Aula gratuita
            $table->boolean('is_published')->default(false);
            $table->boolean('requires_previous')->default(true); // Requer aula anterior
            $table->integer('min_watch_percentage')->default(80); // % mínimo para marcar como assistida
            $table->integer('sort_order')->default(0);
            $table->timestamp('live_at')->nullable(); // Data/hora da aula ao vivo
            $table->timestamps();
            
            $table->unique(['course_module_id', 'slug']);
            $table->index(['course_module_id', 'sort_order']);
            $table->index(['type', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};
