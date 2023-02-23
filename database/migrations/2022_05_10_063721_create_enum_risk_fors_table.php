<?php

/*
* Filename : 2022_05_10_063721_create_enum_risk_fors_table.php
* Creation date : 10 May 2022
* Update date : 15 Feb 2023
* This file is used to create the table "enum_risk_fors" in the data base. In this file, we can see the different
* attribute of this table (id and value) and how they are defined (string, boolean, unique or not)
*/ 




use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the table enum_risk_fors in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('enum_risk_fors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('value');
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table enum_risk_fors if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enum_risk_fors');
    }
};
