<?php

/*
* Filename : 2023_04_20_075848_cons_families.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file is used to create the table "cons_families" in the data base. In this file, we can see the different
* attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table cons_families in the database
     * @return void
     */
    public function up()
    {
        Schema::create('cons_families', function (Blueprint $table) {
            $table->id('id');
            $table->string('consFam_ref')->unique();
            $table->string('consFam_design');
            $table->string('consFam_drawingPath')->nullable();
            $table->integer('consFam_nbrVersion')->default(1);
            $table->string('consFam_variablesCharac')->nullable();
            $table->string('consFam_variablesCharacDesign')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('consFam_qualityApproverId')->nullable();
            $table->foreign('consFam_qualityApproverId')->references('id')->on('users');
            $table->unsignedBigInteger('consFam_technicalReviewerId')->nullable();
            $table->foreign('consFam_technicalReviewerId')->references('id')->on('users');
            $table->date('consFam_signatureDate')->nullable();
            $table->enum('consFam_validate', ['drafted', 'to_be_validated', 'validated']);
            $table->string('consFam_version')->nullable();
            $table->boolean('consFam_active')->default(true);
            $table->unsignedBigInteger('enumPurchasedBy_id')->nullable();
            $table->foreign('enumPurchasedBy_id')->references('id')->on('enum_purchased_bies')->onDelete('restrict');
            $table->string('consFam_mainRef')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table cons_families if it already exists
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cons_families');
    }
}






