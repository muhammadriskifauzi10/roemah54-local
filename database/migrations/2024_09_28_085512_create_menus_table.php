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
        Schema::create('menus', function (Blueprint $table) {
            $table->id(); // ID unik untuk setiap menu
            $table->string('name'); // Nama menu yang ditampilkan
            $table->string('route')->nullable(); // Nama route untuk mengarahkan ke halaman terkait
            $table->string('role')->nullable(); // Role yang memiliki akses ke menu ini (misalnya: 'admin', 'developer')
            $table->unsignedBigInteger('parent_id')->nullable(); // Menghubungkan submenu dengan menu utama
            $table->integer('order')->default(0); // Menentukan urutan menu
            $table->timestamps(); // Timestamp untuk created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
