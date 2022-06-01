<?php

/*
* Filename : 2022_05_10_061315_create_enum_state_names_table.php
* Creation date : 9 May 2022
* Update date : 9 May 2022
* Role : This file is used to create the table "enum_state_names" in the data base. In this file, we can see the different
* attribute of this table (id and value) and how they are defined (string, boolean, unique or not)
*/ 


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the table enum_state_names in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('enum_state_names', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('value') ; 
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table enum_state_names if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enum_state_names');
    }
};
