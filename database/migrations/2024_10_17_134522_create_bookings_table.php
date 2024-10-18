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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->nullable();
            $table->dateTime('dari_tanggal')->nullable();
            $table->dateTime('sampai_tanggal')->nullable();
            $table->foreignId('lokasi_id')->nullable();
            $table->text('catatan')->nullable();
            $table->integer('operator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
