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
        Schema::create('detailpenghasilan', function (Blueprint $table) {
            $table->id('detail_penghasilan');
            $table->unsignedBigInteger('id_gaji');
            $table->foreign('id_gaji')->references('id_gaji')->on('gaji')->onDelete('cascade');
            $table->unsignedBigInteger('id_komponen');
            $table->foreign('id_komponen')->references('id_komponen')->on('masterkomponen')->onDelete('cascade');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailpenghasilan');
    }
};
