<?php

/*
* Filename : 2022_06_09_072655_create_mme_usages_table.php
* Creation date : 9 Jun 2022
* Update date : 9 Jun 2022
* Role : This file is used to create the table "mme_usages" in the data base. In this file, we can see the different
* attribute of this table (id and value) and how they are defined (string, boolean, unique or not)
*/ 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMmeUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mme_usages', function (Blueprint $table) {
            $table->id();
            $table->string('usg_measurementType') ; 
            $table->string('usg_precision');
            $table->string('usg_application') ; 
            $table->date('usg_startDate')  ;
            $table->date('usg_reformDate') ->nullable();
            $table->enum('usg_validate',  ['drafted', 'to_be_validated', 'validated']) ;  
            $table->unsignedBigInteger('enumUsageMetrologicalLevel_id') ->nullable();
            $table->foreign('enumUsageMetrologicalLevel_id')->references('id')->on('enum_usage_metrological_levels') -> onDelete('restrict') ;
            $table->unsignedBigInteger('enumUsageVerifAcceptanceAuthority_id') ->nullable();
            $table->foreign('enumUsageVerifAcceptanceAuthority_id')->references('id')->on('enum_usage_verif_acceptance_authorities') -> onDelete('restrict') ;
            $table->unsignedBigInteger('mmeTemp_id') ->nullable();
            $table->foreign('mmeTemp_id')->references('id')->on('mme_temps') -> onDelete('cascade') ; 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mme_usages');
    }
}
