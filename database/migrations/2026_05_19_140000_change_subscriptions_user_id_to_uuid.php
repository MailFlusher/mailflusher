<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cashier's stock migration declares subscriptions.user_id as bigint, but our
 * users table uses UUID primary keys. The mismatch means Cashier's webhook
 * handler throws on the first INSERT (incorrect integer value in strict mode),
 * which would prevent the first paying user from ever getting their plan.
 *
 * Safe to run today because the subscriptions table is empty in production
 * (no real Stripe subscriptions yet, only retroactive trials). After this,
 * Cashier's parent webhook + cancel/resume/grace-period helpers all work.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'stripe_status']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('user_id', 36)->change();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['user_id', 'stripe_status']);
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'stripe_status']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['user_id', 'stripe_status']);
        });
    }
};
