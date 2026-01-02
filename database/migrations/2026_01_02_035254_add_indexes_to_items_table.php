<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {

            // Search & lookup
            $table->index('nama_barang');
            $table->index('tipe_barang');
            $table->index('barcode');

            // Stock & soft delete
            $table->index('stok');
            $table->index('deleted_at');

            // Sorting / laporan
            $table->index('tanggal_order');

            // Composite index (paling sering kepakai)
            $table->index(['stok', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {

            $table->dropIndex(['nama_barang']);
            $table->dropIndex(['tipe_barang']);
            $table->dropIndex(['barcode']);
            $table->dropIndex(['stok']);
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['tanggal_order']);
            $table->dropIndex(['stok', 'deleted_at']);
        });
    }
};
