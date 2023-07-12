<?php

/*
* Filename : 2023_04_20_091843_create_criticalities_table.php
* Creation date : 20 Apr 2023
* Update date : 10 Jul 2023
* This file is used to create the table "comp_criticalities" in the data base. In this file, we can see the different
* attribute of this table (compCriticality, technicalReviewer..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriticalitiesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table comp_criticalities in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('criticalities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('crit_qualityApproverId') ->nullable();
            $table->foreign('crit_qualityApproverId')->references('id')->on('users') ;
            $table->unsignedBigInteger('crit_technicalReviewerId') ->nullable();
            $table->foreign('crit_technicalReviewerId')->references('id')->on('users') ;
            $table->date('crit_signatureDate') -> nullable() ;
            $table->enum('crit_validate',  ['drafted', 'to_be_validated', 'validated']) ;
            $table->string('crit_remarks') ->nullable();

            $table->unsignedBigInteger('compFam_id') ->nullable();
            $table->foreign('compFam_id')->references('id')->on('comp_families') ;
            $table->unsignedBigInteger('consFam_id') ->nullable();
            $table->foreign('consFam_id')->references('id')->on('cons_families') ;
            $table->unsignedBigInteger('rawFam_id') ->nullable();
            $table->foreign('rawFam_id')->references('id')->on('raw_families') ;

            $table->unsignedBigInteger('compSubFam_id') ->nullable();
            $table->foreign('compSubFam_id')->references('id')->on('comp_sub_families') ;
            $table->unsignedBigInteger('consSubFam_id') ->nullable();
            $table->foreign('consSubFam_id')->references('id')->on('cons_sub_families') ;
            $table->unsignedBigInteger('rawSubFam_id') ->nullable();
            $table->foreign('rawSubFam_id')->references('id')->on('raw_sub_families') ;

            $table->enum('crit_artCriticality', ['No direct contact', 'No contact but integrated in invasive Medical device','Surface contact', 'Invasive/Implantable'])->nullable();
            $table->boolean('crit_performanceMedicalDevice')->nullable();
            $table->string('crit_checkedTests')->nullable();
            $table->string('crit_checkedTestRadioFunc')->nullable();
            $table->string('crit_checkedTestRadioAsp')->nullable();
            $table->string('crit_checkedTestRadioDoc')->nullable();
            $table->string('crit_checkedTestRadioAdm')->nullable();
            $table->string('crit_checkedTestRadioDim')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     * Delete the table comp_criticalities if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('criticalities');
    }
}
