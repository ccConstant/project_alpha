<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
* Filename : 2022_06_08_120123_create_enum_usage_verif_acceptance_authorities_table.php
* Creation date : 8 Jun 2022
* Update date : 15 Feb 2023
* This file is used to create the table "enum_usage_verif_acceptance_authorities" in the data base. In this file, we can see the different
* attribute of this table (id and value) and how they are defined (string, boolean, unique or not)
*/ 

class CreateEnumVerifAcceptanceAuthoritiesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table enum_usage_verif_acceptance_authorities in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('enum_verif_acceptance_authorities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('value') ; 
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table enum_usage_verif_acceptance_authorities if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enum_verif_acceptance_authorities');
    }
}
