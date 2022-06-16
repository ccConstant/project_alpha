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
            $table->string('user_signaturePath')->nullable();
            $table->string('user_pseudo') ;
            $table->string('password') ;
            $table->date('user_startDate') ;
            $table->date('user_endDate') -> nullable(); 
             //rightLevel 
            $table->boolean('user_menuUserAcessRight') ->default(true);
            $table->boolean('user_resetUserPasswordRight') ->default(true);
            $table->boolean('user_updateDataInDraftRight') ->default(true);
            $table->boolean('user_validateDescriptiveLifeSheetDataRight') ->default(true);
            $table->boolean('user_validateOtherDataRight') ->default(true);
            $table->boolean('user_updateDataValidatedButNotSignedRight') ->default(true);
            $table->boolean('user_updateDescriptiveLifeSheetDataSignedRight') ->default(true);
            $table->boolean('user_makeQualityValidationRight') ->default(true);
            $table->boolean('user_makeTechnicalValidationRight') ->default(true);
            $table->boolean('user_makeEqOpValidationRight') ->default(true);
            $table->boolean('user_updateEnumRight')->default(true);
            $table->boolean('user_deleteEnumRight')->default(true);
            $table->boolean('user_addEnumRight') ->default(true);
            $table->boolean('user_deleteDataNotValidatedLinkedToEqOrMmeRight') ->default(true);
            $table->boolean('user_deleteDataValidatedLinkedToEqOrMmeRight')->default(true);
            $table->boolean('user_deleteDataSignedLinkedToEqOrEcmeRight') ->default(true);
            $table->boolean('user_deleteEqOrMmeRight') ->default(true);
            $table->boolean('user_updateInformationRight') ->default(true);
            $table->boolean('user_personTrainedToGeneralPrinciplesOfEqManagementRight') ->default(true);
            $table->boolean('user_formationEqDate') ->default(true);
            $table->boolean('user_personTrainedToGeneralPrinciplesOfMMEManagementRight') ->default(true);
            $table->boolean('user_formationMmeDate') ->default(true);
            $table->boolean('user_makeEqRespValidationRight') ->default(true);
            $table->boolean('user_makeReformRight') ->default(true);
            $table->boolean('user_declareNewStateRight') ->default(true);
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
