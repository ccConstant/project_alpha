<?php

/*
* Filename : 2023_05_31_120526_create_cons_family_members_table.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file is used to create the table "cons_family_members" in the data base. In this file, we can see the different
* attribute of this table (dimension, technicalReviewer..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsFamilyMembersTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table cons_family_members in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('cons_family_members', function (Blueprint $table) {
            $table->id();
            $table->string('consMb_dimension')->nullable();
            $table->string('consMb_design')->nullable();
            $table->boolean('consMb_sameValues')->default(true);
            $table->foreign('consMb_technicalReviewerId')->references('id')->on('users');
            $table->unsignedBigInteger('consMb_technicalReviewerId')->nullable();
            $table->foreign('consMb_qualityApproverId')->references('id')->on('users');
            $table->unsignedBigInteger('consMb_qualityApproverId')->nullable();
            $table->string('consMb_signatureDate')->nullable();
            $table->unsignedBigInteger('consSubFam_id')->nullable();
            $table->foreign('consSubFam_id')->references('id')->on('cons_sub_families');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table cons_family_members if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cons_family_members');
    }
}
