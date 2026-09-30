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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

               $table->foreignId('gym_id')
                  ->constrained('gyms')
                  ->restrictOnDelete();

             $table->string('name');

          $table->enum('type', [
        'daily',
        '3x_week',
        '2x_week',
    ]);


    $table->unsignedInteger('duration_days');

    $table->decimal('price', 10, 2);

    $table->unsignedInteger('absence_threshold_days');
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
