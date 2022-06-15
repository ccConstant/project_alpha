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


    /**
     * Function call by AccountsManagement.vue with the route : /users/send (get)
     * Get all the users with their rights and send this information to the view
     * @return \Illuminate\Http\Response
     */
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
                "user_deleteDataSignedLinkedToEqOrMmeRight" => $user->user_deleteDataSignedLinkedToEqOrMmeRight,
                "user_deleteEqOrMmeRight" => $user->user_deleteEqOrMmeRight,
                "user_updateInformationRight" => $user->user_updateInformationRight,
                "user_personTrainedToGeneralPrinciplesOfEqManagementRight" => $user->user_personTrainedToGeneralPrinciplesOfEqManagementRight,
                "user_formationEqDate" => $user->user_formationEqDate,
                "user_personTrainedToGeneralPrinciplesOfMMEManagementRight" => $user->user_personTrainedToGeneralPrinciplesOfMMEManagementRight,
                "user_formationMmeDate" => $user->user_formationMmeDate,
                "user_makeEqRespValidationRight" => $user->user_makeEqRespValidationRight,
                "user_makeReformRight" => $user->user_makeReformRight,
                "user_declareNewStateRight" => $user->user_declareNewStateRight,
            ]);
            array_push($container_userInfo,$infoUser);
        }
        return response()->json($container_userInfo) ;
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/menuUserAcessRight/{id} (post)
     * Update the right menuUserAccess of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the menuUserAcessRight
     */
    public function update_menuUserAcessRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_menuUserAcessRight' => $request->user_menuUserAcessRight,
        ]);
    }

      /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/resetUserPasswordRight/{id} (post)
     * Update the right resetUserPassword of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the resetUserPasswordRight
     */
    public function update_resetUserPasswordRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_resetUserPasswordRight' => $request->user_resetUserPasswordRight,
        ]);
    }

      /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateDataInDraftRight/{id} (post)
     * Update the right updateDataInDraft of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateDataInDraftRight
     */
    public function update_updateDataInDraftRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_updateDataInDraftRight' => $request->user_updateDataInDraftRight,
        ]);
    }

      /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/validateDescriptiveLifeSheetDataRight/{id} (post)
     * Update the right validateDescriptiveLifeSheetData of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the validateDescriptiveLifeSheetDataRight
     */
    public function update_validateDescriptiveLifeSheetDataRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_validateDescriptiveLifeSheetDataRight' => $request->user_validateDescriptiveLifeSheetDataRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/validateOtherDataRight/{id} (post)
     * Update the right validateOtherData of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the validateOtherDataRight
     */
    public function update_validateOtherDataRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_validateOtherDataRight' => $request->user_validateOtherDataRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateDataValidatedButNotSignedRight/{id} (post)
     * Update the right updateDataValidatedButNotSigned of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateDataValidatedButNotSignedRight
     */
    public function update_updateDataValidatedButNotSignedRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_updateDataValidatedButNotSignedRight' => $request->user_updateDataValidatedButNotSignedRight,
        ]);
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateDescriptiveLifeSheetDataSignedRight/{id} (post)
     * Update the right updateDescriptiveLifeSheetDataSigned of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateDescriptiveLifeSheetDataSignedRight
     */
    public function update_updateDescriptiveLifeSheetDataSignedRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_updateDescriptiveLifeSheetDataSignedRight' => $request->user_updateDescriptiveLifeSheetDataSignedRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route :  /user/update_right/makeQualityValidationRight/{id} (post)
     * Update the right makeQualityValidation of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the makeQualityValidationRight
     */
    public function update_makeQualityValidationRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_makeQualityValidationRight' => $request->user_makeQualityValidationRight,
        ]);
    }

      /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/makeTechnicalValidationRight/{id} (post)
     * Update the right makeTechnicalValidation of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the makeTechnicalValidationRight
     */
    public function update_makeTechnicalValidationRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_makeTechnicalValidationRight' => $request->user_makeTechnicalValidationRight,
        ]);
    }

      /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/makeEqOpValidationRight/{id} (post)
     * Update the right makeEqOpValidation of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the makeEqOpValidationRight
     */
    public function update_makeEqOpValidationRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_makeEqOpValidationRight' => $request->user_makeEqOpValidationRight,
        ]);
    }
    
    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateEnumRight/{id} (post)
     * Update the right updateEnum of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateEnumRight
     */
    public function update_updateEnumRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_updateEnumRight' => $request->user_updateEnumRight,
        ]);
    }

      /**
     * Function call by AccountManagementElement.vue with the route :  /user/update_right/deleteEnumRight/{id} (post)
     * Update the right deleteEnumRight of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the deleteEnumRight
     */
    public function update_deleteEnumRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_deleteEnumRight' => $request->user_deleteEnumRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/addEnumRight/{id}' (post)
     * Update the right addEnum of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the addEnumRight
     */
    public function update_addEnumRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_addEnumRight' => $request->user_addEnumRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/deleteDataNotValidatedLinkedToEqOrMmeRight/{id}' (post)
     * Update the right deleteDataNotValidatedLinkedToEqOrMme of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the deleteDataNotValidatedLinkedToEqOrMmeRight
     */
    public function update_deleteDataNotValidatedLinkedToEqOrMmeRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_deleteDataNotValidatedLinkedToEqOrMmeRight' => $request->user_deleteDataNotValidatedLinkedToEqOrMmeRight,
        ]);
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/deleteDataValidatedLinkedToEqOrMmeRight/{id}' (post)
     * Update the right deleteDataValidatedLinkedToEqOrMme of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the deleteDataValidatedLinkedToEqOrMmeRight
     */
    public function update_deleteDataValidatedLinkedToEqOrMmeRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_deleteDataValidatedLinkedToEqOrMmeRight' => $request->user_deleteDataValidatedLinkedToEqOrMmeRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/deleteDataSignedLinkedToEqOrMmeRight/{id}' (post)
     * Update the right deleteDataSignedLinkedToEqOrMme of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the deleteDataSignedLinkedToEqOrMmeRight
     */
    public function update_deleteDataSignedLinkedToEqOrMmeRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_deleteDataSignedLinkedToEqOrMmeRight' => $request->user_deleteDataSignedLinkedToEqOrEcmeRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/deleteEqOrMmeRight/{id}' (post)
     * Update the right deleteEqOrMme of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the deleteEqOrMmeRight
     */
    public function update_deleteEqOrMmeRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_deleteEqOrMmeRight' => $request->user_deleteEqOrMmeRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateInformationRight/{id} (post)
     * Update the right updateInformation of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateInformationRight
     */
    public function update_updateInformationRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_updateInformationRight' => $request->user_updateInformationRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/personTrainedToGeneralPrinciplesOfEqManagementRight/{id} (post)
     * Update the right personTrainedToGeneralPrinciplesOfEqManagement of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the personTrainedToGeneralPrinciplesOfEqManagementRight
     */
    public function update_personTrainedToGeneralPrinciplesOfEqManagementRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_personTrainedToGeneralPrinciplesOfEqManagementRight' => $request->user_personTrainedToGeneralPrinciplesOfEqManagementRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/personTrainedToGeneralPrinciplesOfMMEManagementRight/{id}(post)
     * Update the right personTrainedToGeneralPrinciplesOfMMEManagementRight of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the personTrainedToGeneralPrinciplesOfMMEManagementRight
     */
    public function update_personTrainedToGeneralPrinciplesOfMMEManagementRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_personTrainedToGeneralPrinciplesOfMMEManagementRight' => $request->user_personTrainedToGeneralPrinciplesOfMMEManagementRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route :  /user/update_right/makeEqRespValidationRight/{id}
     * Update the right makeEqRespValidation, of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the makeEqRespValidationRight
     */
    public function update_makeEqRespValidationRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_makeEqRespValidationRight' => $request->user_makeEqRespValidationRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route :/user/update_right/makeReformRight/{id} (post)
     * Update the right makeReformRight of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the makeReformRight
     */
    public function update_makeReformRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_makeReformRight' => $request->user_makeReformRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/declareNewStateRight/{id} (post)
     * Update the right declareNewStateRight of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the declareNewStateRight
     */
    public function update_declareNewStateRight($id, Request $request){
        $user=User::findOrFail($id) ; 
        $user->update([
            'user_declareNewStateRight' => $request->user_declareNewStateRight,
        ]);
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update/infos/{id} (post)
     * Update the personnal informations of the user like his password or his initials
     * The id parameter correspond to the id of the user we want to change the informations
     */
    public function update_info($id, Request $request){
        $user=User::findOrFail($id) ; 

        $request->validate([
            'user_initials' => ['required', 'string', 'max:4', 'min:2'],
           'user_password' => ['required', Rules\Password::defaults()], 
           'user_confirmation_password' => ['required', Rules\Password::defaults()], 
        ],[
            'user_initials.required' => 'You must enter the initials ',
            'user_initials.string' => 'Your firstName must be of type string',
            'user_initials.max' => 'You must enter a maximum of 4 characters',
            'user_initials.min' => 'You must enter a at least 2 characters',

            'user_password.required' => 'You must enter a password',
            'user_password.string' => 'Your password must be of type string',
            'user_password.max' => 'You must enter a maximum of 255 characters',
            'user_password.min' => 'You must enter at least 8 characters',

            'user_confirmation_password.required' => 'You must confirm your password',
            'user_confirmation_password.string' => 'Your password must be of type string',
            'user_confirmation_password.max' => 'You must enter a maximum of 255 characters',
            'user_confirmation_password.min' => 'You must enter at least 8 characters',
        ]);

        $user->update([
            'password' => Hash::make($request->user_password),
            'user_initials' => $request->user_initials,
        ]);
    }

}
