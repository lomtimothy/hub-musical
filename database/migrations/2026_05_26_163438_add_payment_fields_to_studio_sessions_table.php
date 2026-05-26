<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('studio_sessions', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');
            $table->string('stripe_checkout_session_id')->nullable()->unique()->after('payment_status');
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_checkout_session_id');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('stripe_payment_intent_id');
            $table->timestamp('paid_at')->nullable()->after('amount_paid');
        });
    }

    public function down(): void
    {
        Schema::table('studio_sessions', function (Blueprint $table) {
            $table->dropUnique(['stripe_checkout_session_id']);
            $table->dropColumn([
                'payment_status',
                'stripe_checkout_session_id',
                'stripe_payment_intent_id',
                'amount_paid',
                'paid_at',
            ]);
        });
    }
};
