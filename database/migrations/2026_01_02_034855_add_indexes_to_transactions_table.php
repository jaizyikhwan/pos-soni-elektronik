<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {

            // Index untuk filter & search
            $table->index('tanggal');
            $table->index('nama_pembeli');
            $table->index('status');

            // Index untuk relasi
            $table->index('item_id');

            // (Opsional tapi direkomendasikan)
            // Index gabungan untuk query dashboard
            $table->index(['status', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {

            // Drop single indexes
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['nama_pembeli']);
            $table->dropIndex(['status']);
            $table->dropIndex(['item_id']);

            // Drop composite index
            $table->dropIndex(['status', 'tanggal']);
        });
    }
};
