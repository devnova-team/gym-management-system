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
        Schema::create('admin_reminders', function (Blueprint $table) {
            $table->id();


            $table->foreignId('gym_id')
                ->constrained('gyms')
                ->restrictOnDelete();

            $table->string('title');
            $table->date('due_date');
            $table->text('reminder_note')->nullable();

            $table->timestamps();

             $table->index(['gym_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_reminders');
    }
};
