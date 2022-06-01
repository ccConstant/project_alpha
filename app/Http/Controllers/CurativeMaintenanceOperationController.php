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
use App\Models\State ; 
use App\Http\Controllers\PowerController ; 
use App\Http\Controllers\FileController ; 
use App\Http\Controllers\UsageController ; 
use App\Http\Controllers\StateController ; 
use App\Http\Controllers\RiskController ; 
use App\Http\Controllers\DimensionController;
use Carbon\Carbon;

class CurativeMaintenanceOperationController extends Controller
{

     /**
     * Function call by EquipmentCurMtnOpForm.vue when the form is submitted for check data with the route : /curMtnOp/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_curMtnOp(Request $request){
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
             //-----CASE curMtnOp->validate=drafted or curMtnOp->validate=to be validate----//
        //if the user has choosen "drafted" or "to be validated" he have no obligations 
        }else{
            $this->validate(
                $request,
                [
                    'curMtnOp_description' => 'required|min:1|max:50',
                ],
                [
                    'curMtnOp_description.required' => 'You must enter a description for the curative maintenance operation ',
                    'curMtnOp_description.min' => 'You must enter at least three characters ',
                ]
            );
        }


        if ($request->reason=="update"){
            $curMtnOp=CurativeMaintenanceOperation::FindOrFail($request->curMtnOp_id ) ;
            if ($curMtnOp->curMtnOp_validate=="VALIDATED"){
                return response()->json([
                    'errors' => [
                        'curMtnOp_validate' => ["You can't update a curative maintenance operation already validated"]
                    ]
                ], 429);
            }
        }

        if ($request->reason=="add"){
            $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
            if ($mostRecentlyEqTmp_validate!="VALIDATED"){
                return response()->json([
                    'errors' => [
                        'eqTemp_validate' => ["You can't add a curative maintenance operation while you have'nt finished to complete the Id card of the equipment"]
                    ]
                ], 429);
            }
        }
    }

    
    /**
     * Function call by EquipmentCurMtnOpForm.vue when the form is submitted for insert with the route :/equipment/add/state/curMtnOp (post)
     * Add a new enregistrement of curative maintenance operation in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new curMtnOp
     */
    public function add_curMtnOp(Request $request){
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
            'curMtnOp_startDate' => $state->state_startDate,
            'curMtnOp_endDate' => $state->state_endDate,
            'state_id' => $request->state_id,   
            'curMtnOp_number' => $max_number, 

        ]) ; 
        
        $curMtnOp_id=$curMtnOp->id;
        return response()->json($curMtnOp->id) ; 
    }

     /**
     * Function call by EquipmentCurMtnOpForm.vue when the form is submitted for update with the route :/equipment/update/state/curMtnOp/{id} (post)
     * Update an enregistrement of curative maintenance operation in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the curative maintenance operation we want to update
     * */
    public function update_curMtnOp(Request $request, $id){
        $curMtnOp=CurativeMaintenanceOperation::FindOrFail($id) ;
        $curMtnOp->update([
            'curMtnOp_reportNumber' => $request->curMtnOp_reportNumber,
            'curMtnOp_validate' => $request->curMtnOp_validate,
            'curMtnOp_description' => $request->curMtnOp_description,
        ]);
    }

    /**
     * Function call by EquipmentCurMtnOpForm.vue when we want to delete a curative maintenance operation with the route : /state/delete/curMtnOp/{id}(post)
     * Delete a curative maintenance operation thanks to the id given in parameter
     * The id parameter correspond to the id of the curative maintenance operation we want to delete
     * */
    public function delete_prvMtnOpRlz($id){
        $curMtnOp=CurativeMaintenanceOperation::findOrFail($id);
        $curMtnOp->delete() ; 
    }

     /**
     * Function call by ReferenceACurMtnOp.vue with the route : /state/curMtnOp/send/{id}(get)
     * Get the curative maintenance operations of the state whose id is passed in parameter
     * The id parameter corresponds to the id of the state from which we want the curative maintenance operations. 
     * @return \Illuminate\Http\Response 
     */

    public function send_curMtnOp($id) {
        $state = State::findOrFail($id);
        $container=array() ; 
        if (count($state->curative_maintenance_operations)>0){
            $curMtnOps=$state->curative_maintenance_operations ; 
            foreach ($curMtnOps as $curMtnOp) {
                $obj=([
                   "id" => $curMtnOp->id,
                    "curMtnOp_reportNumber" => $curMtnOp->curMtnOp_reportNumber,
                    "curMtnOp_description" => $curMtnOp->curMtnOp_description,
                    "curMtnOp_startDate" => $curMtnOp->curMtnOp_startDate,
                    "curMtnOp_endDate" => $curMtnOp->curMtnOp_endDate,
                    "curMtnOp_validate" => $curMtnOp->curMtnOp_validate,
                ]);
                array_push($container,$obj);
           }
       }
        return response()->json($container) ;
    }

     /**
     * Function call by DimensionController (and more) when we need to copy links between curMtnOp and a state
     * Copy the links between a curMtnOp and a state to the new state
     * The actualId parameter correspond of the id of the state from which we want to copy the curMtnOp
     * The newId parameter correspond of the id of the state where we want to copy the curMtnOp
     * The idNotCopy parameter correspond of the id of the curMtnOp we don't have to copy 
     * */
    public function copy_curMtnOp_linked_state($actualId, $newId, $idNotCopy){   
        $actualState= State::findOrFail($actualId) ; 
        $newState=State::findOrFail($newId) ; 
        $curMtnOps=$actualState->curative_maintenance_operations ; 
        foreach($curMtnOps as $curMtnOp){
            if ($curMtnOp->id!=$idNotCopy){
                //Creation of a new curative maintenance operation
                $curMtnOp=CurativeMaintenanceOperation::create([
                    'curMtnOp_reportNumber' => $curMtnOp->curMtnOp_reportNumber,
                    'curMtnOp_validate' => $curMtnOp->curMtnOp_validate,
                    'curMtnOp_description' => $curMtnOp->curMtnOp_description,
                    'curMtnOp_startDate' => $curMtnOp->curMtnOp_startDate,
                    'curMtnOp_endDate' => $curMtnOp->curMtnOp_endDate,
                    'state_id' => $newId,
                    'curMtnOp_number' => $curMtnOp->curMtnOp_number,
                ]) ; 
            }
        }
    }
    
}



