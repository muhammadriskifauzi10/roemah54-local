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
        Schema::create('tokenlistriks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->nullable();
            $table->dateTime('tanggal_token')->nullable();
            $table->foreignId('penyewa_id')->nullable();
            $table->foreignId('lokasi_id')->nullable();
            $table->decimal('jumlah_kwh_lama', 15, 2)->nullable();
            $table->decimal('jumlah_kwh_baru', 15, 2)->nullable();
            $table->decimal('jumlah_pembayaran', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('fotokwhlama', 100)->nullable();
            $table->string('fotokwhbaru', 100)->nullable();
            $table->integer('operator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokenlistriks');
    }
};
