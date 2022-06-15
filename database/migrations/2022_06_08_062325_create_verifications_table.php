<?php

/*
* Filename : 2022_06_08_062325_create_verifications_table.php
* Creation date : 8 Jun 2022
* Update date : 8 Jun 2022
* Role : This file is used to create the table "verifications" in the data base. In this file, we can see the different
* attribute of this table (number, description, periodicity...) and how they are defined (string, boolean, unique or not)
*/ 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('verif_number')  ; 
            $table->string('verif_name')  ; 
            $table->string('verif_expectedResult') ->nullable(); 
            $table->string('verif_nonComplianceLimit') ->nullable(); 
            $table->unsignedMediumInteger('verif_periodicity') ->nullable(); 
            $table->enum('verif_symbolPeriodicity', ['Y', 'M', 'D', 'H']) ->nullable();  
            $table->string('verif_requiredSkill') ->nullable(); 
            $table->mediumText('verif_protocol') ->nullable(); 
            $table->mediumText('verif_description') ->nullable(); 
            $table->timestamp('verif_startDate') ; 
            $table->timestamp('verif_nextDate') ->nullable(); 
            $table->date('verif_reformDate') ->nullable() ; 
            $table->enum('verif_validate',  ['drafted', 'to_be_validated', 'validated']) ;  
            $table->unsignedBigInteger('mmeTemp_id') ->nullable() ;
            $table->foreign('mmeTemp_id')->references('id')->on('mme_temps') ->onDelete('cascade')  ;
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
        Schema::dropIfExists('verifications');
    }
}
