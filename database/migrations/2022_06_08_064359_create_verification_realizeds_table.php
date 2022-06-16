<?php

/*
* Filename : 2022_06_08_064359_create_verification_realizeds_table.php
* Creation date : 8 Jun 2022
* Update date : 8 Jun 2022
* Role : This file is used to create the table "verification_realizeds" in the data base. In this file, we can see the different
* attribute of this table (reportNumber, startDate, endDate...) and how they are defined (string, boolean, unique or not)
*/ 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationRealizedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verification_realizeds', function (Blueprint $table) {
            $table->id();
            $table->string('verifRlz_reportNumber') ;
            $table->string('verifRlz_isPassed') ;
            $table->date('verifRlz_startDate') ->nullable();
            $table->date('verifRlz_endDate') -> nullable(); 
            $table->date('verifRlz_entryDate') ->nullable();
            $table->enum('verifRlz_validate',  ['drafted', 'to_be_validated', 'validated']) ;  
            $table->unsignedBigInteger('enteredBy_id') -> nullable() ; 
            $table->foreign('enteredBy_id')->references('id')->on('users') ->onDelete('restrict')  ; 
            $table->unsignedBigInteger('realizedBy_id')  -> nullable() ; 
            $table->foreign('realizedBy_id')->references('id')->on('users') ->onDelete('restrict')  ; 
            $table->unsignedBigInteger('approvedBy_id')  -> nullable() ; 
            $table->foreign('approvedBy_id')->references('id')->on('users') ->onDelete('restrict')  ; 
            $table->unsignedBigInteger('state_id') ;
            $table->foreign('state_id')->references('id')->on('mme_states') ->onDelete('restrict')  ; 
            $table->unsignedBigInteger('verif_id') ;
            $table->foreign('verif_id')->references('id')->on('verifications') ->onDelete('cascade')  ; 
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
        Schema::dropIfExists('verification_realizeds');
    }
}
