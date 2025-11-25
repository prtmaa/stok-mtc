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
        Schema::table('barang_out', function (Blueprint $table) {
            $table->unsignedBigInteger('divisi_id')->nullable();

            $table->foreign('divisi_id')
                ->references('id')
                ->on('divisi')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_out', function (Blueprint $table) {
            $table->dropForeign(['divisi_id']);
            $table->dropColumn(['divisi_id']);
        });
    }
};
