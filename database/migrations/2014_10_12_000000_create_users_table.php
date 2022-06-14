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
            $table->boolean('user_menuUserAcessRight') ->default(false);
            $table->boolean('user_resetUserPasswordRight') ->default(false);
            $table->boolean('user_updateDataInDraftRight') ->default(false);
            $table->boolean('user_validateDescriptiveLifeSheetDataRight') ->default(false);
            $table->boolean('user_validateOtherDataRight') ->default(false);
            $table->boolean('user_updateDataValidatedButNotSignedRight') ->default(false);
            $table->boolean('user_updateDescriptiveLifeSheetDataSignedRight') ->default(false);
            $table->boolean('user_makeQualityValidationRight') ->default(false);
            $table->boolean('user_makeTechnicalValidationRight') ->default(false);
            $table->boolean('user_makeEqOpValidationRight') ->default(false);
            $table->boolean('user_updateEnumRight')->default(false);
            $table->boolean('user_deleteEnumRight')->default(false);
            $table->boolean('user_addEnumRight') ->default(false);
            $table->boolean('user_deleteDataNotValidatedLinkedToEqOrMmeRight') ->default(false);
            $table->boolean('user_deleteDataValidatedLinkedToEqOrMmeRight')->default(false);
            $table->boolean('user_deleteDataSignedLinkedToEqOrEcmeRight') ->default(false);
            $table->boolean('user_deleteEqOrMmeRight') ->default(false);
            $table->boolean('user_updateInformationRight') ->default(false);
            $table->boolean('user_personTrainedToGeneralPrinciplesOfEqManagementRight') ->default(false);
            $table->boolean('user_formationEqDate') ->default(false);
            $table->boolean('user_personTrainedToGeneralPrinciplesOfMMEManagementRight') ->default(false);
            $table->boolean('user_formationMmeDate') ->default(false);
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
