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
        Schema::create('inventory_items', function (Blueprint $table) {

                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('unit'); // kg, pieces, liters, etc.
                $table->decimal('current_stock', 10, 2);
                $table->decimal('minimum_stock', 10, 2);
                $table->decimal('unit_cost', 8, 2);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
