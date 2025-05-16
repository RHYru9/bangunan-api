<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 40);
            $table->string('email')->unique();
            $table->string('alamat')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('password');
            $table->rememberToken(); // untuk fitur "remember me"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
