<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CREATE TABLE payment_types (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     name VARCHAR(150) NOT NULL,
//     status TINYINT(1) NOT NULL DEFAULT 1,
//     created_at DATETIME NOT NULL,
//     updated_at DATETIME NOT NULL,
//     deleted_at DATETIME NULL
// );

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_types');
    }
};
