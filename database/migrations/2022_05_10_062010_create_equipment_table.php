
<?php

/*
* Filename : 2022_05_10_062010_create_equipment_table.php
* Creation date : 10 May 2022
* Update date : 25 May 2023
* This file is used to create the table "equipement" in the data base. In this file, we can see the different
* attribute of this table (internReference, externReference..) and how they are defined (string, boolean, unique or not)
*/


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Class CreateEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table equipement in the data base
     * @return void
     */

    public function up()
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id('id') ;
            $table->string('eq_internalReference') -> unique ;
            $table->string('eq_externalReference') ;
            $table->string('eq_name') -> nullable() ;
            $table->string('eq_serialNumber') -> nullable() ;
            $table->string('eq_constructor') -> nullable() ;
            $table->string('eq_set') -> nullable();
            $table->unsignedTinyInteger('eq_nbrVersion') -> default(1) ;
            $table->timestamps() ;
            $table->unsignedBigInteger('state_id') ->nullable();
            $table->foreign('state_id')->references('id')->on('states') ;
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table equipment if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment');
    }
};




