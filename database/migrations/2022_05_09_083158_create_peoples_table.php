<?php

/*
* Filename : 2022_05_09_083158_create_peoples_table.php
* Creation date : 9 May 2022
* Update date : 9 May 2022
* Role : This file is used to create the table "peoples" in the data base. In this file, we can see the different
* attribute of this table (firstName, lastName..) and how they are defined (string, boolean, unique or not)
*/ 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the table peoples in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('peoples', function (Blueprint $table) {
            $table->id();
            $table->string('people_firstName') ; 
            $table->string('people_lastName') ; 
            $table->string('people_initials') ; 
            $table->string('people_signaturePath') -> nullable() ; 
            $table->enum('people_validate',  ['drafted', 'to_be_validated', 'validated']) ;  
            $table->unsignedTinyInteger('people_rightLevel') -> nullable() ; 
            $table->string('people_userName') -> nullable() ; 
            $table->string('people_password')-> nullable() ; 
            $table->date('people_startDate') ; 
            $table->date('people_endDate') ->nullable(); 
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table peoples if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peoples');
    }
};


