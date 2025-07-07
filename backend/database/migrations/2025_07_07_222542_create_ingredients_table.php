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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string("name", 150);
            $table->text("description");
            $table->decimal("unit_price", 10, 2);
            $table->decimal("quantity", 10, 2);
            $table->decimal("min_quantity", 10, 2);
            $table->decimal("max_quantity", 10, 2);
            $table->boolean("is_additional")->default(false);
            $table->boolean("status")->default(true);
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('unit_measure_id')->constrained('unit_measures');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
