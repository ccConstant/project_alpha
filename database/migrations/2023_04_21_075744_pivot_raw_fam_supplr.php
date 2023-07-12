<?php

/*
* Filename : 2023_04_21_075744_pivot_raw_fam_supplr.php
* Creation date : 21 Apr 2023
* Update date : 27 Jun 2023
* This file is used to create the table "pivot_raw_fam_supplr" in the data base. In this file, we can see the different
* attribute of this table ((two foreign key for link rawFam table and supplier table)) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PivotRawFamSupplr extends Migration
{
    /**
     * Run the migrations.
     * Create the table pivot_raw_fam_supplr in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_raw_fam_supplr', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rawFam_id');
            $table->foreign('rawFam_id')->references('id')->on('raw_families')->onDelete('cascade');
            $table->unsignedBigInteger('supplr_id');
            $table->foreign('supplr_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->string('supplr_ref')->nullable();
            $table->string('remark')->nullable();
            $table->unsignedBigInteger('purSpec_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table pivot_raw_fam_supplr if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pivot_raw_fam_supplr');
    }
}
