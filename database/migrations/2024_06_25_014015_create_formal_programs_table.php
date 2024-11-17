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
        Schema::create('formal_programs', function (Blueprint $table) {
            $table->id();
            $table->string('day');
            $table->string('year');
            $table->string('lecture');
            $table->string('doctor');
            $table->string('place');
            $table->time('from', precision: 0);
            $table->time('to', precision: 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formal_programs');
    }
};
