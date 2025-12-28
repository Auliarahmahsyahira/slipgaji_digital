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
        Schema::create('gaji_bulanan', function (Blueprint $table) {
            $table->id('id');
            $table->string('nip_pegawai', '30'); 
            $table->foreign('nip_pegawai')->references('nip_pegawai')->on('pegawai')->onDelete('cascade');
            $table->decimal('total_potongan', 15, 2)->default(0);
            $table->decimal('gaji_diterima', 15, 2)->default(0);
            $table->unsignedBigInteger('id_gaji');
            $table->foreign('id_gaji')->references('id_gaji')->on('gaji_wajib')->onDelete('cascade');
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji_bulanan');
    }
};
