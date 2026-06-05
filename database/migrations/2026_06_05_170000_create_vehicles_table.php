<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('plate', 10)->unique();
            $table->string('driver')->nullable();
            $table->string('status')->default('idle'); // moving | idle | stopped | maintenance
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->unsignedSmallInteger('speed')->default(0); // km/h
            $table->unsignedTinyInteger('fuel')->default(100); // %
            $table->unsignedSmallInteger('heading')->default(0); // graus 0-359
            $table->timestamp('last_tick_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
