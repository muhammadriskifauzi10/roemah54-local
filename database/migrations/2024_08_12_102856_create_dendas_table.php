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
        Schema::create('dendas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal_denda')->nullable();
            $table->foreignId('pembayaran_id')->nullable();
            $table->foreignId('penyewa_id')->nullable();
            $table->foreignId('lokasi_id')->nullable();
            $table->foreignId('tagih_id')->nullable();
            $table->decimal('jumlah_uang', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('operator_id');
            $table->integer('status_pembayaran')->default(2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dendas');
    }
};
