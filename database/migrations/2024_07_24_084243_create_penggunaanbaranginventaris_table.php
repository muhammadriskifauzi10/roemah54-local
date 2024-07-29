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
        Schema::create('penggunaanbaranginventaris', function (Blueprint $table) {
            $table->id();
            $table->string('no_barcode')->nullable();
            $table->string('label')->nullable();
            $table->foreignId('baranginventaris_id')->nullable();
            $table->foreignId('lokasi_id')->nullable();
            $table->integer('jumlah')->nullable();
            $table->integer('operator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggunaanbaranginventaris');
    }
};
