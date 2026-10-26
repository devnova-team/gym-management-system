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
        Schema::table('attendance', function (Blueprint $table) {
           
             
    
         $table->foreignId('subscription_id')
                ->nullable()
                ->constrained('subscriptions')
                ->restrictOnDelete();

            $table->index([
                'subscription_id',
                'attendance_date'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
              $table->dropIndex([
                'subscription_id',
                'attendance_date'
            ]);

            $table->dropConstrainedForeignId('subscription_id');
        });
    }
};
