<?php

/*
* Filename : 2023_04_21_075744_pivot_comp_fam_supplr.php
* Creation date : 21 Apr 2023
* Update date : 21 Apr 2023
* This file is used to create the table "pivot_comp_fam_supplr" in the data base. In this file, we can see the different
* attribute of this table ((two foreign key for link compFam table and supplier table)) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PivotCompFamSupplr extends Migration
{
    /**
     * Run the migrations.
     * Create the table pivot_comp_fam_supplr in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_comp_fam_supplr', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('compFam_id');
            $table->foreign('compFam_id')->references('id')->on('comp_families')->onDelete('cascade');
            $table->unsignedBigInteger('supplr_id');
            $table->foreign('supplr_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->string('supplr_ref');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table pivot_comp_fam_supplr if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pivot_comp_fam_supplr');
    }
}
