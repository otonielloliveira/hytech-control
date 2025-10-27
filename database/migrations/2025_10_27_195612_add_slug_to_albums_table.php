<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Album;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar se a coluna slug já existe
        if (!Schema::hasColumn('albums', 'slug')) {
            // Adicionar coluna slug como nullable primeiro
            Schema::table('albums', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('title');
            });
        }

        // Gerar slugs para álbuns existentes que não têm slug
        Album::whereNull('slug')->orWhere('slug', '')->get()->each(function ($album) {
            $slug = Str::slug($album->title);
            $originalSlug = $slug;
            $count = 1;

            while (Album::where('slug', $slug)->where('id', '!=', $album->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }

            $album->update(['slug' => $slug]);
        });

        // Tornar o slug obrigatório e único (apenas se ainda não for)
        $indexExists = collect(DB::select("SHOW INDEXES FROM albums WHERE Key_name = 'albums_slug_unique'"))->isNotEmpty();
        
        if (!$indexExists) {
            Schema::table('albums', function (Blueprint $table) {
                $table->string('slug')->nullable(false)->unique()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
