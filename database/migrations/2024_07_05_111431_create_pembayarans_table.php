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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagih_id')->nullable();
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->dateTime('tanggal_masuk')->nullable();
            $table->dateTime('tanggal_keluar')->nullable();
            $table->foreignId('penyewa_id')->nullable();
            $table->foreignId('lokasi_id')->nullable();
            $table->foreignId('tipekamar_id')->nullable();
            $table->foreignId('mitra_id')->nullable(); 
            $table->string('jenissewa')->nullable();
            $table->decimal('jumlah_pembayaran', 15, 2)->nullable();
            $table->decimal('diskon', 15, 2)->nullable(); 
            $table->decimal('potongan_harga', 15, 2)->nullable(); 
            $table->decimal('total_bayar', 15, 2)->nullable(); 
            $table->decimal('kurang_bayar', 15, 2)->nullable(); 
            $table->enum('status_pembayaran', ['failed', 'pending', 'completed'])->default('pending');
            $table->integer('operator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
