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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kategori_barang_id')->nullable()->constrained('kategori_barangs')->nullOnDelete();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('satuan')->default('pcs');
            $table->decimal('harga_beli', 15, 2); // harga setor / modal diajukan anggota
            $table->decimal('harga_jual', 15, 2)->default(0); // disetujui / ditentukan ketua
            $table->integer('stok_saat_ini')->default(0);
            $table->string('status')->default('pending'); // pending, aktif, ditolak, retur
            $table->text('catatan')->nullable();
            $table->string('foto_barang')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
