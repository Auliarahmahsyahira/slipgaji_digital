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
        // Hapus foreign key
        $table->dropForeign(['id']); // atau sesuaikan jika FK-nya beda nama
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detailpotongan', function (Blueprint $table) {
            //
        });
    }
};
