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
        Schema::create('asramas', function (Blueprint $table) {
            $table->id();
            $table->foreignId("lantai_id")->nullable();
            $table->string("nomor_kamar", 50)->nullable();
            $table->foreignId("tipekamar_id")->nullable();
            $table->string('tipekamar', 50)->nullable();
            $table->string('jenissewa')->nullable();
            $table->integer("jumlah_mahasiswa")->default(0);
            $table->integer('operator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asramas');
    }
};
