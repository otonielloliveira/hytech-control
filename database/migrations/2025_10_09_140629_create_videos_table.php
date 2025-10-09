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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url'); // URL do YouTube, Vimeo, etc.
            $table->string('video_platform')->default('youtube'); // youtube, vimeo, local
            $table->string('video_id')->nullable(); // ID do vídeo na plataforma
            $table->string('thumbnail_url')->nullable();
            $table->string('duration')->nullable(); // Duração do vídeo
            $table->date('published_date')->nullable();
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'priority']);
            $table->index('category');
            $table->index('published_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
