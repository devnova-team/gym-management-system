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
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
              

            $table->foreignId('staff_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->dateTime('check_in_time');
            $table->dateTime('check_out_time')->nullable();

            $table->timestamps();

             $table->index(['staff_id', 'check_in_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_attendance');
    }
};
