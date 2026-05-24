<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studio_session_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('studio_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('instrument')->nullable();
            $table->decimal('payment_split', 5, 2)->default(0);

            $table->timestamps();

            $table->unique(['studio_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studio_session_user');
    }
};
