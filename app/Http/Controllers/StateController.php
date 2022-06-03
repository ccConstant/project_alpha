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
use App\Http\Controllers\DimensionController ; 
use App\Http\Controllers\PowerController ; 
use App\Http\Controllers\FileController ; 
use App\Http\Controllers\UsageController ; 
use App\Http\Controllers\StateController ; 
use App\Http\Controllers\RiskController ; 
use App\Http\Controllers\SpecialProcessController ; 
use App\Http\Controllers\PreventiveMaintenanceOperationController ; 

use Carbon\Carbon;


class StateController extends Controller
{

      /**
     * Function call by DimensionController (and more) when we need to copy links between equipment temp and state
     * Copy the links between a equipment temp and a state to the new equipment temp
     * The actualId parameter correspond of the id of the equipment from which we want to copy the states
     * The newId parameter correspond of the id of the equipment where we want to copy the states
     * The idNotCopy parameter correspond of the id of the state we don't have to copy 
     * */
    public function copy_state($actualId, $newId, $idNotCopy){ 
        $actualEqTemp= EquipmentTemp::findOrFail($actualId) ; 
        $newEqTemp= EquipmentTemp::findOrFail($newId) ; 
        $states=$actualEqTemp->states ; 
        foreach($states as $state){
            if ($state->id!=$idNotCopy){
                $state->equipment_temps()->attach($newEqTemp);
            }
        }
    }


