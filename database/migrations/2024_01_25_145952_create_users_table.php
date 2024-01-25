<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('email')->unique();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('password');
            $table->timestamps();
            $table->integer('verification_code')->default(4661225);
            $table->timestamp('vCode_date');
            $table->string('role')->default('user');
            $table->boolean('admin')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
