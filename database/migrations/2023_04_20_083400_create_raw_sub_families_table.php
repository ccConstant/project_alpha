<?php

/*
* Filename : 2023_04_20_083400_create_raw_sub_families_table.php
* Creation date : 28 Jun 2023
* Update date : 3 Jul 2023
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
            $table->string('rawSubFam_ref')->unique();
            $table->string('rawSubFam_design');
            $table->string('rawSubFam_drawingPath')->nullable();
            $table->integer('rawSubFam_nbrVersion')->default(1);
            $table->unsignedBigInteger('rawSubFam_qualityApproverId')->nullable();
            $table->foreign('rawSubFam_qualityApproverId')->references('id')->on('users');
            $table->unsignedBigInteger('rawSubFam_technicalReviewerId')->nullable();
            $table->foreign('rawSubFam_technicalReviewerId')->references('id')->on('users');
            $table->date('rawSubFam_signatureDate')->nullable();
            $table->enum('rawSubFam_validate', ['drafted', 'to_be_validated', 'validated']);
            $table->boolean('rawSubFam_active')->default(true);
            $table->unsignedBigInteger('enumPurchasedBy_id')->nullable();
            $table->foreign('enumPurchasedBy_id')->references('id')->on('enum_purchased_bies')->onDelete('restrict');
            $table->timestamps();
            $table->unsignedBigInteger('rawFam_id') ->nullable();
            $table->foreign('rawFam_id')->references('id')->on('raw_families') ;
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
