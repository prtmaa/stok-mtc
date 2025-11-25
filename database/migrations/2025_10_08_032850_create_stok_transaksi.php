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
        Schema::create('stok_transaksi', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->enum('tipe', ['IN', 'OUT']);
            $table->integer('jumlah');
            $table->integer('stok_akhir');
            $table->string('referensi', 50)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_transaksi');
    }
};
