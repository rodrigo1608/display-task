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
        Schema::create('notification_times', function (Blueprint $table) {

            $table->id();

            $table->time('custom_time')->nullable();

            $table->enum('half_an_hour_before', ['true', 'false'])->default('false');

            $table->enum('one_hour_before', ['true', 'false'])->default('false');

            $table->enum('two_hours_before', ['true', 'false'])->default('false');

            $table->enum('one_day_earlier', ['true', 'false'])->default('false');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('reminder_id');
            $table->foreign('reminder_id')->references('id')->on('reminders')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_times');
    }
};
