<?php

/*
* Filename : 2023_04_20_082307_create_comp_family_members_table.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file is used to create the table "comp_family_members" in the data base. In this file, we can see the different
* attribute of this table (dimension, technicalReviewer..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompFamilyMembersTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table comp_family_members in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('comp_family_members', function (Blueprint $table) {
            $table->id();
            $table->string('compMb_dimension') ->nullable();
            $table->string('compMb_design')->nullable();
            $table->boolean('compMb_sameValues')->default(true);
            $table->foreign('compMb_technicalReviewerId')->references('id')->on('users') ;
            $table->unsignedBigInteger('compMb_technicalReviewerId') ->nullable();
            $table->foreign('compMb_qualityApproverId')->references('id')->on('users') ;
            $table->unsignedBigInteger('compMb_qualityApproverId') ->nullable();
            $table->string('compMb_signatureDate') ->nullable();
            $table->unsignedBigInteger('compFam_id') ->nullable();
            $table->foreign('compFam_id')->references('id')->on('comp_families') ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table comp_family_members if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comp_family_members');
    }
}
