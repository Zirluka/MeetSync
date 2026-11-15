<?php

use App\Enums\ParticipantStatus;
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
        Schema::create('participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('meeting_id')->constrained('meetings')->cascadeOnDelete();

            // Если зарегистрирован - ссылка на user_id, если гость - null
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();

            // Уникальный защищенный токен для ссылки-приглашения
            $table->string('access_token', 64)->unique();
            $table->string('status')->default(ParticipantStatus::Invited->value);
            $table->timestampTz('responded_at')->nullable();
            $table->timestamps();

            // Индекс для мгновенного поиска по токену
            $table->index(['meeting_id', 'access_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
