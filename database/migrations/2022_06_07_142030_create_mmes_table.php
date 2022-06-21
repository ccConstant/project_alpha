<?php

/*
* Filename : 2022_06_07_142255_create_mmes_table.php
* Creation date : 7 Jun 2022
* Update date : 7 Jun 2022
* Role : This file is used to create the table "mme" in the data base. In this file, we can see the different
* attribute of this table (internReference, externReference..) and how they are defined (string, boolean, unique or not)
*/ 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMmesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mmes', function (Blueprint $table) {
            $table->id();
            $table->string('mme_internalReference') -> unique ; 
            $table->string('mme_externalReference') ;
            $table->string('mme_name') -> nullable() ; 
            $table->string('mme_serialNumber') -> nullable() ; 
            $table->string('mme_constructor') -> nullable() ; 
            $table->string('mme_set') -> nullable();  
            $table->unsignedTinyInteger('mme_nbrVersion') -> default(1) ;
            $table->unsignedBigInteger('state_id') ->nullable(); 
            $table->foreign('state_id')->references('id')->on('mme_states') ;
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
        Schema::dropIfExists('mmes');
    }
}

