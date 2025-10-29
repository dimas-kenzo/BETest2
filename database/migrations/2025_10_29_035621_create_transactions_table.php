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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->date('date');
            $table->enum('type', ['Pembelian', 'Penjualan']);
            $table->integer('qty');
            $table->decimal('price', 15, 4);
            $table->decimal('cost', 15, 4)->nullable();
            $table->decimal('total_cost', 15, 4)->nullable();
            $table->integer('qty_balance')->nullable();
            $table->decimal('value_balance', 15, 4)->nullable();
            $table->decimal('hpp', 15, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
