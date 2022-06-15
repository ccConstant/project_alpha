<?php

/*
* Filename : StateController.php 
* Creation date : 17 May 2022
* Update date : 17 May 2022
* This file is used to link the view files and the database that concern the state table. 
* For example : add a state for an equipment in the data base, update a file, delete it...
*/ 

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EquipmentTemp ; 
use App\Models\State ; 
use App\Models\Equipment ; 
use App\Models\PreventiveMaintenanceOperationRealized;
use App\Models\CurativeMaintenanceOperation;


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
            'state_isOk' => $request->state_isOk,
            'state_startDate' => $date,
            'state_name' => $request->state_name,
        ]) ; 

        if ($request->state_name=="Reform"){
            $state->update([
                'reformedBy_id' => $request->enteredBy_id,
            ]);
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
            'state_isOk' => $request->state_isOk,
            'state_startDate' => $date,
            'state_name' => $request->state_name,
        ]) ;
    }

    /**
     * Function call by ReferenceAState.vue with the route : /state/send/{$id} (get)
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

                $obj=([
                    "id" => $state->id,
                    'state_remarks' => $state->state_remarks,
                    'state_validate' => $state->state_validate,
                    'state_isOk' => (boolean)$state->state_isOk,
                    'state_startDate' => $state->state_startDate,
                    'state_endDate' => $state->state_endDate,
                    'state_name' => $state->state_name,
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
            'state_isOk' => (boolean)$state->state_isOk,
            'state_startDate' => $state->state_startDate,
            'state_endDate' => $state->state_endDate,
            'state_name' => $state->state_name,
        ]);
        array_push($container,$obj);
        return response()->json($container) ;
    }


    /**
     * Function call by ListOfEquipmentLifeEvent with the route : /state/verif/beforeReferenceOp/{id} (post)
     * Check if we can create a new state (the previous state is validated, it has a endDate...) 
     * The id parameter is the id of the actual state 
     * @return \Illuminate\Http\Response
     */
    public function verif_before_reference_op(Request $request, $id){
        $state=State::findOrFail($id) ; 
        if ($state->state_name=="Lost"){
            return response()->json([
                'errors' => [
                    'verif_reference' => ["You can't reference a maintenance operation during a lost state"]
                ]
            ], 429);
        }

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyEqTmp->eqTemp_validate!="validated"){
            return response()->json([
                'errors' => [
                    'verif_reference' => ["You can't add a maintenance operation while you have'nt finished to complete the Id card of the equipment"]
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

