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
        Schema::table('banners', function (Blueprint $table) {
            $table->json('layers')->nullable()->after('image');
            $table->string('background_color')->nullable()->after('layers');
            $table->string('background_image')->nullable()->after('background_color');
            $table->string('background_position')->default('center center')->after('background_image');
            $table->string('background_size')->default('cover')->after('background_position');
            $table->string('overlay_color')->nullable()->after('background_size');
            $table->integer('overlay_opacity')->default(0)->after('overlay_color');
            $table->integer('banner_height')->default(500)->after('overlay_opacity');
            $table->string('content_alignment')->default('center')->after('banner_height');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn([
                'layers',
                'background_color',
                'background_image',
                'background_position',
                'background_size',
                'overlay_color',
                'overlay_opacity',
                'banner_height',
                'content_alignment',
            ]);
        });
    }
};
