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
        Schema::create('reserves', function (Blueprint $table) {
            $table->id();
            $table->string('place');
            $table->integer('doctor_id')->unsigned();
            $table->string('doctor_name');
            $table->time('from', precision: 0);
            $table->time('to', precision: 0);
            $table->date('date');
            $table->string('reason')->nullable();
            $table->string('state')->default("processing");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserves');
    }
};
