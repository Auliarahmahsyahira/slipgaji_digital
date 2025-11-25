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
        Schema::create('gaji', function (Blueprint $table) {
            $table->id('id_gaji');
            $table->string('nip_pegawai', '30'); 
            $table->foreign('nip_pegawai')->references('nip_pegawai')->on('pegawai')->onDelete('cascade');
            $table->string('periode');
            $table->decimal('jumlah_kotor', 15, 2)->default(0);
            $table->decimal('jumlah_potongan', 15, 2)->default(0);
            $table->decimal('jumlah_bersih', 15, 2)->default(0);
            $table->decimal('total_potongan', 15, 2)->default(0);
            $table->decimal('gaji_diterima', 15, 2)->default(0);
            $table->decimal('gaji_pokok', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji');
    }
};
