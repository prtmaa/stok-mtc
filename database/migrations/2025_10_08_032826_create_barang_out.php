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
        Schema::create('barang_out', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->string('divisi', 150)->nullable();
            $table->integer('jumlah');
            $table->string('satuan', 50)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_out');
    }
};
