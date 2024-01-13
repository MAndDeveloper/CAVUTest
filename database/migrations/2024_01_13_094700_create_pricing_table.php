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
        Schema::create('pricing', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->float('weekday_cost');
            $table->float('weekend_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing');
    }
};
