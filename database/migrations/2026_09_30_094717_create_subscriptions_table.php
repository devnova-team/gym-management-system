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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

                $table->foreignId('member_id')
                ->constrained('members')
                ->restrictOnDelete();
                 

            $table->foreignId('plan_id')
                ->constrained('plans')
                ->restrictOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            $table->string('payment_status');
            $table->string('payment_method');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
