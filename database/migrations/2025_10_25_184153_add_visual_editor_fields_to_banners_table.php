<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->longText('design_html')->nullable()->after('layers');
            $table->longText('design_css')->nullable()->after('design_html');
            $table->longText('rendered_html')->nullable()->after('design_css');
            $table->longText('rendered_css')->nullable()->after('rendered_html');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['design_html', 'design_css', 'rendered_html', 'rendered_css']);
        });
    }
};
