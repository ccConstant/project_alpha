<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
* Filename : 2022_06_07_1400027_create_mme_states_table.php
* Creation date : 7 Jun 2022
* Update date : 7 Jun 2022
* Role : This file is used to create the table "mme_states" in the data base. In this file, we can see the different
* attribute of this table (name, remarks, startDate...) and how they are defined (string, boolean, unique or not)
*/ 

class CreateMmeStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mme_states', function (Blueprint $table) {
            $table->id();
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
        Schema::dropIfExists('mme_states');
    }
}
