<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Note: users.trial_ends_at already exists in the schema baseline.
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_used_trial')->default(false)->after('trial_ends_at');
            $table->unsignedTinyInteger('trial_reminder_stage')->nullable()->after('has_used_trial');
        });

        Schema::create('plan_grants', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->string('plan');
            $table->timestamp('started_at');
            $table->timestamp('ends_at')->nullable();
            $table->string('source')->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_grants');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['has_used_trial', 'trial_reminder_stage']);
        });
    }
};
