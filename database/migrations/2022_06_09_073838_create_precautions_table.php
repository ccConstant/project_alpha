<?php

/*
* Filename : 2022_06_09_073838_create_precautions_table.php
* Creation date : 9 Jun 2022
* Update date : 27 Jun 2023
* This file is used to create the table "precautions" in the data base. In this file, we can see the different
* attribute of this table (id, description..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrecautionsTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table precautions in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('precautions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('prctn_description') ;
            $table->enum('prctn_validate',  ['drafted', 'to_be_validated', 'validated']) ;
            $table->unsignedBigInteger('enumPrecautionType_id') ->nullable();
            $table->foreign('enumPrecautionType_id')->references('id')->on('enum_precaution_types') -> onDelete('restrict') ;
            $table->unsignedBigInteger('mmeUsage_id') ->nullable();
            $table->foreign('mmeUsage_id')->references('id')->on('mme_usages') -> onDelete('restrict') ;
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table precautions if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('precautions');
    }
}
