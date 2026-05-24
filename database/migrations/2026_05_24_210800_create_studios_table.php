<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('address');
            $table->string('city')->default('Guadalajara');
            $table->string('state')->default('Jalisco');
            $table->decimal('hourly_rate', 10, 2);
            $table->unsignedTinyInteger('capacity');
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studios');
    }
};
