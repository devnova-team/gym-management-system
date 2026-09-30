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
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gym_id')
            ->constrained('gyms')
            ->restrictOnDelete();

            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('photo_url')->nullable();
            $table->date('join_date');

            $table->timestamps();
              $table->softDeletes();  // deleted_at

               $table->index(['gym_id', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
