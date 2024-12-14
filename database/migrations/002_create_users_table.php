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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('short_name')->unique()->nullable();
            $table->string('profile_picture')->nullable(); 
            $table->string('cover_photo')->nullable(); 
            $table->unsignedBigInteger('address');
            $table->unsignedBigInteger('role_id');
            $table->text('bio')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('address')->references('id')->on('barangays');
            $table->foreign('role_id')->references('id')->on('roles');
        }); 

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('users');

        
    }
};
