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
        Schema::create('logbaranginventaris', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal_log')->nullable();
            $table->foreignId('baranginventaris_id')->nullable();

            $table->string('no_barcode')->nullable();
            $table->string('label')->nullable();
            
            $table->dateTime('tanggal_masuk')->nullable();
            $table->foreignId('kategoribaranginventaris_id')->nullable();
            $table->string('nama')->nullable();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 15, 2)->nullable();
            $table->integer('jumlah')->nullable();
            $table->decimal('total_harga', 15, 2)->nullable();
            $table->integer('jumlah_terpakai')->default(0)->nullable();
            $table->string('satuan')->nullable();
            
            
            
            $table->foreignId('asallokasi_id')->nullable();
            $table->foreignId('tujuanlokasi_id')->nullable();


            $table->integer('log')->nullable();

            // 0 = hapus
            // 1 = tambah
            // 2 = edit
            // 3 = mutasi
            // 4 = perbaikan


            $table->integer('operator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbaranginventaris');
    }
};
