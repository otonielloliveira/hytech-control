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
        Schema::create('lesson_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // Nome do material
            $table->text('description')->nullable();
            $table->enum('type', ['pdf', 'document', 'presentation', 'worksheet', 'link', 'video', 'audio', 'image'])->default('pdf');
            $table->string('file_path')->nullable(); // Caminho do arquivo
            $table->string('file_name')->nullable(); // Nome original do arquivo
            $table->string('file_size')->nullable(); // Tamanho do arquivo
            $table->string('external_url')->nullable(); // URL externa
            $table->boolean('is_downloadable')->default(true);
            $table->boolean('requires_completion')->default(false); // Requer conclusão da aula
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['course_lesson_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_materials');
    }
};
