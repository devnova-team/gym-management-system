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
        Schema::create('store_sales', function (Blueprint $table) {
            $table->id();
            
             $table->foreignId('gym_id')
                ->constrained('gyms')
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->constrained('store_products')
                ->restrictOnDelete();

                  $table->unsignedInteger('quantity_sold');

            $table->decimal('total_amount', 10, 2);

            $table->date('sale_date');
     

            $table->timestamps();

             $table->index(['gym_id', 'sale_date']);
            $table->index(['product_id', 'sale_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_sales');
    }
};
