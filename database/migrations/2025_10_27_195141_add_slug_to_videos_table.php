<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Video;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar se a coluna slug já existe
        if (!Schema::hasColumn('videos', 'slug')) {
            // Adicionar coluna slug como nullable primeiro
            Schema::table('videos', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('title');
            });
        }

        // Gerar slugs para vídeos existentes que não têm slug
        Video::whereNull('slug')->orWhere('slug', '')->get()->each(function ($video) {
            $slug = Str::slug($video->title);
            $originalSlug = $slug;
            $count = 1;

            while (Video::where('slug', $slug)->where('id', '!=', $video->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }

            $video->update(['slug' => $slug]);
        });

        // Tornar o slug obrigatório e único (apenas se ainda não for)
        $indexExists = collect(DB::select("SHOW INDEXES FROM videos WHERE Key_name = 'videos_slug_unique'"))->isNotEmpty();
        
        if (!$indexExists) {
            Schema::table('videos', function (Blueprint $table) {
                $table->string('slug')->nullable(false)->unique()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
