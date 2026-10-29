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
        Schema::table('subscriptions', function (Blueprint $table) {
             $table->decimal('amount_paid', 10, 2)->nullable();
            $table->dateTime('paid_at')->nullable();

        $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
              $table->dropIndex(['paid_at']);
             $table->dropColumn(['amount_paid', 'paid_at']);
        });
    }
};
