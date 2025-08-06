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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title');
            $table->string('duration');
            $table->integer('enrolled')->default(0);
            $table->integer('lectures')->default(0);
            $table->string('skill_level')->nullable();
            $table->string('language')->nullable();
            $table->decimal('fee', 8, 2)->default(0);
            $table->text('description')->nullable();
            $table->text('learning_skill')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
