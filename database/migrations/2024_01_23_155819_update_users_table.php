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
        
        Schema::table('users', function (Blueprint $table) {
            $table->integer('verification_code')->default(random_int(1000000,9999999));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
