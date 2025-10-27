<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Album;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        // Gerar slugs para álbuns existentes
        Album::all()->each(function ($album) {
            $slug = Str::slug($album->title);
            $originalSlug = $slug;
            $count = 1;

            while (Album::where('slug', $slug)->where('id', '!=', $album->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }

            $album->update(['slug' => $slug]);
        });

        // Tornar o slug obrigatório e único
        Schema::table('albums', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
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
