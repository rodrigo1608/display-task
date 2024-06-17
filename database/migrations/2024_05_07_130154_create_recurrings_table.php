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
        Schema::create('recurrings', function (Blueprint $table) {

            $table->id();

            $table->date('specific_date')->nullable();

            $table->enum('sunday', ['true', 'false'])->default('false');

            $table->enum('monday', ['true', 'false'])->default('false');

            $table->enum('tuesday', ['true', 'false'])->default('false');

            $table->enum('wednesday', ['true', 'false'])->default('false');

            $table->enum('thursday', ['true', 'false'])->default('false');

            $table->enum('friday', ['true', 'false'])->default('false');

            $table->enum('saturday', ['true', 'false'])->default('false');

            $table->enum('available', ['true', 'false'])->default('true');

            $table->unsignedBigInteger('reminder_id')->nullable();
            $table->foreign('reminder_id')->references('id')->on('reminders')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurrings');
    }
};
