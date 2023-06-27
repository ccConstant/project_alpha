<?php

/*
* Filename : StateController.php
* Creation date : 17 May 2022
* Update date : 27 Jun 2023
* This file is used to link the view files and the database that concern the state table.
* For example : add a state for an equipment in the data base, update a file, delete it...
*/

namespace App\Http\Controllers\SW01;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ;
use App\Models\SW01\EquipmentTemp ;
use App\Models\SW01\State ;
use App\Models\User ;
use App\Models\SW01\Mme ;
use App\Models\SW01\Equipment ;
use App\Models\SW01\PreventiveMaintenanceOperationRealized;
use App\Models\SW01\PreventiveMaintenanceOperation;
use App\Models\SW01\CurativeMaintenanceOperation;
use App\Http\Controllers\SW01\EquipmentController ;
use App\Http\Controllers\Controller;


use Carbon\Carbon;


class StateController extends Controller
{


    /**
     * Function call by EquipmentStateForm.vue when the form is submitted for check data with the route : /state/verif''(post)
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

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        $states=$mostRecentlyEqTmp->states;
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
                        if ($request->state_name!="Waiting_for_installation"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in waiting for installation state from this one"]
                                ]
                            ], 429);
                        }
                        break;
                    case "Waiting_for_installation" :
                        if ($request->state_name!="Waiting_for_referencing" && $request->state_name!="In_use" && $request->state_name!="On_hold" && $request->state_name!="Reformed" && $request->state_name!="Lost"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in waiting for referencing, in use, on hold, reformed and lost states from this one"]
                                ]
                            ], 429);
                        }
                        break;
                    case "In_use" :
                        if ($request->state_name!="Waiting_for_referencing" && $request->state_name!="Under_maintenance" && $request->state_name!="On_hold" && $request->state_name!="Reformed" && $request->state_name!="Lost"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in waiting for referencing, Under_maintenance, on hold, reformed and lost states from this one"]
                                ]
                            ], 429);
                        }
                        break;
                    case "Under_maintenance" :
                        if ($request->state_name!="In_use" && $request->state_name!="On_hold" && $request->state_name!="Lost"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in In_use, On_hold and lost states from this one"]
                                ]
                            ], 429);
                        }
                        break;
                    case "On_hold":
                        if ($request->state_name!="In_use" && $request->state_name!="Under_maintenance" && $request->state_name!="Under_repair" && $request->state_name!="Broken" && $request->state_name!="Downgraded" && $request->state_name!="Reformed" && $request->state_name!="Lost"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in In_use, Under_maintenance, Under_repair, Broken, Downgraded, Reformed and lost states from this one"]
                                ]
                            ], 429);
                        }
                        break;
                    case "Under_repair":
                        if ($request->state_name!="In_use" && $request->state_name!="Under_maintenance" && $request->state_name!="Broken" && $request->state_name!="Downgraded" && $request->state_name!="Lost"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in In_use, Under_maintenance, Broken, Downgraded and lost states from this one"]
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
                        if ($request->state_name!="Waiting_for_installation" && $request->state_name!="Under_maintenance" && $request->state_name!="In_use" && $request->state_name!="On_hold" && $request->state_name!="Reformed"){
                            return response()->json([
                                'errors' => [
                                    'state_name' => ["You can't only go in Waiting_for_installation, Under_maintenance, In_use, On_hold and Reformed states from this one"]
                                ]
                            ], 429);
                        }
                        break;

                }
            }
        }
    }

    /**
     * Function call by EquipmentStateForm.vue when the form is submitted for insert with the route :/equipment/add/state (post)
     * Add a new enregistrement of state in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new state
     */
    public function add_state(Request $request){

        //If the user has not entered a date we take the date of the current day
        $date=Carbon::now('Europe/Paris');
        if ($request->state_startDate!='' && $request->state_startDate!=NULL){
            $date=$request->state_startDate ;
        }

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        $states=$mostRecentlyEqTmp->states;
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
        $state=State::create([
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

        if ($request->state_name=="Downgraded" || $request->state_name=="Broken" || $request->state_name=="Reformed" ){
            $mmes=Mme::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ;
            foreach($mmes as $mme){
                $mme->update([
                    'equipmentTemp_id' => NULL,
                ]);
            }
        }

        $state_id=$state->id;
        $id_eq=intval($request->eq_id) ;
        $equipment=Equipment::findOrfail($request->eq_id) ;
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        $state->equipment_temps()->attach($mostRecentlyEqTmp) ;

        return response()->json($state_id) ;
    }


    /**
     * Function call by EquipmentStateForm.vue when the form is submitted for update with the route :/equipment/update/state/{id} (post)
     * Update an enregistrement of state in the data base with the informations entered in the form
     * The id parameter correspond to the id of the state we want to update
     * */
    public function update_state(Request $request, $id){

        //If the user has not entered a date we take the date of the current day
        $date=Carbon::now('Europe/Paris');
        if ($request->state_startDate!='' && $request->state_startDate!=NULL){
            $date=$request->state_startDate ;
        }

        $state=State::findOrFail($id) ;
        $state->update([
            'state_remarks' => $request->state_remarks,
            'state_validate' => $request->state_validate,
            'state_startDate' => $date,
            'state_name' => $request->state_name,
        ]) ;
    }

    /**
     * Function call by ReferenceAState.vue with the route : /states/send/{$id} (get)
     * Get the states of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the states
     * @return \Illuminate\Http\Response
     */

    public function send_states($id) {
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $container=array() ;
        if (count($mostRecentlyEqTmp->states)>0){
            $states=$mostRecentlyEqTmp->states ;
            foreach ($states as $state) {

                $prvMtnOpRlzs=PreventiveMaintenanceOperationRealized::where('state_id', '=', $state->id)->get() ;
                $container_prvMtnOpRlz=array() ;
                foreach($prvMtnOpRlzs as $prvMtnOpRlz){
                    $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($prvMtnOpRlz->prvMtnOp_id) ;
                    $enteredBy_firstName=NULL;
                    $enteredBy_lastName=NULL;
                    $realizedBy_firstName=NULL;
                    $realizedBy_lastName=NULL ;
                    $approvedBy_firstName=NULL;
                    $approvedBy_lastName=NULL ;

                    if ($prvMtnOpRlz->realizedBy_id!=NULL){
                        $realizedBy=User::findOrFail($prvMtnOpRlz->realizedBy_id) ;
                        $realizedBy_firstName=$realizedBy->user_firstName ;
                        $realizedBy_lastName=$realizedBy->user_lastName ;
                    }
                    if ($prvMtnOpRlz->enteredBy_id!=NULL){
                        $enteredBy=User::findOrFail($prvMtnOpRlz->enteredBy_id) ;
                        $enteredBy_firstName=$enteredBy->user_firstName ;
                        $enteredBy_lastName=$enteredBy->user_lastName ;
                    }
                    if ($prvMtnOpRlz->approvedBy_id!=NULL){
                        $approvedBy=User::findOrFail($prvMtnOpRlz->approvedBy_id) ;
                        $approvedBy_firstName=$approvedBy->user_firstName ;
                        $approvedBy_lastName=$approvedBy->user_lastName ;
                    }

                    $obj=([
                        "id" => $prvMtnOpRlz->id,
                        "prvMtnOpRlz_reportNumber" => $prvMtnOpRlz->prvMtnOpRlz_reportNumber,
                        "prvMtnOpRlz_startDate" => $prvMtnOpRlz->prvMtnOpRlz_startDate,
                        "prvMtnOpRlz_endDate" => $prvMtnOpRlz->prvMtnOpRlz_endDate,
                        "prvMtnOpRlz_entryDate" => $prvMtnOpRlz->prvMtnOpRlz_entryDate,
                        "prvMtnOpRlz_validate" => $prvMtnOpRlz->prvMtnOpRlz_validate,
                        "prvMtnOpRlz_comment" => $prvMtnOpRlz->prvMtnOpRlz_comment,
                        "prvMtnOp_id" => $prvMtnOpRlz->prvMtnOp_id,
                        "prvMtnOp_number" => (string)$prvMtnOp->prvMtnOp_number,
                        "prvMtnOp_description" => $prvMtnOp->prvMtnOp_description,
                        "prvMtnOp_protocol" => $prvMtnOp->prvMtnOp_protocol,
                        "realizedBy_firstName" => $realizedBy_firstName,
                        "realizedBy_lastName" => $realizedBy_lastName,
                        "enteredBy_firstName" => $enteredBy_firstName,
                        "enteredBy_lastName" => $enteredBy_lastName,
                        "approvedBy_firstName" => $approvedBy_firstName,
                        "approvedBy_lastName" => $approvedBy_lastName,
                    ]);
                    array_push($container_prvMtnOpRlz, $obj);
                }

                $curMtnOps=CurativeMaintenanceOperation::where('state_id', '=', $state->id)->get() ;
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
                    'prvMtnOpRlz' => $container_prvMtnOpRlz,
                    'curMtnOp' => $container_curMtnOp,
                ]);
                array_push($container,$obj);
            }
        }
        return response()->json($container) ;
    }

