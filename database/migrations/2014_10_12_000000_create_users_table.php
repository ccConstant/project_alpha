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
            $table->boolean('user_menuUserAcessRight') ->nullable();
            $table->boolean('user_resetUserPasswordRight') ->nullable();
            $table->boolean('user_updateDataInDraftRight') ->nullable();
            $table->boolean('user_validateDescriptiveLifeSheetDataRight') ->nullable();
            $table->boolean('user_validateOtherDataRight') ->nullable();
            $table->boolean('user_updateDataValidatedButNotSignedRight') ->nullable();
            $table->boolean('user_updateDescriptiveLifeSheetDataSignedRight') ->nullable();
            $table->boolean('user_makeQualityValidationRight') ->nullable();
            $table->boolean('user_makeTechnicalValidationRight') ->nullable();
            $table->boolean('user_makeEqRespValidationRight') ->nullable();
            $table->boolean('user_makeOpValidationRight') ->nullable();
            $table->boolean('user_updateEnumRight') ->nullable();
            $table->boolean('user_deleteEnumRight') ->nullable();
            $table->boolean('user_addEnumRight') ->nullable();
            $table->boolean('user_deleteDataNotValidatedLinkedToEqOrMmeRight') ->nullable();
            $table->boolean('user_deleteDataValidatedLinkedToEqOrMmeRight') ->nullable();
            $table->boolean('user_deleteDataSignedLinkedToEqOrEcmeRight') ->nullable();
            $table->boolean('user_deleteEqOrMmeRight') ->nullable();
            $table->boolean('user_updateInformationRight') ->nullable();
            $table->boolean('user_personTrainedToGeneralPrinciplesOfEqManagementRight') ->nullable();
            $table->boolean('user_formationEqDate') ->nullable();
            $table->boolean('user_personTrainedToGeneralPrinciplesOfMMEManagementRight') ->nullable();
            $table->boolean('user_formationMmeDate') ->nullable();
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
