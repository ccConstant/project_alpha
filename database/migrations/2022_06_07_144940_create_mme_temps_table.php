<?php

/*
* Filename : 2022_06_07_144940_create_mme_temps_table.php
* Creation date : 7 Jun 2022
* Update date : 15 Feb 2023
* This file is used to create the table "mme_temps" in the data base. In this file, we can see the different
* attribute of this table (version, date, validate..) and how they are defined (string, boolean, unique or not)
*/ 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMmeTempsTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table mme_temps in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('mme_temps', function (Blueprint $table) {
            $table->id() ;
            $table->unsignedTinyInteger('mmeTemp_version') ; 
            $table->timestamp('mmeTemp_date') ; 
            $table->string('mmeTemp_location')->nullable ;
            $table->enum('mmeTemp_validate',  ['drafted', 'to_be_validated', 'validated']) ;  
            $table->timestamps();
            $table->boolean('mmeTemp_lifeSheetCreated') ->default(false) ; 
            $table->text('mmeTemp_remarks') -> nullable();
            $table->unsignedBigInteger('mme_id') ;
            $table->foreign('mme_id')->references('id')->on('mmes') ->onDelete('cascade')  ; 
            $table->unsignedBigInteger('qualityVerifier_id') -> nullable() ; 
            $table->foreign('qualityVerifier_id')->references('id')->on('users') ->onDelete('restrict')  ; 
            $table->unsignedBigInteger('technicalVerifier_id')  -> nullable() ; 
            $table->foreign('technicalVerifier_id')->references('id')->on('users') ->onDelete('restrict')  ; 
            $table->unsignedBigInteger('createdBy_id') -> nullable() ; 
            $table->foreign('createdBy_id')->references('id')->on('users') ->onDelete('restrict')  ; 
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table mme_temps if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mme_temps');
    }
}
