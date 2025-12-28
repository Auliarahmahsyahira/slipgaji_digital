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
    Schema::table('gaji_wajib', function (Blueprint $table) {
        $table->string('periode')->change();
    });
}

public function down(): void
{
    Schema::table('gaji_wajib', function (Blueprint $table) {
        $table->enum('periode', ['24', '1'])->change();
    });
}

};
