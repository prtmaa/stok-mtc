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
        Schema::create('barang_in', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->text('code')->unique();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->string('supplier', 150)->nullable();
            $table->integer('jumlah');
            $table->decimal('harga', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_in');
    }
};
