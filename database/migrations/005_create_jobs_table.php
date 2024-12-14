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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            // $table->string('title');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tree');
            $table->unsignedBigInteger('address');
            $table->integer('quantity');
            $table->integer('stocks');
            // $table->text('job_description');
            $table->timestamps();

            $table->foreign('address')->references('id')->on('barangays');
            $table->foreign('tree')->references('id')->on('trees');
            $table->foreign('user_id')->references('id')->on('users');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       

        
        Schema::dropIfExists('jobs');
    }
};
