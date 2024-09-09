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
        Schema::create('ritels', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal_ritel')->nullable();
            $table->foreignId('penyewa_id')->nullable();
            $table->foreignId('lokasi_id')->nullable();
            $table->foreignId('jenis_ritel')->nullable();
            $table->decimal('kiloan', 15, 2)->nullable();
            $table->decimal('jumlah_pembayaran', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('operator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ritels');
    }
};
