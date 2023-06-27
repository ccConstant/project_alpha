<?php

/*
* Filename : 2014_10_12_000000_create_users_table.php
* Creation date : 9 May 2022
* Update date : 27 Jun 2023
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
            $table->string('user_firstName') ;
            $table->string('user_lastName') ;
            $table->string('user_initials') ->nullable();
            $table->string('user_signaturePath')->nullable();
            $table->string('user_pseudo') ;
            $table->string('password') ;
            $table->date('user_startDate') ;
            $table->date('user_endDate') -> nullable();

            //rightLevel general
            $table->boolean('user_menuUserAcessRight') ->default(false);
            $table->boolean('user_resetUserPasswordRight') ->default(false);
            $table->boolean('user_updateDataInDraftRight') ->default(false);
            $table->boolean('user_validateDescriptiveLifeSheetDataRight') ->default(false);
            $table->boolean('user_validateOtherDataRight') ->default(false);
            $table->boolean('user_updateDataValidatedButNotSignedRight') ->default(false);
            $table->boolean('user_updateDescriptiveLifeSheetDataSignedRight') ->default(false);
            $table->boolean('user_makeQualityValidationRight') ->default(false);
            $table->boolean('user_makeTechnicalValidationRight') ->default(false);
            $table->boolean('user_deleteDataNotValidatedLinkedToEqOrMmeRight') ->default(false);
            $table->boolean('user_deleteDataValidatedLinkedToEqOrMmeRight')->default(false);
            $table->boolean('user_deleteDataSignedLinkedToEqOrMmeRight') ->default(false);
            $table->boolean('user_deleteEqOrMmeRight') ->default(false);
            $table->boolean('user_makeReformRight') ->default(false);
            $table->boolean('user_declareNewStateRight') ->default(false);

            //right level for enum and information
            $table->boolean('user_updateEnumRight')->default(false);
            $table->boolean('user_deleteEnumRight')->default(false);
            $table->boolean('user_addEnumRight') ->default(false);
            $table->boolean('user_updateInformationRight') ->default(false);


            //right level for equipment
            $table->boolean('user_makeEqOpValidationRight') ->default(false);
            $table->boolean('user_personTrainedToGeneralPrinciplesOfEqManagementRight') ->default(false);
            $table->date('user_formationEqDate') ->nullable() ;
            $table->boolean('user_makeEqRespValidationRight') ->default(false);

            //right level for MME
            $table->boolean('user_personTrainedToGeneralPrinciplesOfMMEManagementRight') ->default(false);
            $table->date('user_formationMmeDate') ->nullable();
            $table->boolean('user_makeMmeOpValidationRight') ->default(false);
            $table->boolean('user_makeMmeRespValidationRight') ->default(false);

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
