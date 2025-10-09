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
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->enum('destination', [
                'artigos',
                'peticoes', 
                'ultimas_noticias',
                'noticias_mundiais',
                'noticias_nacionais',
                'noticias_regionais',
                'politica',
                'economia',
                'amigos_apoiadores'
            ])->default('artigos')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->enum('destination', [
                'artigos',
                'peticoes', 
                'ultimas_noticias',
                'noticias_mundiais',
                'noticias_nacionais',
                'noticias_regionais',
                'politica',
                'economia'
            ])->default('artigos')->change();
        });
    }
};
