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
        Schema::table('blog_configs', function (Blueprint $table) {
            $table->string('login_image')->nullable()->after('default_widget_text_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_configs', function (Blueprint $table) {
            $table->dropColumn('login_image');
        });
    }
};
