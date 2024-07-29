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
        Schema::create('hargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId("tipekamar_id");
            $table->foreignId("mitra_id");
            $table->decimal('harian', 15, 2);
            $table->decimal('mingguan', 15, 2);
            $table->decimal('hari14', 15, 2);
            $table->decimal('bulanan', 15, 2);
            $table->integer('operator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hargas');
    }
};
