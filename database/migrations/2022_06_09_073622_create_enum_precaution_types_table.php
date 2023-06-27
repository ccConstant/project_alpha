<?php

/*
* Filename : 2022_06_09_073622_create_enum_precaution_types_table.php
* Creation date : 9 Jun 2022
* Update date : 27 Jun 2023
* This file is used to create the table "enum_precaution_types" in the data base. In this file, we can see the different
* attribute of this table (id and value) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnumPrecautionTypesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table enum_precaution_types in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('enum_precaution_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('value') ;
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table enum_precaution_types if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enum_precaution_types');
    }
}