    /**
     * Function call by ReferenceAState.vue with the route : /state/send/{$id} (get)
     * Get the states of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the state from which we want the informations
     * @return \Illuminate\Http\Response
     */

    public function send_state($id) {
        $state=State::findOrFail($id) ;
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
     * Function call by ListOfEquipmentLifeEvent with the route : /state/verif/beforeReferenceCurOp/{id} (post)
     * Check if we can create a new state (the previous state is validated, it has a endDate...)
     * The id parameter is the id of the actual state
     * @return \Illuminate\Http\Response
     */
    public function verif_before_reference_cur_op(Request $request, $id){
        $state=State::findOrFail($id) ;
        if ($state->state_name!="On_hold" && $state->state_name!="Under_repair"){
            return response()->json([
                'errors' => [
                    'verif_reference' => ["You can only reference a curative maintenance operation during an on hold or an under repair state"]
                ]
            ], 429);
        }

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyEqTmp->eqTemp_validate!="validated"){
            return response()->json([
                'errors' => [
                    'verif_reference' => ["You can't add a curative maintenance operation while you have'nt finished to complete the Id card of the equipment"]
                ]
            ], 429);
        }
    }

     /**
     * Function call by EventDetailsModal with the route : /state/verif/beforeReferencePrvOp/{id} (post)
     * Check if we can create a new state (the previous state is validated, it has a endDate...)
     * The id parameter is the id of the actual state
     * @return \Illuminate\Http\Response
     */
    public function verif_before_reference_prv_op(Request $request, $id){
        $state=State::findOrFail($id) ;
        if ($state->state_name!="In_use" && $state->state_name!="Under_maintenance" && $state->state_name!="On_hold"){
            return response()->json([
                'errors' => [
                    'verif_reference' => ["You can only reference a preventive maintenance operation during an in use, an on hold or an under maintenance state"]
                ]
            ], 429);
        }

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyEqTmp->eqTemp_validate!="validated"){
            return response()->json([
                'errors' => [
                    'verif_reference' => ["You can't add a preventive maintenance operation while you have'nt finished to complete the Id card of the equipment"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by ListOfEquipmentLifeEvent with the route : /state/verif/beforeChangingState/{id} (post)
     * Check if we can create a new state (the previous state is validated, it has a endDate...)
     * The id parameter is the id of the actual state
     * @return \Illuminate\Http\Response
     */
    public function verif_before_changing_state($id){
        $state=State::findOrFail($id) ;
        $prvMtnOpRlzs=PreventiveMaintenanceOperationRealized::where('state_id', '=', $id)->get() ;
        foreach ($prvMtnOpRlzs as $prvMtnOpRlz){
            if ($prvMtnOpRlz->prvMtnOpRlz_validate!="validated"){
                return response()->json([
                    'errors' => [
                        'state_verif' => ["You must validate all your preventive maintenance operation realized before add a new state"]
                    ]
                ], 429);

            }
        }
        $curMtnOps=CurativeMaintenanceOperation::where('state_id', '=', $id)->get() ;
        foreach ($curMtnOps as $curMtnOp){
            if ($curMtnOp->curMtnOp_validate!="validated"){
                return response()->json([
                    'errors' => [
                        'state_verif' => ["You must validate all your curative maintenance operation before add a new state"]
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
     * Function call by EquipmentStateForm.vue when we want to delete a state with the route : /equipment/delete/state{id}(post)
     * Delete a state thanks to the id given in parameter
     * The id parameter correspond to the id of the state we want to delete
     * */
    public function delete_state($id){
        $state=State::findOrFail($id);
        $state->delete() ;
    }
}

