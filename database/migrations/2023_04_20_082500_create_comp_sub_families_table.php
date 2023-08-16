<?php

/*
* Filename : 2023_06_28_142531_create_comp_sub_families_table.php
* Creation date : 28 Jun 2023
* Update date : 28 Jun 2023
* This file is used to create the table "comp_sub_families" in the data base. In this file, we can see the different
* attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompSubFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table comp_sub_families in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('comp_sub_families', function (Blueprint $table) {
            $table->id();
            $table->string('compSubFam_ref')->unique();
            $table->string('compSubFam_design');
            $table->string('compSubFam_drawingPath')->nullable();
            $table->integer('compSubFam_nbrVersion')->default(1);
            $table->string('compSubFam_materials')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('compSubFam_qualityApproverId')->nullable();
            $table->foreign('compSubFam_qualityApproverId')->references('id')->on('users');
            $table->unsignedBigInteger('compSubFam_technicalReviewerId')->nullable();
            $table->foreign('compSubFam_technicalReviewerId')->references('id')->on('users');
            $table->date('compSubFam_signatureDate')->nullable();
            $table->enum('compSubFam_validate', ['drafted', 'to_be_validated', 'validated']);
            $table->string('compSubFam_version')->nullable();
            $table->boolean('compSubFam_active')->default(true);
            $table->unsignedBigInteger('enumPurchasedBy_id')->nullable();
            $table->foreign('enumPurchasedBy_id')->references('id')->on('enum_purchased_bies')->onDelete('restrict');
            $table->unsignedBigInteger('compFam_id') ->nullable();
            $table->foreign('compFam_id')->references('id')->on('comp_families') ;
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table comp_families if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comp_sub_families');
    }
}
