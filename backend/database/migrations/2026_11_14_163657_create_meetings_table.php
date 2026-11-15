<?php

use App\Enums\MeetingStatus;
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
        Schema::create('meetings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->comment('Длительность встречи в минутах');
            $table->timestampTz('date_range_start')->comment('Начало диапазона подбора');
            $table->timestampTz('date_range_end')->comment('Конец диапазона подбора');
            $table->string('timezone', 50)->default('UTC');
            $table->string('status')->default(MeetingStatus::Draft->value);
            $table->uuid('confirmed_slot_id')->nullable();
            $table->timestamps();

            // Индекс для быстрого получения встреч организатора по статусу
            $table->index(['organizer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
