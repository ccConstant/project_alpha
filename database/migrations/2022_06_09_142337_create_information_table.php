<?php

/*
* Filename : 2022_06_09_142337_create_information_table.php
* Creation date : 9 Jun 2022
* Update date : 27 Jun 2023
* This file is used to create the table "informations" in the data base. In this file, we can see the different
* attribute of this table (id, value and name) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformationTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table informations in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('information', function (Blueprint $table) {
            $table->id();
            $table->string('info_name') ;
            $table->string('info_value') ;
            $table->string('info_set') ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table informations if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('information');
    }
}

