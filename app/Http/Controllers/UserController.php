<?php

 /*
 * Filename : UserController.php
 * Creation date : 14 Jun 2022
 * Update date : 27 Jun 2023
 * This file is used to link the view files and the database that concern the user table.
 * For example : send the informations about users..
 */


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User ;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


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
            if ($user->user_endDate==NULL){
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
                    "user_makeMmeOpValidationRight" => $user->user_makeEqOpValidationRight,
                    "user_makeMmeRespValidationRight" => $user->user_makeEqRespValidationRight,
                    'user_SW03_addArticle' => $user->user_SW03_addArticle,
                    'user_SW03_updateArticle' => $user->user_SW03_updateArticle,
                    'user_SW03_updateArticleSigned' => $user->user_SW03_updateArticleSigned,
                    'user_SW03_addSupplier' => $user->user_SW03_addSupplier,
                    'user_SW03_updateSupplier' => $user->user_SW03_updateSupplier,
                    'user_SW03_updateSupplierSigned' => $user->user_SW03_updateSupplierSigned,
                    'user_SW03_technicalValidate' => $user->user_SW03_technicalValidate,
                ]);
                array_push($container_userInfo,$infoUser);
            }
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
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_menuUserAcessRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

      /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/resetUserPasswordRight/{id} (post)
     * Update the right resetUserPassword of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the resetUserPasswordRight
     */
    public function update_resetUserPasswordRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($userResponsable->user_resetUserPasswordRight){
            if ($user->id!=$userResponsable->id){
                if ($user->user_pseudo=="admin"){
                    return response()->json([
                        'errors' => [
                            'user' => ["You can't modify the rights of the admin"]
                        ]
                    ], 429);
                }
                $user->update([
                    'user_resetUserPasswordRight' => $request->user_value,
                ]);
            }else{
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify your own right"]
                    ]
                ], 429);
            }
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You don't have the right to modify the password of another user"]
                ]
            ], 429);
        }
    }

      /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateDataInDraftRight/{id} (post)
     * Update the right updateDataInDraft of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateDataInDraftRight
     */
    public function update_updateDataInDraftRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_updateDataInDraftRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

      /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/validateDescriptiveLifeSheetDataRight/{id} (post)
     * Update the right validateDescriptiveLifeSheetData of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the validateDescriptiveLifeSheetDataRight
     */
    public function update_validateDescriptiveLifeSheetDataRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_validateDescriptiveLifeSheetDataRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/validateOtherDataRight/{id} (post)
     * Update the right validateOtherData of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the validateOtherDataRight
     */
    public function update_validateOtherDataRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_validateOtherDataRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateDataValidatedButNotSignedRight/{id} (post)
     * Update the right updateDataValidatedButNotSigned of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateDataValidatedButNotSignedRight
     */
    public function update_updateDataValidatedButNotSignedRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_updateDataValidatedButNotSignedRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateDescriptiveLifeSheetDataSignedRight/{id} (post)
     * Update the right updateDescriptiveLifeSheetDataSigned of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateDescriptiveLifeSheetDataSignedRight
     */
    public function update_updateDescriptiveLifeSheetDataSignedRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_updateDescriptiveLifeSheetDataSignedRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route :  /user/update_right/makeQualityValidationRight/{id} (post)
     * Update the right makeQualityValidation of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the makeQualityValidationRight
     */
    public function update_makeQualityValidationRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_makeQualityValidationRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

      /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/makeTechnicalValidationRight/{id} (post)
     * Update the right makeTechnicalValidation of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the makeTechnicalValidationRight
     */
    public function update_makeTechnicalValidationRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_makeTechnicalValidationRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

      /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/makeEqOpValidationRight/{id} (post)
     * Update the right makeEqOpValidation of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the makeEqOpValidationRight
     */
    public function update_makeEqOpValidationRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_makeEqOpValidationRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/makeMmeOpValidationRight/{id} (post)
     * Update the right makeMmeOpValidation of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the makeMmeOpValidationRight
     */
    public function update_makeMmeOpValidationRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_makeMmeOpValidationRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateEnumRight/{id} (post)
     * Update the right updateEnum of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateEnumRight
     */
    public function update_updateEnumRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_updateEnumRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

      /**
     * Function call by AccountManagementElement.vue with the route :  /user/update_right/deleteEnumRight/{id} (post)
     * Update the right deleteEnumRight of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the deleteEnumRight
     */
    public function update_deleteEnumRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_deleteEnumRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/addEnumRight/{id}' (post)
     * Update the right addEnum of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the addEnumRight
     */
    public function update_addEnumRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_addEnumRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/deleteDataNotValidatedLinkedToEqOrMmeRight/{id}' (post)
     * Update the right deleteDataNotValidatedLinkedToEqOrMme of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the deleteDataNotValidatedLinkedToEqOrMmeRight
     */
    public function update_deleteDataNotValidatedLinkedToEqOrMmeRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_deleteDataNotValidatedLinkedToEqOrMmeRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/deleteDataValidatedLinkedToEqOrMmeRight/{id}' (post)
     * Update the right deleteDataValidatedLinkedToEqOrMme of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the deleteDataValidatedLinkedToEqOrMmeRight
     */
    public function update_deleteDataValidatedLinkedToEqOrMmeRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_deleteDataValidatedLinkedToEqOrMmeRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/deleteDataSignedLinkedToEqOrMmeRight/{id}' (post)
     * Update the right deleteDataSignedLinkedToEqOrMme of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the deleteDataSignedLinkedToEqOrMmeRight
     */
    public function update_deleteDataSignedLinkedToEqOrMmeRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_deleteDataSignedLinkedToEqOrMmeRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/deleteEqOrMmeRight/{id}' (post)
     * Update the right deleteEqOrMme of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the deleteEqOrMmeRight
     */
    public function update_deleteEqOrMmeRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_deleteEqOrMmeRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateInformationRight/{id} (post)
     * Update the right updateInformation of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateInformationRight
     */
    public function update_updateInformationRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_updateInformationRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/personTrainedToGeneralPrinciplesOfEqManagementRight/{id} (post)
     * Update the right personTrainedToGeneralPrinciplesOfEqManagement of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the personTrainedToGeneralPrinciplesOfEqManagementRight
     */
    public function update_personTrainedToGeneralPrinciplesOfEqManagementRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_personTrainedToGeneralPrinciplesOfEqManagementRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/personTrainedToGeneralPrinciplesOfMMEManagementRight/{id}(post)
     * Update the right personTrainedToGeneralPrinciplesOfMMEManagementRight of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the personTrainedToGeneralPrinciplesOfMMEManagementRight
     */
    public function update_personTrainedToGeneralPrinciplesOfMMEManagementRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_personTrainedToGeneralPrinciplesOfMMEManagementRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route :  /user/update_right/makeEqRespValidationRight/{id}
     * Update the right makeEqRespValidation, of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the makeEqRespValidationRight
     */
    public function update_makeEqRespValidationRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_makeEqRespValidationRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route :  /user/update_right/makeEqRespValidationRight/{id}
     * Update the right makeEqRespValidation, of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the makeEqRespValidationRight
     */
    public function update_makeMmeRespValidationRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_makeMmeRespValidationRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route :/user/update_right/makeReformRight/{id} (post)
     * Update the right makeReformRight of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the makeReformRight
     */
    public function update_makeReformRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_makeReformRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/declareNewStateRight/{id} (post)
     * Update the right declareNewStateRight of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the declareNewStateRight
     */
    public function update_declareNewStateRight($id, Request $request){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_declareNewStateRight' => $request->user_value,
            ]);
        }else{
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/addArticle/{id} (post)
     * Update the right addArticle of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the addArticle
     */
    public function update_addArticle($id, Request $request)
    {
        $user = User::findOrFail($id);
        $userResponsable = User::findOrFail($request->user_id);
        if ($user->id != $userResponsable->id) {
            if ($user->user_pseudo == "admin") {
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_SW03_addArticle' => $request->user_value,
            ]);
        } else {
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateArticle/{id} (post)
     * Update the right updateArticle of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateArticle
     */
    public function update_updateArticle($id, Request $request)
    {
        $user = User::findOrFail($id);
        $userResponsable = User::findOrFail($request->user_id);
        if ($user->id != $userResponsable->id) {
            if ($user->user_pseudo == "admin") {
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_SW03_updateArticle' => $request->user_value,
            ]);
        } else {
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateArticleSigned/{id} (post)
     * Update the right updateArticleSigned of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateArticleSigned
     */
    public function update_updateArticleSigned($id, Request $request)
    {
        $user = User::findOrFail($id);
        $userResponsable = User::findOrFail($request->user_id);
        if ($user->id != $userResponsable->id) {
            if ($user->user_pseudo == "admin") {
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_SW03_updateArticleSigned' => $request->user_value,
            ]);
        } else {
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/addSupplier/{id} (post)
     * Update the right addSupplier of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the addSupplier
     */
    public function update_addSupplier($id, Request $request)
    {
        $user = User::findOrFail($id);
        $userResponsable = User::findOrFail($request->user_id);
        if ($user->id != $userResponsable->id) {
            if ($user->user_pseudo == "admin") {
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_SW03_addSupplier' => $request->user_value,
            ]);
        } else {
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateSupplier/{id} (post)
     * Update the right updateSupplier of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateSupplier
     */
    public function update_updateSupplier($id, Request $request)
    {
        $user = User::findOrFail($id);
        $userResponsable = User::findOrFail($request->user_id);
        if ($user->id != $userResponsable->id) {
            if ($user->user_pseudo == "admin") {
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_SW03_updateSupplier' => $request->user_value,
            ]);
        } else {
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/updateSupplierSigned/{id} (post)
     * Update the right updateSupplierSigned of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the updateSupplierSigned
     */
    public function update_updateSupplierSigned($id, Request $request)
    {
        $user = User::findOrFail($id);
        $userResponsable = User::findOrFail($request->user_id);
        if ($user->id != $userResponsable->id) {
            if ($user->user_pseudo == "admin") {
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_SW03_updateSupplierSigned' => $request->user_value,
            ]);
        } else {
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by AccountManagementElement.vue with the route : /user/update_right/technicalValidate/{id} (post)
     * Update the right technicalValidate of the user which the id is passed in parameter
     * The id parameter correspond to the id of the user we want to change the technicalValidate
     */
    public function update_technicalValidate($id, Request $request)
    {
        $user = User::findOrFail($id);
        $userResponsable = User::findOrFail($request->user_id);
        if ($user->id != $userResponsable->id) {
            if ($user->user_pseudo == "admin") {
                return response()->json([
                    'errors' => [
                        'user' => ["You can't modify the rights of the admin"]
                    ]
                ], 429);
            }
            $user->update([
                'user_SW03_technicalValidate' => $request->user_value,
            ]);
        } else {
            return response()->json([
                'errors' => [
                    'user' => ["You can't modify your own right"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by AccountManagementElement.vue with the route : /user/update/infos/{id} (post)
     * Update the personnal informations of the user like his password or his initials
     * The id parameter correspond to the id of the user we want to change the informations
     */
    public function update_info(Request $request, $id){
        $user=User::findOrFail($id) ;
        $userResponsable=User::findOrFail($request->user_id) ;
        if ($user->id!=$userResponsable->id){
            if ($user->user_pseudo=="admin"){
                return response()->json([
                    'errors' => [
                        'user_confirmation_password' => ["You can't modify the information of the admin"]
                    ]
                ], 429);
            }

            if ($request->user_initials!=NULL){
                $request->validate([
                    'user_initials' => ['required', 'string', 'max:4', 'min:2'],
                ],[
                    'user_initials.required' => 'You must enter the initials ',
                    'user_initials.string' => 'Your initials must be of type string',
                    'user_initials.max' => 'You must enter a maximum of 4 characters',
                    'user_initials.min' => 'You must enter at least 2 characters',
                ]);

                //We check if user_initials is already used in the data base
                $users=User::where('user_initials', '=', $request->user_initials)->where('id', '<>', $id)->get() ;
                if (count($users)>0){
                    return response()->json([
                        'errors' => [
                            'user_initials' => ["This initials are already used"]
                        ]
                    ], 429);
                }

                $user->update([
                    'user_initials' => $request->user_initials,
                ]);
            }

            if ($request->user_endDate!=NULL){
                if ($request->user_endDate<$user->user_startDate){
                    return response()->json([
                        'errors' => [
                            'user_endDate' => ["You can't entered a leave date that is before start date"]
                        ]
                    ], 429);
                }

                $user->update([
                    'user_endDate' => $request->user_endDate,
                    "user_menuUserAcessRight" => false,
                    "user_resetUserPasswordRight" => false,
                    "user_updateDataInDraftRight" => false,
                    "user_validateDescriptiveLifeSheetDataRight" => false,
                    "user_validateOtherDataRight" => false,
                    "user_updateDataValidatedButNotSignedRight" => false,
                    "user_updateDescriptiveLifeSheetDataSignedRight" => false,
                    "user_makeQualityValidationRight" => false,
                    "user_makeTechnicalValidationRight" => false,
                    "user_makeEqOpValidationRight" => false,
                    "user_makeMmeOpValidationRight" => false,
                    "user_updateEnumRight" => false,
                    "user_deleteEnumRight" => false,
                    "user_addEnumRight" => false,
                    "user_deleteDataNotValidatedLinkedToEqOrMmeRight" => false,
                    "user_deleteDataValidatedLinkedToEqOrMmeRight" => false,
                    "user_deleteDataSignedLinkedToEqOrMmeRight" => false,
                    "user_deleteEqOrMmeRight" => false,
                    "user_updateInformationRight" => false,
                    "user_personTrainedToGeneralPrinciplesOfEqManagementRight" => false,
                    "user_personTrainedToGeneralPrinciplesOfMMEManagementRight" =>false,
                    "user_makeEqRespValidationRight" => false,
                    "user_makeMmeRespValidationRight" => false,
                    "user_makeReformRight" => false,
                    "user_declareNewStateRight" => false,
                    "user_SW03_addArticle" => false,
                    "user_SW03_updateArticle" => false,
                    "user_SW03_updateArticleSigned" => false,
                    "user_SW03_addSupplier" => false,
                    "user_SW03_updateSupplier" => false,
                    "user_SW03_updateSupplierSigned" => false,
                    "user_SW03_technicalValidate" => false,
                ]);
            }

            if ($request->user_formationEqDate!=NULL){
                //we checked if the new date is after the actual if it exist
                if ($user->user_formationEqDate!=NULL && $user->user_formationEqDate>$request->user_formationEqDate){
                    return response()->json([
                        'errors' => [
                            'user_formationEqDate' => ["You have to entered a formation equipment date that is after the previous formation equipment date"]
                        ]
                    ], 429);
                }
                $user->update([
                    'user_formationEqDate' => $request->user_formationEqDate,
                ]);
            }

            if ($request->user_formationMmeDate!=NULL){
                //we checked if the new date is after the actual if it exist
                if ($user->user_formationMmeDate!=NULL && $user->user_formationMmeDate>$request->user_formationMmeDate){
                    return response()->json([
                        'errors' => [
                            'user_formationMmeDate' => ["You have to entered a formation mme date that is after the previous formation mme date"]
                        ]
                    ], 429);
                }
                $user->update([
                    'user_formationMmeDate' => $request->user_formationMmeDate,
                ]);
            }

            if ($request->user_password!=NULL || $request->user_confirmation_password){
                $request->validate([
                'user_password' => ['required', Rules\Password::defaults()],
                'user_confirmation_password' => ['required', Rules\Password::defaults()],
                ],[
                    'user_password.required' => 'You must enter a password',
                    'user_password.string' => 'Your password must be of type string',
                    'user_password.max' => 'You must enter a maximum of 255 characters',
                    'user_password.min' => 'You must enter at least 8 characters',

                    'user_confirmation_password.required' => 'You must confirm your password',
                    'user_confirmation_password.string' => 'Your password must be of type string',
                    'user_confirmation_password.max' => 'You must enter a maximum of 255 characters',
                    'user_confirmation_password.min' => 'You must enter at least 8 characters',
                ]);

                if ($request->user_confirmation_password!==$request->user_password){
                    return response()->json([
                        'errors' => [
                            'user_confirmation_password' => ["These passwords are differents"]
                        ]
                    ], 429);
                }

                $user->update([
                    'password' => Hash::make($request->user_password),
                ]);
            }
        }else{
            return response()->json([
                'errors' => [
                    'user_confirmation_password' => ["You can't modify your own information here, please go in myAccount menu"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by MyAccount.vue with the route : /user/update/myAccount/{id} (post)
     * Update the personnal informations of the user like his password or his pseudo
     * The id parameter correspond to the id of the user we want to change the informations
     */
    public function update_myAccount(Request $request, $id){
        $user=User::findOrFail($id) ;
        if ($request->user_firstName!=NULL){
            $request->validate([
                'user_firstName' => ['required', 'string', 'min:2', 'max:50'],
            ],[
                'user_firstName.required' => 'You must enter your firstname ',
                'user_firstName.string' => 'Your firstName must be of type string',
                'user_firstName.max' => 'You must enter a maximum of 50 characters',
                'user_firstName.min' => 'You must enter at least 2 characters',
            ]);

            $user->update([
                'user_firstName' => $request->user_firstName,
            ]);
        }

        if ($request->user_lastName!=NULL){
            $request->validate([
                'user_lastName' => ['required', 'string', 'min:2', 'max:50'],
            ],[
                'user_lastName.required' => 'You must enter your lastName ',
                'user_lastName.string' => 'Your lastName must be of type string',
                'user_lastName.max' => 'You must enter a maximum of 50 characters',
                'user_lastName.min' => 'You must enter at least 2 characters',
            ]);
            $user->update([
                'user_lastName' => $request->user_lastName,
            ]);
        }

        if ($request->user_signaturePath!=NULL){
            $request->validate([
                'user_signaturePath' => ['max:50'],
            ],[
                'user_signaturePath.string' => 'Your signature path must be of type string',
                'user_signaturePath.max' => 'You must enter a maximum of 50 characters',
            ]);
            $user->update([
                'user_signaturePath' => $request->user_signaturePath,
            ]);
        }

        if ($request->user_password!=NULL){
            $request->validate([
               'user_password' => ['required', Rules\Password::defaults()],
               'user_confirmation_password' => ['required', Rules\Password::defaults()],
            ],[
                'user_password.required' => 'You must enter a password',
                'user_password.string' => 'Your password must be of type string',
                'user_password.max' => 'You must enter a maximum of 255 characters',
                'user_password.min' => 'You must enter at least 8 characters',

                'user_confirmation_password.required' => 'You must confirm your password',
                'user_confirmation_password.string' => 'Your password must be of type string',
                'user_confirmation_password.max' => 'You must enter a maximum of 255 characters',
                'user_confirmation_password.min' => 'You must enter at least 8 characters',
            ]);

            if ($request->user_confirmation_password!==$request->user_password){
                return response()->json([
                    'errors' => [
                        'user_confirmation_password' => ["These passwords are differents"]
                    ]
                ], 429);
            }

            $user->update([
                'password' => Hash::make($request->user_password),
            ]);
        }

        if ($request->user_confirmation_password!=NULL){
            $request->validate([
                'user_password' => ['required', Rules\Password::defaults()],
                'user_confirmation_password' => ['required', Rules\Password::defaults()],
             ],[
                 'user_password.required' => 'You must enter a password',
                 'user_password.string' => 'Your password must be of type string',
                 'user_password.max' => 'You must enter a maximum of 255 characters',
                 'user_password.min' => 'You must enter at least 8 characters',

                 'user_confirmation_password.required' => 'You must confirm your password',
                 'user_confirmation_password.string' => 'Your password must be of type string',
                 'user_confirmation_password.max' => 'You must enter a maximum of 255 characters',
                 'user_confirmation_password.min' => 'You must enter at least 8 characters',
             ]);

            if ($request->user_confirmation_password!==$request->user_password){
                 return response()->json([
                     'errors' => [
                         'user_confirmation_password' => ["These passwords are differents"]
                     ]
                 ], 429);
            }

            $user->update([
                'password' => Hash::make($request->user_password),
            ]);
        }
        if ($request->user_pseudo!=NULL){
            $request->validate([
                'user_pseudo' => ['required', 'string', 'max:50', 'min:2'],
            ],[
                'user_pseudo.required' => 'You must enter a pseudo ',
                'user_pseudo.string' => 'Your pseudo must be of type string',
                'user_pseudo.max' => 'You must enter a maximum of 50 characters',
                'user_pseudo.min' => 'You must enter at least 2 characters',
            ]);

            //We check if user_pseudo is already used in the data base
            $users=User::where('user_pseudo', '=', $request->user_pseudo)->where('id','<>',$id)->get() ;
            if (count($users)>0){
                return response()->json([
                    'errors' => [
                        'user_pseudo' => ["This username is already used"]
                    ]
                ], 429);
            }
            $user->update([
                'user_pseudo' => $request->user_pseudo,
            ]);
        }

        if ($request->user_formationEqDate!=NULL){
            //we checked if the new date is after the actual if it exist
            if ($user->user_formationEqDate!=NULL && $user->user_formationEqDate>$request->user_formationEqDate){
                return response()->json([
                    'errors' => [
                        'user_formationEqDate' => ["You have to entered a formation equipment date that is after the previous formation equipment date"]
                    ]
                ], 429);
            }
            $user->update([
                'user_formationEqDate' => $request->user_formationEqDate,
            ]);
        }

        if ($request->user_formationMmeDate!=NULL){
            //we checked if the new date is after the actual if it exist
            if ($user->user_formationMmeDate!=NULL && $user->user_formationMmeDate>$request->user_formationMmeDate){
                return response()->json([
                    'errors' => [
                        'user_formationMmeDate' => ["You have to entered a formation mme date that is after the previous formation mme date"]
                    ]
                ], 429);
            }
            $user->update([
                'user_formationMmeDate' => $request->user_formationMmeDate,
            ]);
        }

    }

       /**
     * Function call by MyAccount.vue with the route : /user/get/formationEqOk/{id}  (post)
     * Get if the date of equipment formation is already available
     * The id parameter correspond to the id of the user we want to know if the equipment formation date is already available
     */
    public function formationEqOk($id){
        $user=User::findOrFail($id) ;
        $now=Carbon::now() ;
        if ($user->user_formationEqDate!=NULL){
            $ymd=explode('-', $user->user_formationEqDate);
            $year=$ymd[0] ;
            $month=$ymd[1] ;
            $day=$ymd[2] ;

            $formationEqDate=Carbon::create($year, $month, $day, 0,0,0);
            $OneYearLater=$formationEqDate->addYear(1) ;
            if ($OneYearLater<$now){
                return response()->json(false) ;
            }else{
                return response()->json(true) ;
            }
        }else{
            return response()->json(false) ;
        }
    }

    public function formationMmeOk($id){
        $user=User::findOrFail($id) ;
        $now=Carbon::now() ;
        if ($user->user_formationMmeDate!=NULL){
            $ymd=explode('-', $user->user_formationMmeDate);
            $year=$ymd[0] ;
            $month=$ymd[1] ;
            $day=$ymd[2] ;

            $formationMmeDate=Carbon::create($year, $month, $day, 0,0,0);
            $OneYearLater=$formationMmeDate->addYear(1) ;
            if ($OneYearLater<$now){
                return response()->json(false) ;
            }else{
                return response()->json(true) ;
            }
        }else{
            return response()->json(false) ;
        }
    }
}
