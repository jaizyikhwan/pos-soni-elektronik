<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {

            $table->integer('titipan')->default(0);
            $table->integer('sisa_pembayaran')->default(0);
            $table->string('status_pembayaran')->default('LUNAS');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {

            $table->dropColumn([
                'titipan',
                'sisa_pembayaran',
                'status_pembayaran'
            ]);
        });
    }
};
