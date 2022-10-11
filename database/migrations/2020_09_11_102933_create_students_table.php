<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string("code_std");
            $table->string("code_filiere"); 
            $table->string("code_niveau");                     
            $table->string('firstname_std');
            $table->string('lastname_std'); 
            $table->string('photo_std'); 
            $table->string('telephone_std'); 
            $table->string('email_std')->unique();
            $table->timestamp('email_std_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
