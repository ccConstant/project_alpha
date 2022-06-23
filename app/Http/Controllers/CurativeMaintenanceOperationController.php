<?php

/*
* Filename : CurativeMaintenanceOperationController.php 
* Creation date : 25 May 2022
* Update date : 25 May 2022
* This file is used to link the view files and the database that concern the CurativeMaintenanceOperation table. 
* For example : add a CurativeMaintenanceOperation for an equipment in the data base, update it, delete it...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EquipmentTemp ; 
use App\Models\CurativeMaintenanceOperation ; 
use App\Models\Equipment ; 
use App\Models\MmeState ; 
use App\Models\State ; 
use App\Models\User ; 
use App\Http\Controllers\PowerController ; 
use App\Http\Controllers\FileController ; 
use App\Http\Controllers\UsageController ; 
use App\Http\Controllers\StateController ; 
use App\Http\Controllers\RiskController ; 
use App\Http\Controllers\DimensionController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CurativeMaintenanceOperationController extends Controller
{


    /*
                GENERALE FUNCTION FOR CURATIVE MAINTENANCE OPERATION 
     /**
    

    /**
     * Function call by EquipmentCurMtnOpForm.vue when we want to delete a curative maintenance operation with the route : /state/delete/curMtnOp/{id}(post)
     * Delete a curative maintenance operation thanks to the id given in parameter
     * The id parameter correspond to the id of the curative maintenance operation we want to delete
     * */
    public function delete_curMtnOp($id){
        $curMtnOp=CurativeMaintenanceOperation::findOrFail($id);
        if ($curMtnOp->curMtnOp_validate=='validated'){
            return response()->json([
                'errors' => [
                    'curMtnOp_delete' => ["You can't delete a curative maintenance operation validated"]
                ]
            ], 429);
        }else{
            $curMtnOp->delete() ; 
        }
    }

     /**
     * Function call by CurMtnOpModal.vue when we want to approuve a preventive maintenance operation realized with the route : /curMtnOp/technicalVerifier/{id}
     * Check technically a curative maintenance operation 
     * The id parameter correspond to the id of the curative maintenance operation we want to check
     * */
    public function technicalVerification_curMtnOp(Request $request, $id){
        $user=User::findOrFail($request->user_id) ; 
        
        if (!Auth::attempt(['user_pseudo' => $request->user_pseudo, 'password' => $request->user_password])) {
            return response()->json([
                'errors' => [
                    'connexion' => ["Verification failed, the couple pseudo password isn't recognized"]
                ]
            ], 429);
        }
        
        $curMtnOp=CurativeMaintenanceOperation::findOrFail($id) ; 
        $curMtnOp->update([
            'technicalVerifier_id' => $user->id, 
        ]);
    }

     /**
     * Function call by  CurMtnOpModal.vue when we want to tell that we are the realizator of a curative maintenance operation with the route : /curMtnOp/realize/{id}
     * Tell that you have realize a curative maintenance operation 
     * The id parameter correspond to the id of the curative maintenance operation we want to entered the realizator
     * */
    public function realize_curMtnOp(Request $request, $id){
        $user=User::findOrFail($request->user_id) ; 
        
        if (!Auth::attempt(['user_pseudo' => $request->user_pseudo, 'password' => $request->user_password])) {
            return response()->json([
                'errors' => [
                    'connexion' => ["Verification failed, the couple pseudo password isn't recognized"]
                ]
            ], 429);
        }

        $curMtnOp=CurativeMaintenanceOperation::findOrFail($id) ; 
        $curMtnOp->update([
            'realizedBy_id' => $user->id, 
        ]);
    }

    /**
     * Function call by CurMtnOpModal.vue when we want to approuve a preventive maintenance operation realized with the route : /curMtnOp/qualityVerifier/{id}
     * Check qualitatively a curative maintenance operation 
     * The id parameter correspond to the id of the curative maintenance operation we want to check
     * */
    public function qualityVerification_curMtnOp(Request $request, $id){
        $user=User::findOrFail($request->user_id) ; 
        
        if (!Auth::attempt(['user_pseudo' => $request->user_pseudo, 'password' => $request->user_password])) {
            return response()->json([
                'errors' => [
                    'connexion' => ["Verification failed, the couple pseudo password isn't recognized"]
                ]
            ], 429);
        }
        
        $curMtnOp=CurativeMaintenanceOperation::findOrFail($id) ; 
        $curMtnOp->update([
            'qualityVerifier_id' => $user->id, 
        ]);
    }


    /*
                FUNCTION FOR CURATIVE MAINTENANCE OPERATION LINKED TO EQUIPMENT
    /**

    /**
     * Function call by EquipmentCurMtnOpForm.vue when the form is submitted for insert with the route :/equipment/add/state/curMtnOp (post)
     * Add a new enregistrement of curative maintenance operation in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new curMtnOp
     */
    public function add_curMtnOp_eq(Request $request){
        $state=State::findOrFail($request->state_id) ; 
        $curMtnOpsInEq=CurativeMaintenanceOperation::where('state_id', '=', $request->state_id)->get();
        $max_number=1 ; 
        if (count($curMtnOpsInEq)!=0){
            foreach ($curMtnOpsInEq as $curMtnOpInEq){
                $number=intval($curMtnOpInEq->curMtnOp_number) ; 
                if ($number>$max_number){
                    $max_number=$curMtnOpInEq->curMtnOp_number ; 
                }
            }
            $max_number=$max_number+1 ;
        }
        
        
        //Creation of a new curative maintenance operation
        $curMtnOp=CurativeMaintenanceOperation::create([
            'curMtnOp_reportNumber' => $request->curMtnOp_reportNumber,
            'curMtnOp_validate' => $request->curMtnOp_validate,
            'curMtnOp_description' => $request->curMtnOp_description,
            'curMtnOp_startDate' => $request->curMtnOp_startDate,
            'curMtnOp_endDate' => $request->curMtnOp_endDate,
            'state_id' => $request->state_id,   
            'curMtnOp_number' => $max_number,
            'enteredBy_id' => $request->enteredBy_id,

        ]) ; 
        
        $curMtnOp_id=$curMtnOp->id;
        return response()->json($curMtnOp->id) ; 
    }

     /**
     * Function call by EquipmentCurMtnOpForm.vue when the form is submitted for update with the route :/equipment/update/state/curMtnOp/{id} (post)
     * Update an enregistrement of curative maintenance operation in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the curative maintenance operation we want to update
     * */
    public function update_curMtnOp_eq(Request $request, $id){
        $curMtnOp=CurativeMaintenanceOperation::FindOrFail($id) ;
        $curMtnOp->update([
            'curMtnOp_reportNumber' => $request->curMtnOp_reportNumber,
            'curMtnOp_validate' => $request->curMtnOp_validate,
            'curMtnOp_description' => $request->curMtnOp_description,
            'curMtnOp_startDate' => $request->curMtnOp_startDate,
            'curMtnOp_endDate' => $request->curMtnOp_endDate,
        ]);
    }



     /**
     * Function call by ReferenceACurMtnOp.vue with the route : /state/curMtnOp/send/{id}(get)
     * Get the curative maintenance operations of the state whose id is passed in parameter
     * The id parameter corresponds to the id of the state from which we want the curative maintenance operations. 
     * @return \Illuminate\Http\Response 
     */

    public function send_curMtnOp_eq($id) {
        $state = State::findOrFail($id);
        $container=array() ; 
        if (count($state->curative_maintenance_operations)>0){
            $curMtnOps=$state->curative_maintenance_operations ; 
            foreach ($curMtnOps as $curMtnOp) {
                
                $technicalVerifier_firstName=NULL;
                $technicalVerifier_lastName=NULL;
                $qualityVerifier_firstName=NULL;
                $qualityVerifier_lastName=NULL;
                $enteredBy_firstName=NULL;
                $enteredBy_lastName=NULL;
                $realizedBy_firstName=NULL;
                $realizedBy_lastName=NULL ; 

                if ($curMtnOp->technicalVerifier_id!=NULL){
                    $technicalVerifier=User::findOrFail($curMtnOp->technicalVerifier_id) ; 
                    $technicalVerifier_firstName=$technicalVerifier->user_firstName;
                    $technicalVerifier_lastName=$technicalVerifier->user_lastName;
                }
                if ($curMtnOp->qualityVerifier_id!=NULL){
                    $qualityVerifier=User::findOrFail($curMtnOp->qualityVerifier_id) ; 
                    $qualityVerifier_firstName=$qualityVerifier->user_firstName ; 
                    $qualityVerifier_lastName=$qualityVerifier->user_lastName ; 
                }
                if ($curMtnOp->realizedBy_id!=NULL){
                    $realizedBy=User::findOrFail($curMtnOp->realizedBy_id) ; 
                    $realizedBy_firstName=$realizedBy->user_firstName ; 
                    $realizedBy_lastName=$realizedBy->user_lastName ; 
                }
                if ($curMtnOp->enteredBy_id!=NULL){
                    $enteredBy=User::findOrFail($curMtnOp->enteredBy_id) ; 
                    $enteredBy_firstName=$enteredBy->user_firstName ; 
                    $enteredBy_lastName=$enteredBy->user_lastName ; 
                }

                $obj=([
                   "id" => $curMtnOp->id,
                   "curMtnOp_number" => (string)$curMtnOp->curMtnOp_number,
                    "curMtnOp_reportNumber" => $curMtnOp->curMtnOp_reportNumber,
                    "curMtnOp_description" => $curMtnOp->curMtnOp_description,
                    "curMtnOp_startDate" => $curMtnOp->curMtnOp_startDate,
                    "curMtnOp_endDate" => $curMtnOp->curMtnOp_endDate,
                    "curMtnOp_validate" => $curMtnOp->curMtnOp_validate,
                    "qualityVerifier_firstName" => $qualityVerifier_firstName,
                    "qualityVerifier_lastName" => $qualityVerifier_lastName,
                    "realizedBy_firstName" => $realizedBy_firstName,
                    "realizedBy_lastName" => $realizedBy_lastName,
                    "enteredBy_firstName" =>$enteredBy_firstName,
                    "enteredBy_lastName" =>$enteredBy_lastName,
                    "technicalVerifier_firstName" => $technicalVerifier_firstName,
                    "technicalVerifier_lastName" => $technicalVerifier_lastName,
                ]);
                array_push($container,$obj);
           }
       }
        return response()->json($container) ;
    }

    /* Function call by EquipmentCurMtnOpForm.vue when the form is submitted for check data with the route : /curMtnOp/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_curMtnOp(Request $request){
        $state=State::findOrFail($request->state_id) ; 
        if ($request->curMtnOp_validate=='validated'){
            $this->validate(
                $request,
                [
                    'curMtnOp_reportNumber' => 'required|min:3|max:255',
                    'curMtnOp_description' => 'required|min:3',
                ],
                [
                    'curMtnOp_reportNumber.required' => 'You must enter a report number for the curative maintenance operation ',
                    'curMtnOp_reportNumber.min' => 'You must enter at least three characters ',
                    'curMtnOp_reportNumber.max' => 'You must enter a maximum of 255 characters',
                    'curMtnOp_description.required' => 'You must enter a description for the curative maintenance operation ',
                    'curMtnOp_description.min' => 'You must enter at least three characters ',
                ]
            );

    
            if ($request->curMtnOp_startDate=='' || $request->curMtnOp_startDate===NULL){
                return response()->json([
                    'errors' => [
                        'curMtnOp_startDate' => ["You have to entered the startDate of your curative maintenance operation for validate it"]
                    ]
                ], 429);
            }

            if ($request->curMtnOp_endDate=='' || $request->curMtnOp_endDate===NULL){
                return response()->json([
                    'errors' => [
                        'curMtnOp_endDate' => ["You have to entered the endDate of your curative maintenance operation for validate it"]
                    ]
                ], 429);
            }

            if ($request->reason=="update"){
                $curMtnOp=CurativeMaintenanceOperation::findOrFail($request->curMtnOp_id) ;

                if ($curMtnOp->realizedBy_id===NULL){
                    return response()->json([
                        'errors' => [
                            'curMtnOp_validate' => ["You have to entered the realizator of this curative maintenance operation for validate it"]
                        ]
                    ], 429);
                }
    
                if ($curMtnOp->qualityVerifier_id===NULL){
                    return response()->json([
                        'errors' => [
                            'curMtnOp_validate' => ["You have to entered the quality Verifier of this curative maintenance operation for validate it"]
                        ]
                    ], 429);
                }
    
                if ($curMtnOp->technicalVerifier_id===NULL){
                    return response()->json([
                        'errors' => [
                            'curMtnOp_validate' => ["You have to entered the technical Verifier of this curative maintenance operation for validate it"]
                        ]
                    ], 429);
                }
            }else{
                return response()->json([
                    'errors' => [
                        'curMtnOp_validate' => ["You have to entered the realizator of this curative maintenance operation for validate it"]
                    ]
                ], 429);
    
                return response()->json([
                    'errors' => [
                        'curMtnOp_validate' => ["You have to entered the quality Verifier of this curative maintenance operation for validate it"]
                    ]
                ], 429);
    
                return response()->json([
                    'errors' => [
                        'curMtnOp_validate' => ["You have to entered the technical Verifier of this curative maintenance operation for validate it"]
                    ]
                ], 429);
            }

    

        //-----CASE curMtnOp->validate=drafted or curMtnOp->validate=to be validate----//
        //if the user has choosen "drafted" or "to be validated" he have no obligations 
        }else{
            $this->validate(
                $request,
                [
                    'curMtnOp_description' => 'required|min:1|max:50',
                    'curMtnOp_reportNumber' => 'max:255',
                ],
                [
                    'curMtnOp_description.required' => 'You must enter a description for the curative maintenance operation ',
                    'curMtnOp_description.min' => 'You must enter at least three characters ',
                    'curMtnOp_reportNumber.max' => 'You must enter a maximum of 255 characters',
                ]
            );
        }


        if ($request->reason=="update"){
            $curMtnOp=CurativeMaintenanceOperation::FindOrFail($request->curMtnOp_id ) ;
            if ($curMtnOp->curMtnOp_validate=="validated"){
                return response()->json([
                    'errors' => [
                        'curMtnOp_validate' => ["You can't update a curative maintenance operation already validated"]
                    ]
                ], 429);
            }
        }

        $oneMonthAgo=Carbon::now()->subMonth(1) ; 
        if ($request->curMtnOp_startDate!=NULL && $request->curMtnOp_startDate<$oneMonthAgo){
            return response()->json([
                'errors' => [
                    'curMtnOp_startDate' => ["You can't enter a date that is older than one month"]
                ]
            ], 429);
        }

        if ($request->curMtnOp_endDate!=NULL && $request->curMtnOp_endDate<$oneMonthAgo){
            return response()->json([
                'errors' => [
                    'curMtnOp_endDate' => ["You can't enter a date that is older than one month"]
                ]
            ], 429);
        }


        if ($state->state_startDate!=NULL && $request->curMtnOp_startDate!=NULL){
            if ($request->curMtnOp_startDate<$state->state_startDate ){
                return response()->json([
                    'errors' => [
                        'curMtnOp_startDate' => ["You can't entered this startDate because it must be after the startDate of the state"]
                    ]
                ], 429);
            }
        }
        if ($state->state_startDate!=NULL && $request->curMtnOp_endDate!=NULL){
            if ($request->curMtnOp_endDate<$state->state_startDate){
                return response()->json([
                    'errors' => [
                        'curMtnOp_endDate' => ["You can't entered this endDate because it must be after the startDate of the state"]
                    ]
                ], 429);
            }
        }

        if ($request->curMtnOp_startDate!=NULL && $request->curMtnOp_endDate!=NULL){
            if ($request->curMtnOp_endDate < $request->curMtnOp_startDate){
                return response()->json([
                    'errors' => [
                        'curMtnOp_endDate' => ["You must entered a startDate that is before endDate"]
                    ]
                ], 429);

            }
        }
    }

     /*
                FUNCTION FOR CURATIVE MAINTENANCE OPERATION LINKED TO MME
    /**
    
      /**
     * Function call by MmeCurMtnOpForm.vue when the form is submitted for insert with the route :/mme/add/state/curMtnOp (post)
     * Add a new enregistrement of curative maintenance operation in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new curMtnOp
     */
    public function add_curMtnOp_mme(Request $request){
        $state=MmeState::findOrFail($request->state_id) ; 
        $curMtnOpsInMme=CurativeMaintenanceOperation::where('mme_state_id', '=', $request->state_id)->get();
        $max_number=1 ; 
        if (count($curMtnOpsInMme)!=0){
            foreach ($curMtnOpsInMme as $curMtnOpInMme){
                $number=intval($curMtnOpInMme->curMtnOp_number) ; 
                if ($number>$max_number){
                    $max_number=$curMtnOpInMme->curMtnOp_number ; 
                }
            }
            $max_number=$max_number+1 ;
        }
        
        
        //Creation of a new curative maintenance operation
        $curMtnOp=CurativeMaintenanceOperation::create([
            'curMtnOp_reportNumber' => $request->curMtnOp_reportNumber,
            'curMtnOp_validate' => $request->curMtnOp_validate,
            'curMtnOp_description' => $request->curMtnOp_description,
            'curMtnOp_startDate' => $request->curMtnOp_startDate,
            'curMtnOp_endDate' => $request->curMtnOp_endDate,
            'mme_state_id' => $request->mme_state_id,   
            'curMtnOp_number' => $max_number,
            'enteredBy_id' => $request->enteredBy_id,

        ]) ;
        
        $curMtnOp_id=$curMtnOp->id;
        return response()->json($curMtnOp->id) ;
    }

     /**
     * Function call by MmeCurMtnOpForm.vue when the form is submitted for update with the route :/mme/update/state/curMtnOp/{id} (post)
     * Update an enregistrement of curative maintenance operation in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the curative maintenance operation we want to update
     * */
    public function update_curMtnOp_mme(Request $request, $id){
        $curMtnOp=CurativeMaintenanceOperation::FindOrFail($id) ;
        $curMtnOp->update([
            'curMtnOp_reportNumber' => $request->curMtnOp_reportNumber,
            'curMtnOp_validate' => $request->curMtnOp_validate,
            'curMtnOp_description' => $request->curMtnOp_description,
            'curMtnOp_startDate' => $request->curMtnOp_startDate,
            'curMtnOp_endDate' => $request->curMtnOp_endDate,
        ]);
    }



     /**
     * Function call by ReferenceACurMtnOp.vue with the route : /mme_state/curMtnOp/send/{id}(get)
     * Get the curative maintenance operations of the state whose id is passed in parameter
     * The id parameter corresponds to the id of the mme_state from which we want the curative maintenance operations. 
     * @return \Illuminate\Http\Response 
     */

    public function send_curMtnOp_mme($id) {
        $state = Mmme_State::findOrFail($id);
        $container=array() ; 
        if (count($state->curative_maintenance_operations)>0){
            $curMtnOps=$state->curative_maintenance_operations ; 
            foreach ($curMtnOps as $curMtnOp) {
                
                $technicalVerifier_firstName=NULL;
                $technicalVerifier_lastName=NULL;
                $qualityVerifier_firstName=NULL;
                $qualityVerifier_lastName=NULL;
                $enteredBy_firstName=NULL;
                $enteredBy_lastName=NULL;
                $realizedBy_firstName=NULL;
                $realizedBy_lastName=NULL ; 

                if ($curMtnOp->technicalVerifier_id!=NULL){
                    $technicalVerifier=User::findOrFail($curMtnOp->technicalVerifier_id) ; 
                    $technicalVerifier_firstName=$technicalVerifier->user_firstName;
                    $technicalVerifier_lastName=$technicalVerifier->user_lastName;
                }
                if ($curMtnOp->qualityVerifier_id!=NULL){
                    $qualityVerifier=User::findOrFail($curMtnOp->qualityVerifier_id) ; 
                    $qualityVerifier_firstName=$qualityVerifier->user_firstName ; 
                    $qualityVerifier_lastName=$qualityVerifier->user_lastName ; 
                }
                if ($curMtnOp->realizedBy_id!=NULL){
                    $realizedBy=User::findOrFail($curMtnOp->realizedBy_id) ; 
                    $realizedBy_firstName=$realizedBy->user_firstName ; 
                    $realizedBy_lastName=$realizedBy->user_lastName ; 
                }
                if ($curMtnOp->enteredBy_id!=NULL){
                    $enteredBy=User::findOrFail($curMtnOp->enteredBy_id) ; 
                    $enteredBy_firstName=$enteredBy->user_firstName ; 
                    $enteredBy_lastName=$enteredBy->user_lastName ; 
                }

                $obj=([
                   "id" => $curMtnOp->id,
                   "curMtnOp_number" => (string)$curMtnOp->curMtnOp_number,
                    "curMtnOp_reportNumber" => $curMtnOp->curMtnOp_reportNumber,
                    "curMtnOp_description" => $curMtnOp->curMtnOp_description,
                    "curMtnOp_startDate" => $curMtnOp->curMtnOp_startDate,
                    "curMtnOp_endDate" => $curMtnOp->curMtnOp_endDate,
                    "curMtnOp_validate" => $curMtnOp->curMtnOp_validate,
                    "qualityVerifier_firstName" => $qualityVerifier_firstName,
                    "qualityVerifier_lastName" => $qualityVerifier_lastName,
                    "realizedBy_firstName" => $realizedBy_firstName,
                    "realizedBy_lastName" => $realizedBy_lastName,
                    "enteredBy_firstName" =>$enteredBy_firstName,
                    "enteredBy_lastName" =>$enteredBy_lastName,
                    "technicalVerifier_firstName" => $technicalVerifier_firstName,
                    "technicalVerifier_lastName" => $technicalVerifier_lastName,
                ]);
                array_push($container,$obj);
           }
       }
        return response()->json($container) ;
    }

    /* Function call by EquipmentCurMtnOpForm.vue when the form is submitted for check data with the route : /curMtnOp/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_curMtnOp_mme(Request $request){
        $state=MmeState::findOrFail($request->state_id) ; 
        if ($request->curMtnOp_validate=='validated'){
            $this->validate(
                $request,
                [
                    'curMtnOp_reportNumber' => 'required|min:3|max:255',
                    'curMtnOp_description' => 'required|min:3',
                ],
                [
                    'curMtnOp_reportNumber.required' => 'You must enter a report number for the curative maintenance operation ',
                    'curMtnOp_reportNumber.min' => 'You must enter at least three characters ',
                    'curMtnOp_reportNumber.max' => 'You must enter a maximum of 255 characters',
                    'curMtnOp_description.required' => 'You must enter a description for the curative maintenance operation ',
                    'curMtnOp_description.min' => 'You must enter at least three characters ',
                ]
            );

        
            if ($request->curMtnOp_startDate=='' || $request->curMtnOp_startDate===NULL){
                return response()->json([
                    'errors' => [
                        'curMtnOp_startDate' => ["You have to entered the startDate of your curative maintenance operation for validate it"]
                    ]
                ], 429);
            }

            if ($request->curMtnOp_endDate=='' || $request->curMtnOp_endDate===NULL){
                return response()->json([
                    'errors' => [
                        'curMtnOp_endDate' => ["You have to entered the endDate of your curative maintenance operation for validate it"]
                    ]
                ], 429);
            }
        
            if ($request->reason=="update"){
                $curMtnOp=CurativeMaintenanceOperation::findOrFail($request->curMtnOp_id) ;

                if ($curMtnOp->realizedBy_id===NULL){
                    return response()->json([
                        'errors' => [
                            'curMtnOp_validate' => ["You have to entered the realizator of this curative maintenance operation for validate it"]
                        ]
                    ], 429);
                }
    
                if ($curMtnOp->qualityVerifier_id===NULL){
                    return response()->json([
                        'errors' => [
                            'curMtnOp_validate' => ["You have to entered the quality Verifier of this curative maintenance operation for validate it"]
                        ]
                    ], 429);
                }
    
                if ($curMtnOp->technicalVerifier_id===NULL){
                    return response()->json([
                        'errors' => [
                            'curMtnOp_validate' => ["You have to entered the technical Verifier of this curative maintenance operation for validate it"]
                        ]
                    ], 429);
                }
            }else{
                return response()->json([
                    'errors' => [
                        'curMtnOp_validate' => ["You have to entered the realizator of this curative maintenance operation for validate it"]
                    ]
                ], 429);
    
                return response()->json([
                    'errors' => [
                        'curMtnOp_validate' => ["You have to entered the quality Verifier of this curative maintenance operation for validate it"]
                    ]
                ], 429);
    
                return response()->json([
                    'errors' => [
                        'curMtnOp_validate' => ["You have to entered the technical Verifier of this curative maintenance operation for validate it"]
                    ]
                ], 429);
            }

    

        //-----CASE curMtnOp->validate=drafted or curMtnOp->validate=to be validate----//
        //if the user has choosen "drafted" or "to be validated" he have no obligations 
        }else{
            $this->validate(
                $request,
                [
                    'curMtnOp_description' => 'required|min:1|max:50',
                    'curMtnOp_reportNumber' => 'max:255',
                ],
                [
                    'curMtnOp_description.required' => 'You must enter a description for the curative maintenance operation ',
                    'curMtnOp_description.min' => 'You must enter at least three characters ',
                    'curMtnOp_reportNumber.max' => 'You must enter a maximum of 255 characters',
                ]
            );
        }

        if ($request->reason=="update"){
            $curMtnOp=CurativeMaintenanceOperation::FindOrFail($request->curMtnOp_id ) ;
            if ($curMtnOp->curMtnOp_validate=="validated"){
                return response()->json([
                    'errors' => [
                        'curMtnOp_validate' => ["You can't update a curative maintenance operation already validated"]
                    ]
                ], 429);
            }
        }

        
        $oneMonthAgo=Carbon::now()->subMonth(1) ; 
        if ($request->curMtnOp_startDate!=NULL && $request->curMtnOp_startDate<$oneMonthAgo){
            return response()->json([
                'errors' => [
                    'curMtnOp_startDate' => ["You can't enter a date that is older than one month"]
                ]
            ], 429);
        }

        if ($request->curMtnOp_endDate!=NULL && $request->curMtnOp_endDate<$oneMonthAgo){
            return response()->json([
                'errors' => [
                    'curMtnOp_endDate' => ["You can't enter a date that is older than one month"]
                ]
            ], 429);
        }

        
        if ($state->state_startDate!=NULL && $request->curMtnOp_startDate!=NULL){
            if ($request->curMtnOp_startDate<$state->state_startDate ){
                return response()->json([
                    'errors' => [
                        'curMtnOp_startDate' => ["You can't entered this startDate because it must be after the startDate of the state"]
                    ]
                ], 429);
            }
        }
        
        if ($state->state_startDate!=NULL && $request->curMtnOp_endDate!=NULL){
            if ($request->curMtnOp_endDate<$state->state_startDate){
                return response()->json([
                    'errors' => [
                        'curMtnOp_endDate' => ["You can't entered this endDate because it must be after the startDate of the state"]
                    ]
                ], 429);
            }
        }
        
        if ($request->curMtnOp_startDate!=NULL && $request->curMtnOp_endDate!=NULL){
            if ($request->curMtnOp_endDate < $request->curMtnOp_startDate){
                return response()->json([
                    'errors' => [
                        'curMtnOp_endDate' => ["You must entered a startDate that is before endDate"]
                    ]
                ], 429);

            }
        }
    }
    

    
}



