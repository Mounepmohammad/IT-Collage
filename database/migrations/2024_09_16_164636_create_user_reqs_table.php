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
        Schema::create('user_reqs', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->unsigned();
            $table->string('student_name');
            $table->string('collage_number')->nullable();
            $table->string('photo1');
            $table->string('photo2');
            $table->string('photo3');
            $table->string('photo4')->nullable();
            $table->string('photo5')->nullable();
            $table->string('state')->nullable();;
            $table->string('descreption')->nullable();;
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reqs');
    }
};
