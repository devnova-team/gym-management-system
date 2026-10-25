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
        Schema::table('plans', function (Blueprint $table) {
              $table->unsignedInteger('total_sessions')->nullable();

               $table->enum('type', ['limited', 'unlimited'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {

              $table->dropColumn('total_sessions');

               
            $table->enum('type', [
                'daily',
                '3x_week',
                '2x_week',
            ])->change();
             
        });
    }
};
