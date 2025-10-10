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
        Schema::create('shipping_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['fixed', 'weight_based', 'price_based', 'location_based']);
            $table->decimal('base_cost', 8, 2)->default(0);
            $table->decimal('cost_per_kg', 8, 2)->nullable();
            $table->decimal('min_weight', 8, 3)->nullable();
            $table->decimal('max_weight', 8, 3)->nullable();
            $table->decimal('min_order_value', 10, 2)->nullable();
            $table->decimal('max_order_value', 10, 2)->nullable();
            $table->json('locations')->nullable(); // CEPs, estados, cidades
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->integer('estimated_days_min')->default(1);
            $table->integer('estimated_days_max')->default(7);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rules');
    }
};
