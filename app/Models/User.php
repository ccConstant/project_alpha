<?php

/*
* Filename : User.php
* Creation date : 9 Jun 2022
* Update date : 8 Feb 2023
* This file define the model User. We can see more details about this model (like his attributes) in the
* migration file named "2014_10_12_000000_create_users_table.php" in Database>migrations."
*
*/

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_firstName',
        'user_lastName',
        'user_initials',
        'user_signaturePath',
        'password',
        'user_startDate',
        'user_endDate',
        'user_pseudo',

        //Right level general
        'user_menuUserAcessRight',
        'user_resetUserPasswordRight',
        'user_updateDataInDraftRight',
        'user_validateDescriptiveLifeSheetDataRight',
        'user_validateOtherDataRight',
        'user_updateDataValidatedButNotSignedRight',
        'user_updateDescriptiveLifeSheetDataSignedRight',
        'user_makeQualityValidationRight',
        'user_makeTechnicalValidationRight',
        'user_deleteDataNotValidatedLinkedToEqOrMmeRight',
        'user_deleteDataValidatedLinkedToEqOrMmeRight',
        'user_deleteDataSignedLinkedToEqOrMmeRight',
        'user_deleteEqOrMmeRight',
        'user_makeReformRight',
        'user_declareNewStateRight',

        //right level for enum and information
        'user_updateEnumRight',
        'user_deleteEnumRight',
        'user_addEnumRight',
        'user_updateInformationRight',

        //right level for equipment
        'user_makeEqOpValidationRight',
        'user_personTrainedToGeneralPrinciplesOfEqManagementRight',
        'user_formationEqDate',
        'user_makeEqRespValidationRight',

        //right level for mme
        'user_personTrainedToGeneralPrinciplesOfMMEManagementRight',
        'user_formationMmeDate',
        'user_makeMmeOpValidationRight',
        'user_makeMmeRespValidationRight',

        //right level for article
        'user_SW03_addArticle',
        'user_SW03_updateArticle',
        'user_SW03_updateArticleSigned',

        //right level for supplier
        'user_SW03_addSupplier',
        'user_SW03_updateSupplier',
        'user_SW03_updateSupplierSigned',

        //right level for SW03
        'user_SW03_technicalValidate',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
