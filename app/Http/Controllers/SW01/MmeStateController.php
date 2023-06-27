<?php

/*
* Filename : MmeStateController.php
* Creation date : 16 Jun 2022
* Update date : 27 Jun 2023
* This file is used to link the view files and the database that concern the mme state table.
* For example : add a state for an mme in the data base, update a file, delete it...
*/

namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB ;
use App\Models\SW01\MmeTemp ;
use App\Models\SW01\MmeState ;
use App\Models\User ;
use App\Models\SW01\Mme ;
use App\Models\SW01\VerificationRealized ;
use App\Models\SW01\CurativeMaintenanceOperation ;
use App\Models\SW01\Verification ;
use Carbon\Carbon;
use App\Http\Controllers\SW01\MmeController ;
use App\Http\Controllers\Controller;

class MmeStateController extends Controller
{

    /**
     * Function call by MmeStateForm.vue when the form is submitted for check data with the route : /mme_state/verif''(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_state(Request $request){

        //-----CASE state->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his state, so he must enter all the attributes

        $this->validate(
            $request,
            [
                'state_remarks' => 'required|min:3|max:255',

            ],
            [
                'state_remarks.required' => 'You must enter a remark about the state ',
                'state_remarks.min' => 'You must enter at least three characters ',
                'state_remarks.max' => 'You must enter a maximum of 255 characters',

            ]
        );

        //verification about state_name, if no one value is selected we need to alert the user
        if ($request->state_name=='' || $request->state_name==NULL ){
            return response()->json([
                'errors' => [
                    'state_name' => ["You must choose a name for your state"]
                ]
            ], 429);
        }

        if ($request->state_startDate==NULL ){
            return response()->json([
                'errors' => [
                    'state_startDate' => ["You must entered a start date for your state"]
                ]
            ], 429);
        }

        $oneMonthAgo=Carbon::now()->subMonth(1) ;
        if ($request->state_startDate!=NULL && $request->state_startDate<$oneMonthAgo){
            return response()->json([
                'errors' => [
                    'state_startDate' => ["You can't enter a date that is older than one month"]
                ]
            ], 429);
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->orderBy('created_at', 'desc')->first();
        $states=$mostRecentlyMmeTmp->states;
        if ($states!==NULL){
            if ($request->reason=='update'){
                $mostRecentlyState=NULL ;
                $first=true ;
                foreach($states as $state){
                    if ($state->id !=$request->state_id){
                        if ($first){
                            $mostRecentlyState=$state ;
                            $first=false;
                        }else{
                            $date=$state->created_at ;
                            $date2=$mostRecentlyState->created_at;
                            if ($date>=$date2){
                                $mostRecentlyState=$state ;
                            }
                        }
                    }
                }
                if ($mostRecentlyState!=NULL ){
                    if ($request->state_startDate!=NULL && $request->state_startDate<$mostRecentlyState->state_endDate){
                        return response()->json([
                            'errors' => [
                                'state_startDate' => ["You must entered a startDate that is after the previous state"]
                            ]
                        ], 429);
                    }
                }
            }else{
                $mostRecentlyState=NULL ;
                $first=true ;
                foreach($states as $state){
                    if ($first){
                        $mostRecentlyState=$state ;
                        $first=false;
                    }else{
                        $date=$state->created_at ;
                        $date2=$mostRecentlyState->created_at;
                        if ($date>=$date2){
                            $mostRecentlyState=$state ;
                        }
                    }
                }
                if ($mostRecentlyState!=NULL ){
                    if ($request->state_startDate!=NULL && $request->state_startDate<$mostRecentlyState->state_startDate){
                        return response()->json([
                            'errors' => [
                                'state_startDate' => ["You must entered a startDate that is after the previous state"]
                            ]
                        ], 429);
                    }
                }
                switch($mostRecentlyState->state_name){
                    case "Waiting_for_referencing" :
                        if ($request->state_name!="Waiting_to_be_in_use"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in waiting to be in use state from this one"]
                                ]
                            ], 429);
                        }
                        break;
                    case "Waiting_to_be_in_use" :
                        if ($request->state_name!="Waiting_for_referencing" && $request->state_name!="In_use" && $request->state_name!="In_quarantine" && $request->state_name!="Reformed" && $request->state_name!="Lost"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in waiting for referencing, in use, in quarantine, reformed and lost states from this one"]
                                ]
                            ], 429);
                        }
                        break;
                    case "In_use" :
                        if ($request->state_name!="Waiting_for_referencing" && $request->state_name!="Under_verification" && $request->state_name!="In_quarantine" && $request->state_name!="Reformed" && $request->state_name!="Lost"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in waiting for referencing, Under_verification, In_quarantine, reformed and lost states from this one"]
                                ]
                            ], 429);
                        }
                        break;
                    case "Under_verification" :
                        if ($request->state_name!="In_use" && $request->state_name!="In_quarantine" && $request->state_name!="Lost"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in In_use, In_quarantine and lost states from this one"]
                                ]
                            ], 429);
                        }
                        break;
                    case "In_quarantine":
                        if ($request->state_name!="In_use" && $request->state_name!="Under_verification" && $request->state_name!="Under_repair" && $request->state_name!="Broken" && $request->state_name!="Downgraded" && $request->state_name!="Reformed" && $request->state_name!="Lost"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in In_use, Under_verification, Under_repair, Broken, Downgraded, Reformed and lost states from this one"]
                                ]
                            ], 429);
                        }
                        break;
                    case "Under_repair":
                        if ($request->state_name!="In_use" && $request->state_name!="Under_verification" && $request->state_name!="Broken" && $request->state_name!="Downgraded" && $request->state_name!="Lost"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in In_use, Under_verification, Broken, Downgraded and lost states from this one"]
                                ]
                            ], 429);
                        }
                        break;
                    case "Broken" :
                        return response()->json([
                            'errors' => [
                                'state_name' => ["You can't go in another state"]
                            ]
                        ], 429);
                        break;
                    case "Downgraded":
                        return response()->json([
                            'errors' => [
                                'state_name' => ["You can't go in another state"]
                            ]
                        ], 429);
                        break;
                    case "Reformed" :
                        return response()->json([
                            'errors' => [
                                'state_name' => ["You can't go in another state"]
                            ]
                        ], 429);
                        break;
                    case "Lost":
                        if ($request->state_name!="Waiting_to_be_in_use" && $request->state_name!="Under_verification" && $request->state_name!="In_use" && $request->state_name!="In_quarantine" && $request->state_name!="Reformed"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in Waiting_to_be_in_use, Under_verification, In_use, In_quarantine and Reformed states from this one"]
                                ]
                            ], 429);
                        }
                        break;
                }

                $mme=Mme::findOrFail($request->mme_id);
                if ($mme->equipmentTemp_id!=NULL){
                    if ($request->state_name=="Broken"){
                        return response()->json([
                            'errors' => [
                                'state_name' => ["You can't put an mme already linked to an equipment to the broken state. Please delete the mme from the equipment before."]
                            ]
                        ], 429);
                    }

                    if ($request->state_name=="Downgraded"){
                        return response()->json([
                            'errors' => [
                                'state_name' => ["You can't put an mme already linked to an equipment to the downgraded state. Please delete the mme from the equipment before."]
                            ]
                        ], 429);
                    }

                    if ($request->state_name=="Reformed"){
                        return response()->json([
                            'errors' => [
                                'state_name' => ["You can't put an mme already linked to an equipment to the reformed state. Please delete the mme from the equipment before."]
                            ]
                        ], 429);
                    }
                }
            }
        }
    }

    /**
     * Function call by MmeStateForm.vue when the form is submitted for insert with the route :/mme/add/state (post)
     * Add a new enregistrement of state in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new state
     */
    public function add_state(Request $request){

        //If the user has not entered a date we take the date of the current day
        $date=Carbon::now('Europe/Paris');
        if ($request->state_startDate!='' && $request->state_startDate!=NULL){
            $date=$request->state_startDate ;
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->orderBy('created_at', 'desc')->first();
        $states=$mostRecentlyMmeTmp->states;
        if ($states!==NULL){
            $mostRecentlyState=NULL ;
            $first=true ;
            foreach($states as $state){
                if ($first){
                    $mostRecentlyState=$state ;
                    $first=false;
                }else{
                    $date=$state->created_at ;
                    $date2=$mostRecentlyState->created_at;
                    if ($date>=$date2){
                        $mostRecentlyState=$state ;
                    }
                }
            }
            if ($mostRecentlyState!=NULL){
                $mostRecentlyState->update([
                    'state_endDate' => $date,
                ]);
            }
        }

        //Creation of a new state
        $state=MmeState::create([
            'state_remarks' => $request->state_remarks,
            'state_validate' => $request->state_validate,
            'state_startDate' => $date,
            'state_name' => $request->state_name,
        ]) ;

        if ($request->state_name=="Reformed"){
            $state->update([
                'reformedBy_id' => $request->enteredBy_id,
            ]);
        }

        $state_id=$state->id;
        $id_mme=intval($request->mme_id) ;
        $mme=Mme::findOrfail($request->mme_id) ;
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->orderBy('created_at', 'desc')->first();
        $state->mme_temps()->attach($mostRecentlyMmeTmp) ;

        return response()->json($state_id) ;
    }


    /**
     * Function call by MmeStateForm.vue when the form is submitted for update with the route :/mme/update/state/{id} (post)
     * Update an enregistrement of state in the data base with the informations entered in the form
     * The id parameter correspond to the id of the state we want to update
     * */
    public function update_state(Request $request, $id){

        //If the user has not entered a date we take the date of the current day
        $date=Carbon::now('Europe/Paris');
        if ($request->state_startDate!='' && $request->state_startDate!=NULL){
            $date=$request->state_startDate ;
        }

        $state=MmeState::findOrFail($id) ;
        $state->update([
            'state_remarks' => $request->state_remarks,
            'state_validate' => $request->state_validate,
            'state_startDate' => $date,
            'state_name' => $request->state_name,
        ]) ;
    }

    /**
     * Function call by ReferenceAState.vue with the route : /mme_states/send/{$id} (get)
     * Get the states of the mme whose id is passed in parameter
     * The id parameter corresponds to the id of the mme from which we want the states
     * @return \Illuminate\Http\Response
     */

    public function send_states($id) {
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $container=array() ;
        if (count($mostRecentlyMmeTmp->states)>0){
            $states=$mostRecentlyMmeTmp->states ;
            foreach ($states as $state) {

                $verifRlzs=VerificationRealized::where('state_id', '=', $state->id)->get() ;
                $container_verifRlz=array() ;
                foreach($verifRlzs as $verifRlz){
                    $verif=Verification::findOrFail($verifRlz->verif_id) ;
                    $enteredBy_firstName=NULL;
                    $enteredBy_lastName=NULL;
                    $realizedBy_firstName=NULL;
                    $realizedBy_lastName=NULL ;
                    $approvedBy_firstName=NULL;
                    $approvedBy_lastName=NULL ;

                    if ($verifRlz->realizedBy_id!=NULL){
                        $realizedBy=User::findOrFail($verifRlz->realizedBy_id) ;
                        $realizedBy_firstName=$realizedBy->user_firstName ;
                        $realizedBy_lastName=$realizedBy->user_lastName ;
                    }
                    if ($verifRlz->enteredBy_id!=NULL){
                        $enteredBy=User::findOrFail($verifRlz->enteredBy_id) ;
                        $enteredBy_firstName=$enteredBy->user_firstName ;
                        $enteredBy_lastName=$enteredBy->user_lastName ;
                    }
                    if ($verifRlz->approvedBy_id!=NULL){
                        $approvedBy=User::findOrFail($verifRlz->approvedBy_id) ;
                        $approvedBy_firstName=$approvedBy->user_firstName ;
                        $approvedBy_lastName=$approvedBy->user_lastName ;
                    }

                    $obj=([
                        "id" => $verifRlz->id,
                        "verifRlz_reportNumber" => $verifRlz->verifRlz_reportNumber,
                        "verifRlz_startDate" => $verifRlz->verifRlz_startDate,
                        "verifRlz_endDate" => $verifRlz->verifRlz_endDate,
                        "verifRlz_entryDate" => $verifRlz->verifRlz_entryDate,
                        "verifRlz_validate" => $verifRlz->verifRlz_validate,
                        "veriRlz_comment" => $verifRlz->verifRlz_comment,
                        "verif_id" => $verifRlz->verif_id,
                        "verif_number" => (string)$verif->verif_number,
                        "verif_description" => $verif->verif_description,
                        "verif_protocol" => $verif->verif_protocol,
                        "verif_expectedResult" => $verif->verif_expectedResult,
                        'verif_nonComplianceLimit' => $verif->verif_nonComplianceLimit,
                        "realizedBy_firstName" => $realizedBy_firstName,
                        "realizedBy_lastName" => $realizedBy_lastName,
                        "enteredBy_firstName" => $enteredBy_firstName,
                        "enteredBy_lastName" => $enteredBy_lastName,
                        "approvedBy_firstName" => $approvedBy_firstName,
                        "approvedBy_lastName" => $approvedBy_lastName,
                    ]);
                    array_push($container_verifRlz, $obj);
                }

                $curMtnOps=CurativeMaintenanceOperation::where('mme_state_id', '=', $state->id)->get() ;
                $container_curMtnOp=array() ;
                foreach($curMtnOps as $curMtnOp){
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
                    array_push($container_curMtnOp, $obj);
                }

                $reformedBy_id=NULL ;
                if ($state->reformedBy_id!=NULL){
                    $reformedBy_id=$state->reformedBy_id;
                }
                $obj=([
                    "id" => $state->id,
                    'state_remarks' => $state->state_remarks,
                    'state_validate' => $state->state_validate,
                    'state_startDate' => $state->state_startDate,
                    'state_endDate' => $state->state_endDate,
                    'state_name' => $state->state_name,
                    'reformedBy_id' =>$reformedBy_id,
                    'verifRlz' => $container_verifRlz,
                    'curMtnOp' => $container_curMtnOp,
                ]);
                array_push($container,$obj);
            }
        }
        return response()->json($container) ;
    }

    /**
     * Function call by ReferenceAState.vue with the route : /mme_state/send/{$id} (get)
     * Get the states of the mme whose id is passed in parameter
     * The id parameter corresponds to the id of the state from which we want the informations
     * @return \Illuminate\Http\Response
     */

    public function send_state($id) {
        $state=MmeState::findOrFail($id) ;
        $container=array() ;

        $obj=([
            "id" => $state->id,
            'state_remarks' => $state->state_remarks,
            'state_validate' => $state->state_validate,
            'state_startDate' => $state->state_startDate,
            'state_endDate' => $state->state_endDate,
            'state_name' => $state->state_name,
        ]);
        array_push($container,$obj);
        return response()->json($container) ;
    }


    /**
     * Function call by ListOfMmeLifeEvent with the route : /mme_state/verif/beforeReferenceVerif/{id} (post)
     * Check if we can create a new state (the previous state is validated, it has a endDate...)
     * The id parameter is the id of the actual state
     * @return \Illuminate\Http\Response
     */
    public function verif_before_reference_verif(Request $request, $id){
        $state=MmeState::findOrFail($id) ;
        if ($state->state_name!="In_use" && $state->state_name!="Under_verification" && $state->state_name!="In_quarantine"){
            return response()->json([
                'errors' => [
                    'verif_reference' => ["You can only reference a verification during an in use, an under verification or an in quarantine state"]
                ]
            ], 429);
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyMmeTmp->mmeTemp_validate!="validated"){
            return response()->json([
                'errors' => [
                    'verif_reference' => ["You can't add a maintenance operation while you have'nt finished to complete the Id card of the mme"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by ListOfMMELifeEvent with the route : /mme_state/verif/beforeReferenceCurOp/{id} (post)
     * Check if we can create a new state (the previous state is validated, it has a endDate...)
     * The id parameter is the id of the actual state
     * @return \Illuminate\Http\Response
     */
    public function verif_before_reference_cur_op(Request $request, $id){
        $state=MmeState::findOrFail($id) ;
        if ($state->state_name!="In_quarantine" && $state->state_name!="Under_repair"){
            return response()->json([
                'errors' => [
                    'verif_reference' => ["You can only reference a curative maintenance operation during an in quarantine or an under repair state"]
                ]
            ], 429);
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyMmeTmp->mmeTemp_validate!="validated"){
            return response()->json([
                'errors' => [
                    'verif_reference' => ["You can't add a curative maintenance operation while you have'nt finished to complete the Id card of the mme"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by ListOfMmeLifeEvent with the route : /state/verif/beforeChangingState/{id} (post)
     * Check if we can create a new state (the previous state is validated, it has a endDate...)
     * The id parameter is the id of the actual state
     * @return \Illuminate\Http\Response
     */
    public function verif_before_changing_state($id){
        $state=MmeState::findOrFail($id) ;
        $verifRlzs=VerificationRealized::where('state_id', '=', $id)->get() ;
        foreach ($verifRlzs as $verifRlz){
            if ($verifRlz->verifRlz_validate!="validated"){
                return response()->json([
                    'errors' => [
                        'state_verif' => ["You must validate all your verification realized before add a new state"]
                    ]
                ], 429);

            }
        }

        if ($state->state_validate!="validated"){
            return response()->json([
                'errors' => [
                    'state_verif' => ["You must validate your state before add a new state"]
                ]
            ], 429);
        }


    }

    /**
     * Function call by MmeStateForm.vue when we want to delete a state with the route : /mme/delete/state{id}(post)
     * Delete a state thanks to the id given in parameter
     * The id parameter correspond to the id of the state we want to delete
     * */
    public function delete_state($id){
        $state=MmeState::findOrFail($id);
        $state->delete() ;
    }
}

