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
             $table->dateTime('check_out_time')
                ->nullable()
                ->after('check_in_time');

            $table->date('attendance_date')
                ->after('check_out_time')
                ->index();

                  $table->uuid('local_id')
                ->nullable()
                ->unique()
                ->after('attendance_date');

            $table->index(
                ['member_id', 'check_out_time'],
                'attendance_member_checkout_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
             $table->dropUnique(['local_id']);

            $table->dropIndex('attendance_date_index');

            $table->dropIndex('attendance_member_checkout_index');

            $table->dropColumn([
                'check_out_time',
                'attendance_date',
                'local_id',
            ]);
        });
    }
};
