<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnswerassignsTable extends Migration
{
    

    public function up()
    {
        Schema::create('answerassigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("student_id");
            $table->integer("assign_id");
            $table->string("name_doc");
            $table->string("student_name");
            $table->string("chifre_answer");
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('answerassigns');
    }
}
