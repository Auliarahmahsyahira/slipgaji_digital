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
        Schema::table('detailpotongan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_gaji_bulanan');
            $table->foreign('id_gaji_bulanan')
            ->references('id')
            ->on('gaji_bulanan')
            ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('detailpotongan', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
};