    /**
     * Function call by EquipmentStateForm.vue when the form is submitted for check data with the route : /state/verif''(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_state(Request $request){

        //-----CASE state->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his state, so he must enter all the attributes
       
        if ($request->state_validate=='validated'){
            //verification about state_isOk, if no one value is selected we need to alert the user
            if ($request->state_isOk==='' || $request->state_isOk===NULL ){
                return response()->json([
                    'errors' => [
                        'state_isOk' => ["You must said if all the verifications in the state went well"]
                    ]
                ], 429);
            }
        }
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

        if ($request->state_startDate!='' && $request->state_endDate!=''){
            if ($request->state_endDate < $request->state_startDate){
                return response()->json([
                    'errors' => [
                        'state_endDate' => ["You must entered a startDate that is before endDate"]
                    ]
                ], 429);

            }
        }

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        $states=$mostRecentlyEqTmp->states;
        if ($states!=NULL){
            $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
            foreach($states as $state){
                $date=$state->created_at ; 
                $date2=$mostRecentlyState->created_at;
               if ($date>=$date2){
                     $mostRecentlyState=$state ; 
                }
            }

           if ($request->state_startDate!=NULL && $request->state_startDate<$mostRecentlyState->state_endDate){
                return response()->json([
                    'errors' => [
                        'state_startDate' => ["You must entered a startDate that is after the previous state"]
                    ]
                ], 429);
            }
    
            if ($request->state_endDate!=NULL && $request->state_endDate<$mostRecentlyState->state_endDate){
                return response()->json([
                    'errors' => [
                        'state_endDate' => ["You must entered a endDate that is after the previous state"]
                    ]
                ], 429);
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
        
        //Creation of a new state
        $state=State::create([
            'state_remarks' => $request->state_remarks,
            'state_validate' => $request->state_validate,
            'state_isOk' => $request->state_isOk,
            'state_startDate' => $date,
            'state_endDate' => $request->state_endDate,
            'state_name' => $request->state_name,
        ]) ; 
        
        $state_id=$state->id;
        $id_eq=intval($request->eq_id) ; 
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyEqTmp!=NULL){
             //If the equipment temp is validated and a life sheet has been already created, we need to create another equipment temp (that's mean another life sheet version) for add state
            if ((boolean)$mostRecentlyEqTmp->eqTemp_lifeSheetCreated==true && $mostRecentlyEqTmp->eqTemp_validate=="VALIDATED"){
                
               //We need to increase the number of equipment temp linked to the equipment
               $version_eq=$equipment->eq_nbrVersion+1 ; 
               //Update of equipment
               $equipment->update([
                   'eq_nbrVersion' =>$version_eq,
               ]);
               
               //We need to increase the version of the equipment temp (because we create a new equipment temp)
               $version =  $mostRecentlyEqTmp->eqTemp_version+1 ; 
               //Creation of a new equipment temp
               $new_eqTemp=EquipmentTemp::create([
                   'equipment_id'=> $request->eq_id,
                   'eqTemp_version' => $version,
                   'eqTemp_date' => Carbon::now('Europe/Paris'),
                   'eqTemp_validate' => $mostRecentlyEqTmp->eqTemp_validate,
                   'enumMassUnit_id' => $mostRecentlyEqTmp->enumMassUnit_id,
                   'eqTemp_mass' => $mostRecentlyEqTmp->eqTemp_mass,
                   'eqTemp_remarks' => $mostRecentlyEqTmp->eqTemp_remarks,
                   'qualityVerifier_id' => $mostRecentlyEqTmp->qualityVerifier_id,
                   'technicalVerifier_id' => $mostRecentlyEqTmp->technicalVerifier_id,
                   'createdBy_id' => $mostRecentlyEqTmp->createdBy_id,
                   'eqTemp_mobility' => $mostRecentlyEqTmp->eqTemp_mobility,
                   'enumType_id' => $mostRecentlyEqTmp->enumType_id,
                   'specialProcess_id' => $mostRecentlyEqTmp->specialProcess_id,
               ]);

               $state->equipment_temps()->attach($new_eqTemp);
                   
               //We copy the links of the actual Equipment temp to the new equipment temp 
                $DimController= new DimensionController() ; 
                $DimController->copy_dimension($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

                $SpProcController= new SpecialProcessController() ; 
                $SpProcController->copy_specialProcess($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
        
               $PowerController= new PowerController() ; 
               $PowerController->copy_power($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $FileController= new FileController() ; 
               $FileController->copy_file($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $UsageController= new UsageController() ; 
               $UsageController->copy_usage($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $StateController= new StateController() ; 
               $StateController->copy_state($mostRecentlyEqTmp->id, $new_eqTemp->id, $state_id) ; 
           
               $RiskController= new RiskController() ; 
               $RiskController->copy_risk($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $PreventiveMaintenanceOperationController= new PreventiveMaintenanceOperationController() ; 
               $PreventiveMaintenanceOperationController->copy_preventiveMaintenanceOperation($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

             // In the other case, we can add informations without problems
            }else{

                $state->equipment_temps()->attach($mostRecentlyEqTmp) ;

            }
            return response()->json($state_id) ; 
        }
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
        
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        //We search the most recently equipment temp of the equipment 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->latest()->first();
        if ($mostRecentlyEqTmp!=NULL){
            //We checked if the most recently equipment temp is validate and if a life sheet has been already created.
            //If the equipment temp is validated and a life sheet has been already created, we need to create another equipment temp (that's mean another life sheet version)
            if ($mostRecentlyEqTmp->eqTemp_validate=="VALIDATED" && (boolean)$mostRecentlyEqTmp->eqTemp_lifeSheetCreated==true){
            
                //We need to increase the number of equipment temp linked to the equipment
                $version_eq=$equipment->eq_nbrVersion+1 ; 
                //Update of equipment
                $equipment->update([
                    'eq_nbrVersion' =>$version_eq,
                ]);
                
               //We need to increase the version of the equipment temp (because we create a new equipment temp)
                $version =  $mostRecentlyEqTmp->eqTemp_version+1 ; 
                //Creation of a new equipment temp
                $new_eqTemp=EquipmentTemp::create([
                    'equipment_id'=> $request->eq_id,
                    'eqTemp_version' => $version,
                    'eqTemp_date' => Carbon::now('Europe/Paris'),
                    'eqTemp_validate' => $mostRecentlyEqTmp->eqTemp_validate,
                    'enumMassUnit_id' => $mostRecentlyEqTmp->enumMassUnit_id,
                    'eqTemp_mass' => $mostRecentlyEqTmp->eqTemp_mass,
                    'eqTemp_remarks' => $mostRecentlyEqTmp->eqTemp_remarks,
                    'qualityVerifier_id' => $mostRecentlyEqTmp->qualityVerifier_id,
                    'technicalVerifier_id' => $mostRecentlyEqTmp->technicalVerifier_id,
                    'createdBy_id' => $mostRecentlyEqTmp->createdBy_id,
                    'eqTemp_mobility' => $mostRecentlyEqTmp->eqTemp_mobility,
                    'enumType_id' => $mostRecentlyEqTmp->enumType_id,
                    'specialProcess_id' => $mostRecentlyEqTmp->specialProcess_id,
                ]);
                
                //Creation of a new state
                $state=State::create([
                    'state_remarks' => $request->state_remarks,
                    'state_validate' => $request->state_validate,
                    'state_isOk' => $request->state_isOk,
                    'state_startDate' => $date,
                    'state_endDate' => $request->state_endDate,
                    'state_name' => $request->state_name,
                ]) ; 

                $state->equipment_temps()->attach($new_eqTemp);
                    
                //Dédoubler les liens de eqTemps 
                $DimController= new DimensionController() ; 
                $DimController->copy_dimension($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

                $SpProcController= new SpecialProcessController() ; 
                $SpProcController->copy_specialProcess($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
        
               $PowerController= new PowerController() ; 
               $PowerController->copy_power($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $FileController= new FileController() ; 
               $FileController->copy_file($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $UsageController= new UsageController() ; 
               $UsageController->copy_usage($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $StateController= new StateController() ; 
               $StateController->copy_state($mostRecentlyEqTmp->id, $new_eqTemp->id, $id) ; 
           
               $RiskController= new RiskController() ; 
               $RiskController->copy_risk_eqTemp($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $PreventiveMaintenanceOperationController= new PreventiveMaintenanceOperationController() ; 
               $PreventiveMaintenanceOperationController->copy_preventiveMaintenanceOperation($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
            

                // In the other case, we can modify the informations without problems
            }else{

                $state=State::findOrFail($id) ; 
                $state->update([
                    'state_remarks' => $request->state_remarks,
                   'state_validate' => $request->state_validate,
                    'state_isOk' => $request->state_isOk,
                    'state_startDate' => $date,
                    'state_endDate' => $request->state_endDate,
                    'state_name' => $request->state_name,
                ]) ; 
                return response()->json($request->state_validate) ; 
            }
        }
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
            'state_name' => $state_state_name,
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
        if ($mostRecentlyEqTmp->eqTemp_validate!="VALIDATED"){
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
            if ($prvMtnOpRlz->prvMtnOpRlz_validate!="VALIDATED"){
                return response()->json([
                    'errors' => [
                        'state_verif' => ["You must validate all your preventive maintenance operation realized before add a new state"]
                    ]
                ], 429);    

            }
        }
        $curMtnOps=CurativeMaintenanceOperation::where('state_id', '=', $id)->get() ; 
        foreach ($curMtnOps as $curMtnOp){
            if ($curMtnOp->curMtnOp_validate!="VALIDATED"){
                return response()->json([
                    'errors' => [
                        'state_verif' => ["You must validate all your curative maintenance operation before add a new state"]
                    ]
                ], 429);    

            }
        }
        if ($state->state_endDate=='' || $state->state_endDate==NULL){
            return response()->json([
                'errors' => [
                    'state_verif' => ["You don\'t have entered a endDate for your state"]
                ]
            ], 429);    
        }

        if ($state->state_validate!="Validated"){
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

