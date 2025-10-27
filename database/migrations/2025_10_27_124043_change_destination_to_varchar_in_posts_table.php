<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            // Alterar de ENUM para VARCHAR para permitir seções dinâmicas
            DB::statement("ALTER TABLE `blog_posts` MODIFY `destination` VARCHAR(255) NOT NULL DEFAULT 'artigos'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            // Reverter para ENUM com os valores originais
            DB::statement("ALTER TABLE `blog_posts` MODIFY `destination` ENUM('artigos', 'peticoes', 'ultimas_noticias', 'noticias_mundiais', 'noticias_nacionais', 'noticias_regionais', 'politica', 'economia') NOT NULL DEFAULT 'artigos'");
        });
    }
};
