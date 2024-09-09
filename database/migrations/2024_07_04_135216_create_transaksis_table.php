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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->foreignId('tagih_id')->nullable();
            $table->foreignId('pembayaran_id')->nullable();
            $table->dateTime('tanggal_transaksi');
            $table->decimal('jumlah_uang', 15, 2);
            $table->string('metode_pembayaran');
            $table->enum('tipe', ['pemasukan', 'pengeluaran'])->default('pemasukan');
            $table->integer('operator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
