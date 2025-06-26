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
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->id(); // Corresponde a 'id INT AUTO_INCREMENT PRIMARY KEY'
            $table->string('name', 150)->nullable(false); // Corresponde a 'name VARCHAR(150) NOT NULL'
            $table->string('description', 255)->nullable(); // Corresponde a 'description VARCHAR(255) NULL'
            $table->timestamps(); // Cria created_at e updated_at DATETIME NOT NULL
            $table->softDeletes(); // Cria deleted_at DATETIME NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_groups');
    }
};