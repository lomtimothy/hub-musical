<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('studio_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('version')->default('1.0');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['studio_session_id', 'uploaded_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
