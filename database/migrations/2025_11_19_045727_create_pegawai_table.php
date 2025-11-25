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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->string('nip_pegawai', 30)->primary();
            $table->string('nama', 100);
            $table->string('kdsatker','50');
            $table->string('jabatan', 100)->nullable();
            $table->string('no_rekening', '30');
            $table->string('golongan', 50)->nullable();
            $table->string('nama_golongan', '100')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
