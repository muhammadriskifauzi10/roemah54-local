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
        Schema::create('lokasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId("jenisruangan_id")->nullable();
            $table->foreignId("lantai_id")->nullable();
            $table->string("nomor_kamar", 50)->nullable();
            $table->foreignId("tipekamar_id")->nullable();
            $table->string("token_listrik", 50)->nullable();
            $table->tinyInteger("status")->default(0);
            $table->integer('operator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasis');
    }
};
