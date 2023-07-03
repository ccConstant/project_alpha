<?php

/*
* Filename : 2023_04_20_075945_create_cons_sub_families_table.php
* Creation date : 3 Jul 2023
* Update date : 3 Jul 2023
* This file is used to create the table "cons_sub_families" in the data base. In this file, we can see the different
* attribute of this table (ref, technicalReviewer..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsSubFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table cons_sub_families in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('cons_sub_families', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('consSubFam_ref')->unique();
            $table->string('consSubFam_design');
            $table->string('consSubFam_drawingPath')->nullable();
            $table->integer('consSubFam_nbrVersion')->default(1);
            $table->unsignedBigInteger('consSubFam_qualityApproverId')->nullable();
            $table->foreign('consSubFam_qualityApproverId')->references('id')->on('users');
            $table->unsignedBigInteger('consSubFam_technicalReviewerId')->nullable();
            $table->foreign('consSubFam_technicalReviewerId')->references('id')->on('users');
            $table->date('consSubFam_signatureDate')->nullable();
            $table->enum('consSubFam_validate', ['drafted', 'to_be_validated', 'validated']);
            $table->string('consSubFam_version')->nullable();
            $table->boolean('consSubFam_active')->default(true);
            $table->unsignedBigInteger('enumPurchasedBy_id')->nullable();
            $table->foreign('enumPurchasedBy_id')->references('id')->on('enum_purchased_bies')->onDelete('restrict');
            $table->unsignedBigInteger('consFam_id') ->nullable();
            $table->foreign('consFam_id')->references('id')->on('cons_families') ;
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table cons_sub_families if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cons_sub_families');
    }
}
