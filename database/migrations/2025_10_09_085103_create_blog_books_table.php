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
        Schema::create('blog_books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('amazon_link')->nullable();
            $table->string('goodreads_link')->nullable();
            $table->string('pdf_link')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->string('isbn')->nullable();
            $table->year('publication_year')->nullable();
            $table->string('category');
            $table->text('review')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1);
            $table->timestamps();
            
            $table->index(['is_active', 'priority']);
            $table->index(['category', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_books');
    }
};
