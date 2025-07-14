<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('guest_name', 150)->nullable();
            $table->string('guest_email', 150)->nullable();
            $table->string('guest_phone', 20)->nullable();

            // Chaves estrangeiras, agora referenciando tables e users
            $table->foreignId('table_id')->constrained('tables'); // Garante que 'tables' existe antes
            $table->foreignId('user_id')->nullable()->constrained('users');

            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('guests_count');
            $table->string('notes', 255)->nullable();
            $table->boolean('status')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};