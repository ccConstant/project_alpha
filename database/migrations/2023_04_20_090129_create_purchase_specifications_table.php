<?php

/*
* Filename : 2023_04_20_090129_create_purchase_specifications_table.php
* Creation date : 20 Apr 2023
* Update date : 25 May 2023
* This file is used to create the table "purchase_specifications" in the data base. In this file, we can see the different
* attribute of this table (requiredDoc, technicalReviewer..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table purchase_specifications in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_specifications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->longText('purSpe_requiredDoc') ->nullable();
            $table->unsignedBigInteger('purSpe_qualityApproverId') ->nullable();
            $table->foreign('purSpe_qualityApproverId')->references('id')->on('users') ;
            $table->unsignedBigInteger('purSpe_technicalReviewerId') ->nullable();
            $table->foreign('purSpe_technicalReviewerId')->references('id')->on('users') ;
            $table->date('purSpe_signatureDate') -> nullable() ;
            $table->enum('purSpe_validate',  ['drafted', 'to_be_validated', 'validated']) ;
            $table->unsignedBigInteger('consFam_id') ->nullable();
            $table->foreign('consFam_id')->references('id')->on('cons_families') ;
            $table->unsignedBigInteger('compFam_id') ->nullable();
            $table->foreign('compFam_id')->references('id')->on('comp_families') ;
            $table->unsignedBigInteger('rawFam_id') ->nullable();
            $table->foreign('rawFam_id')->references('id')->on('raw_families') ;


        });
    }

    /**
     * Reverse the migrations.
     * Delete the table purchase_specifications if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_specifications');
    }
}
