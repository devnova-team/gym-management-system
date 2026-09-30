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
        Schema::create('store_products', function (Blueprint $table) {
            $table->id();


            $table->foreignId('gym_id')
                ->constrained('gyms')
                ->restrictOnDelete();

            $table->string('name');

            $table->unsignedInteger('quantity')
                ->default(0);

            $table->decimal('unit_price', 10, 2);

            $table->timestamps();

            $table->index(['gym_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_products');
    }
};
