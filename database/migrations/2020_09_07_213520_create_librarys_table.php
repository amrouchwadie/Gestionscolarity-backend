<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLibrarysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('librarys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('book_name'); 
            $table->integer("nbr_page");          
            $table->string('domain'); 
            $table->string('figure_book'); 
            $table->string("author");
            $table->string("description");
            $table->string('publication_date'); 
            $table->string('photo'); 
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
        Schema::dropIfExists('librarys');
    }
}
