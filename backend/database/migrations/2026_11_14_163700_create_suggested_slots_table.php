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
        Schema::create('suggested_slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('meeting_id')->constrained('meetings')->cascadeOnDelete();

            $table->timestampTz('start_time');
            $table->timestampTz('end_time');
            $table->unsignedSmallInteger('available_participants_count');
            $table->unsignedSmallInteger('total_participants_count');
            $table->decimal('score_percentage', 5, 2)->comment('Процент явки');
            $table->timestamps();

            // Индекс для поиска по id встречи и проценту явки
            $table->index(['meeting_id', 'score_percentage']);
        });

        // Добавляем внешний ключ от meetings.confirmed_slot_id к suggested_slots.id
        Schema::table('meetings', function (Blueprint $table) {
            $table->foreign('confirmed_slot_id')
                ->references('id')
                ->on('suggested_slots')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting', function (Blueprint $table) {
            $table->dropForeign('confirmed_slot_id');
        });

        Schema::dropIfExists('suggested_slots');
    }
};
