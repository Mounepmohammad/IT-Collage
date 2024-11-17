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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->integer('doctor_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->time('from', precision: 0)->nullable();
            $table->time('to', precision: 0)->nullable();
            $table->date('date')->nullable();
            $table->string('goal')->nullable();
            $table->string('title')->nullable();
            $table->string('reason')->nullable();
            $table->string('note')->nullable();
            $table->string('state')->default("processing");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
