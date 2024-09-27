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
        Schema::create('penyewas', function (Blueprint $table) {
            $table->id();
            $table->string('namalengkap', 191);
            $table->string('noktp', 50);
            $table->string('nohp', 50);
            $table->string('jenis_kelamin', 1);
            $table->text('alamat');
            $table->string('jenis_penyewa', 100)->default('Umum');
            $table->string('fotoktp', 100);
            $table->tinyInteger('status')->default(1);
            $table->integer('operator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyewas');
    }
};
