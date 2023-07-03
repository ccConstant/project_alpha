<?php

/*
* Filename : 2023_04_20_083900_create_raw_family_members_table.php
* Creation date : 20 Apr 2023
* Update date : 3 Jul 2023
* This file is used to create the table "raw_family_members" in the data base. In this file, we can see the different
* attribute of this table (dimension, technicalReviewer..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawFamilyMembersTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table raw_family_members in the data base
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_family_members', function (Blueprint $table) {
            $table->id();
            $table->string('rawMb_dimension') ->nullable();
            $table->string('rawMb_design')->nullable();
           /* $table->boolean('rawMb_sameValues')->default(true);*/
            $table->unsignedBigInteger('rawSubFam_id') ->nullable();
            $table->foreign('rawSubFam_id')->references('id')->on('raw_sub_families') ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table raw_family_members if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raw_family_members');
    }
}
