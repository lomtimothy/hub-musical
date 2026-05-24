<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studio_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('studio_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('booked_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('notes')->nullable();

            $table->dateTime('starts_at');
            $table->dateTime('ends_at');

            $table->string('status')->default('pending');
            $table->decimal('total_price', 10, 2)->default(0);

            $table->timestamps();

            $table->index(['studio_id', 'starts_at']);
            $table->index(['booked_by', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studio_sessions');
    }
};
