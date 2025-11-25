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
        Schema::table('items', function (Blueprint $table) {
            // Tambahkan kolom baru (bukan change)
            $table->unsignedBigInteger('kategori_id')->nullable()->after('id');
            $table->unsignedBigInteger('satuan_id')->nullable()->after('kategori_id');

            // Tambahkan foreign key
            $table->foreign('kategori_id')
                ->references('id')
                ->on('kategori')
                ->onDelete('set null');

            $table->foreign('satuan_id')
                ->references('id')
                ->on('satuan')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropForeign(['satuan_id']);
            $table->dropColumn(['kategori_id', 'satuan_id']);
        });
    }
};
