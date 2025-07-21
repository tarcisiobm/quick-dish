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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('zipcode', 10);
            $table->string('street', 255);
            $table->integer('number');
            $table->string('neighborhood', 100);
            $table->string('city', 100);
            $table->string('state', 50);
            $table->string('refference', 255)->nullable();
            $table->string('complement', 255)->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
