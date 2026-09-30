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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
              

            $table->foreignId('gym_id')
                ->constrained('gyms')
                ->restrictOnDelete();

                 $table->string('name');

            $table->date('purchase_date')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_due')->nullable();

            $table->timestamps();
             

              $table->index(['gym_id', 'next_maintenance_due']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
