<?php

/*
* Filename : 2023_04_20_083400_create_raw_sub_families_table.php
* Creation date : 28 Jun 2023
* Update date : 28 Jun 2023
* This file is used to create the table "raw_sub_families" in the data base. In this file, we can see the different
* attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawSubFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table raw_sub_families in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('raw_sub_families', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table raw_sub_families if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raw_sub_families');
    }
}
