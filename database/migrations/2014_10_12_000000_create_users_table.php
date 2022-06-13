<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_firstName') ;
            $table->string('user_lastName') ;
            $table->string('user_initials') ->nullable();
            $table->string('user_signaturePath') ->nullable();
            $table->string('user_pseudo') ;
            $table->string('user_password') ;
             //rightLevel 
            $table->string('user_menuUserAcessRight') ->nullable();
            $table->string('user_resetUserPasswordRight') ->nullable();
            $table->string('user_updateDataInDraftRight') ->nullable();
            $table->string('user_validateDescriptiveLifeSheetDataRight') ->nullable();
            $table->string('user_validateOtherDataRight') ->nullable();
            $table->string('user_updateDataValidatedButNotSignedRight') ->nullable();
            $table->string('user_updateDescriptiveLifeSheetDataSignedRight') ->nullable();
            $table->string('user_makeQualityValidationRight') ->nullable();
            $table->string('user_makeTechnicalValidationRight') ->nullable();
            $table->string('user_makeEqRespValidationRight') ->nullable();
            $table->string('user_makeOpValidationRight') ->nullable();
            $table->string('user_updateEnumRight') ->nullable();
            $table->string('user_deleteEnumRight') ->nullable();
            $table->string('user_addEnumRight') ->nullable();
            $table->string('user_deleteDataNotValidatedLinkedToEqOrMmeRight') ->nullable();
            $table->string('user_deleteDataValidatedLinkedToEqOrMmeRight') ->nullable();
            $table->string('user_deleteDataSignedLinkedToEqOrEcmeRight') ->nullable();
            $table->string('user_deleteEqOrMmeRight') ->nullable();
            $table->string('user_updateInformationRight') ->nullable();
            $table->string('user_personTrainedToGeneralPrinciplesOfEqManagementRight') ->nullable();
            $table->string('user_formationEqDate') ->nullable();
            $table->string('user_personTrainedToGeneralPrinciplesOfMMEManagementRight') ->nullable();
            $table->string('user_formationMmeDate') ->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
