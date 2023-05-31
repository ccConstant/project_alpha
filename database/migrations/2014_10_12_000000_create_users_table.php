<?php

/*
* Filename : 2014_10_12_000000_create_users_table.php
* Creation date : 9 May 2022
* Update date : 15 Feb 2023
* This file is used to create the table "users" in the data base. In this file, we can see the different
* attribute of this table (firstname, lastname, rights...) and how they are defined (string, boolean, unique or not)
*/


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table "users" in the data base.
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_firstName');
            $table->string('user_lastName');
            $table->string('user_initials')->nullable();
            $table->string('user_signaturePath')->nullable();
            $table->string('user_pseudo');
            $table->string('password');
            $table->date('user_startDate');
            $table->date('user_endDate')->nullable();

            //rightLevel general
            $table->boolean('user_menuUserAcessRight')->default(true);
            $table->boolean('user_resetUserPasswordRight')->default(true);
            $table->boolean('user_updateDataInDraftRight')->default(true);
            $table->boolean('user_validateDescriptiveLifeSheetDataRight')->default(true);
            $table->boolean('user_validateOtherDataRight')->default(true);
            $table->boolean('user_updateDataValidatedButNotSignedRight')->default(true);
            $table->boolean('user_updateDescriptiveLifeSheetDataSignedRight')->default(true);
            $table->boolean('user_makeQualityValidationRight')->default(true);
            $table->boolean('user_makeTechnicalValidationRight')->default(true);
            $table->boolean('user_deleteDataNotValidatedLinkedToEqOrMmeRight')->default(true);
            $table->boolean('user_deleteDataValidatedLinkedToEqOrMmeRight')->default(true);
            $table->boolean('user_deleteDataSignedLinkedToEqOrMmeRight')->default(true);
            $table->boolean('user_deleteEqOrMmeRight')->default(true);
            $table->boolean('user_makeReformRight')->default(true);
            $table->boolean('user_declareNewStateRight')->default(true);

            //right level for enum and information
            $table->boolean('user_updateEnumRight')->default(true);
            $table->boolean('user_deleteEnumRight')->default(true);
            $table->boolean('user_addEnumRight')->default(true);
            $table->boolean('user_updateInformationRight')->default(true);


            //right level for equipment
            $table->boolean('user_makeEqOpValidationRight')->default(true);
            $table->boolean('user_personTrainedToGeneralPrinciplesOfEqManagementRight')->default(true);
            $table->date('user_formationEqDate')->nullable();
            $table->boolean('user_makeEqRespValidationRight')->default(true);

            //right level for MME
            $table->boolean('user_personTrainedToGeneralPrinciplesOfMMEManagementRight')->default(true);
            $table->date('user_formationMmeDate')->nullable();
            $table->boolean('user_makeMmeOpValidationRight')->default(true);
            $table->boolean('user_makeMmeRespValidationRight')->default(true);

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table "users" in the data base.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
