<?php

/*
* Filename : 2023_05_31_123900_create_raw_family_members_table.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
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
     * Create the table raw_family_members in the database
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_family_members', function (Blueprint $table) {
            $table->id();
            $table->string('rawMb_dimension')->nullable();
            $table->string('rawMb_design')->nullable();
            $table->boolean('rawMb_sameValues')->default(true);
            $table->foreign('rawMb_technicalReviewerId')->references('id')->on('users');
            $table->unsignedBigInteger('rawMb_technicalReviewerId')->nullable();
            $table->foreign('rawMb_qualityApproverId')->references('id')->on('users');
            $table->unsignedBigInteger('rawMb_qualityApproverId')->nullable();
            $table->string('rawMb_signatureDate')->nullable();
            $table->unsignedBigInteger('rawSubFam_id')->nullable();
            $table->foreign('rawSubFam_id')->references('id')->on('raw_sub_families');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table raw_family_members if it already exists
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raw_family_members');
    }
}
