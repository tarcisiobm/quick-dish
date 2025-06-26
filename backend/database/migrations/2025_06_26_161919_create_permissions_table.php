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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id(); // Corresponde a 'id INT AUTO_INCREMENT PRIMARY KEY'

            // Chave estrangeira para permission_groups
            $table->foreignId('permission_group_id') // Corresponde a 'permission_group_id INT NOT NULL'
                  ->constrained('permission_groups') // Nome da tabela referenciada
                  ->onDelete('no action')
                  ->onUpdate('no action');

            $table->string('name', 150)->nullable(false); // Corresponde a 'name VARCHAR(150) NOT NULL'
            $table->string('description', 255)->nullable(); // Corresponde a 'description VARCHAR(255) NULL'
            $table->timestamps(); // Cria created_at e updated_at
            $table->softDeletes(); // Cria deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};