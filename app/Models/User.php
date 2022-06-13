<?php

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
        'user_password',
        'user_pseudo',
        'user_menuUserAcessRight',
        'user_resetUserPasswordRight',
        'user_updateDataInDraftRight',
        'user_validateDescriptiveLifeSheetDataRight',
        'user_validateOtherDataRight',
        'user_updateDataValidatedButNotSignedRight',
        'user_updateDescriptiveLifeSheetDataSignedRight',
        'user_makeQualityValidationRight',
        'user_makeTechnicalValidationRight',
        'user_makeEqRespValidationRight',
        'user_makeOpValidationRight',
        'user_updateEnumRight',
        'user_deleteEnumRight',
        'user_addEnumRight',
        'user_deleteDataNotValidatedLinkedToEqOrMmeRight',
        'user_deleteDataValidatedLinkedToEqOrMmeRight',
        'user_deleteDataSignedLinkedToEqOrEcmeRight',
        'user_deleteEqOrMmeRight',
        'user_updateInformationRight',
        'user_personTrainedToGeneralPrinciplesOfEqManagementRight',
        'user_formationEqDate',
        'user_personTrainedToGeneralPrinciplesOfMMEManagementRight',
        'user_formationMmeDate'
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
