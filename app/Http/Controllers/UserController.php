<?php

/*
* Filename : UserController.php 
* Creation date : 14 Jun 2022
* Update date : 14 Jun 2022
* This file is used to link the view files and the database that concern the user table. 
* For example : send the informations about users..
*/ 



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User ; 

class UserController extends Controller{

    public function send_users(){
        $users=User::all() ; 
        $container_userInfo=array() ;
        foreach($users as $user){
            $infoUser=([
                "id" => $user->id,
                "user_firstName" => $user->user_firstName,
                "user_lastName" => $user->user_lastName,
                "user_initials" => $user->user_initials,
                "user_signaturePath" => $user->user_signaturePath,
                "user_pseudo" => $user->user_pseudo,
                "user_password" => $user->password,
                "user_menuUserAcessRight" => $user->user_menuUserAcessRight,
                "user_resetUserPasswordRight" => $user->user_resetUserPasswordRight,
                "user_updateDataInDraftRight" => $user->user_updateDataInDraftRight,
                "user_validateDescriptiveLifeSheetDataRight" => $user->user_validateDescriptiveLifeSheetDataRight,
                "user_validateOtherDataRight" => $user->user_validateOtherDataRight,
                "user_updateDataValidatedButNotSignedRight" => $user->user_updateDataValidatedButNotSignedRight,
                "user_updateDescriptiveLifeSheetDataSignedRight" => $user->user_updateDescriptiveLifeSheetDataSignedRight,
                "user_makeQualityValidationRight" => $user->user_makeQualityValidationRight,
                "user_makeTechnicalValidationRight" => $user->user_makeTechnicalValidationRight,
                "user_makeEqOpValidationRight" => $user->user_makeEqOpValidationRight,
                "user_updateEnumRight" => $user->user_updateEnumRight,
                "user_deleteEnumRight" => $user->user_deleteEnumRight,
                "user_addEnumRight" => $user->user_addEnumRight,
                "user_deleteDataNotValidatedLinkedToEqOrMmeRight" => $user->user_deleteDataNotValidatedLinkedToEqOrMmeRight,
                "user_deleteDataValidatedLinkedToEqOrMmeRight" => $user->user_deleteDataValidatedLinkedToEqOrMmeRight,
                "user_deleteDataSignedLinkedToEqOrEcmeRight" => $user->user_deleteDataSignedLinkedToEqOrEcmeRight,
                "user_deleteEqOrMmeRight" => $user->user_deleteEqOrMmeRight,
                "user_updateInformationRight" => $user->user_updateInformationRight,
                "user_personTrainedToGeneralPrinciplesOfEqManagementRight" => $user->user_personTrainedToGeneralPrinciplesOfEqManagementRight,
                "user_formationEqDate" => $user->user_formationEqDate,
                "user_personTrainedToGeneralPrinciplesOfMMEManagementRight" => $user->user_personTrainedToGeneralPrinciplesOfMMEManagementRight,
                "user_formationMmeDate" => $user->user_formationMmeDate,
            ]);
            array_push($container_userInfo,$infoUser);
        }
        return response()->json($container_userInfo) ;
    }
}
