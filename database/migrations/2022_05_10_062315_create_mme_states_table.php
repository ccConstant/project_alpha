<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
* Filename : 2022_05_10_062315_create_mme_states_table.php
* Creation date : 7 Jun 2022
* Update date : 15 Feb 2023
* This file is used to create the table "mme_states" in the data base. In this file, we can see the different
* attribute of this table (name, remarks, startDate...) and how they are defined (string, boolean, unique or not)
*/ 

class CreateMmeStatesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table mme_states in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('mme_states', function (Blueprint $table) {
            $table->id();
            $table->string('state_remarks') ;
            $table->date('state_startDate') ->nullable(); 
            $table->enum('state_name', ['Waiting_for_referencing', 'Waiting_to_be_in_use', 'In_use', 'Under_verification', 'In_quarantine', 'Under_repair', 'Broken', 'Downgraded', 'Reformed', 'Lost']) ;  
            $table->date('state_endDate') ->nullable() ; 
            $table->boolean('state_isOk') ->nullable(); 
            $table->enum('state_validate', ['drafted', 'to_be_validated', 'validated']) ; 
            $table->unsignedBigInteger('reformedBy_id')  -> nullable() ; 
            $table->foreign('reformedBy_id')->references('id')->on('users') ->onDelete('restrict')  ; 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table mme_states if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mme_states');
    }
}
