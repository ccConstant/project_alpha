<?php

/*
* Filename : 2023_04_20_093809_create_enum_storage_conditions_table.php
* Creation date : 20 Apr 2023
* Update date : 27 Jun 2023
* This file is used to create the table "enum_storage_conditions" in the data base. In this file, we can see the different
* attribute of this table (id, value..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnumStorageConditionsTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table enum_storage_conditions in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('enum_storage_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('value') ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table enum_storage_conditions if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enum_storage_conditions');
    }
}
