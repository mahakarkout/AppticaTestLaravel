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
        Schema::create('top_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_id');
            $table->unsignedBigInteger('category_id');
            $table->integer('position');
            $table->date('date');
            $table->unsignedInteger('country_id')->default(1);
            $table->timestamps();

            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_positions');
    }
};
