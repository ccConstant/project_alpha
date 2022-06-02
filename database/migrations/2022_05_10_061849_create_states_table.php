<?php

/*
* Filename : 2022_05_10_061849_create_states_table.php
* Creation date : 10 May 2022
* Update date : 10 May 2022
* Role : This file is used to create the table "states" in the data base. In this file, we can see the different
* attribute of this table (name, remarks, startDate...) and how they are defined (string, boolean, unique or not)
*/ 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the table states in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('state_remarks') ;
            $table->date('state_startDate') ->nullable; 
            $table->date('state_endDate') ->nullable() ; 
            $table->boolean('state_isOk') ->nullable(); 
            $table->enum('state_validate', ['Drafted', 'to_be_validated', 'Validated']) ;  
            $table->unsignedBigInteger('reformedBy_id')  -> nullable() ; 
            $table->foreign('reformedBy_id')->references('id')->on('peoples') ->onDelete('restrict')  ; 
            $table->unsignedBigInteger('enumStateName_id') ->nullable();
            $table->foreign('enumStateName_id')->references('id')->on('enum_state_names') -> onDelete('restrict') ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table states if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('states');
    }
};



