<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\PreventiveMaintenanceOperationRealized ; 
use App\Models\EquipmentTemp ; 
use App\Models\Equipment ; 
use App\Models\State ; 
use App\Models\PreventiveMaintenanceOperation ; 
use App\Http\Controllers\DimensionController ; 
use App\Http\Controllers\PowerController ; 
use App\Http\Controllers\FileController ; 
use App\Http\Controllers\UsageController ; 
use App\Http\Controllers\StateController ; 
use App\Http\Controllers\RiskController ; 
use App\Http\Controllers\PreventiveMaintenanceOperationController ; 
use Carbon\Carbon;


class PreventiveMaintenanceOperationRealizedController extends Controller
{
    

    /**
     * Function call by EquipmentPrvMtnOpRlzForm.vue when the form is submitted for insert with the route :/equipment/add/state/prvMtnOpRlz (post)
     * Add a new enregistrement of preventive maintenance operation realized in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : the id of the new prvMtnOpRlz
     */
    public function add_prvMtnOpRlz(Request $request){
        $state=State::findOrFail($request->state_id) ; 
        $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($request->prvMtnOp_id) ; 

        //Creation of a new preventive maintenance operation realized
        $prvMtnOpRlz=PreventiveMaintenanceOperationRealized::create([
            'prvMtnOpRlz_reportNumber' => $request->prvMtnOpRlz_reportNumber,
            'prvMtnOpRlz_validate' => $request->prvMtnOpRlz_validate,
            'prvMtnOpRlz_startDate' => $state->state_startDate,
            'prvMtnOpRlz_endDate' => $state->state_endDate,
            'prvMtnOpRlz_entryDate' => Carbon::now('Europe/Paris'),
            'state_id' => $request->state_id,
            'prvMtnOp_id' => $request->prvMtnOp_id,

        ]) ; /*

        $prvMtnOpRlz_id=$prvMtnOpRlz->id;
        return response()->json($prvMtnOpRlz_id) ;*/
    }


     /**
     * Function call by EquipmentPrvMtnOpRlzForm.vue when the form is submitted for check data with the route : /prvMtnOpRlz/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_prvMtnOpRlz(Request $request){        
        $this->validate(
            $request,
            [
                'prvMtnOpRlz_reportNumber' => 'required|min:3|max:255',
            ],
            [
                'prvMtnOpRlz_reportNumber.required' => 'You must enter a report number for the preventive maintenance operation realized ',
                'prvMtnOpRlz_reportNumber.min' => 'You must enter at least three characters ',
                'prvMtnOpRlz_reportNumber.max' => 'You must enter a maximum of 255 characters',

            
            ]
        );
        $state=State::findOrFail($request->state_id) ; 
        $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($request->prvMtnOp_id) ; 
        if ($state->startDate!='' && $prvMtnOp->prvMtnOp_startDate!=''){
            if ($prvMtnOp->prvMtnOp_startDate>$state->state_startDate){
                return response()->json([
                    'errors' => [
                        'operation_maintenance_preventive' => ["You can choose this preventive maintenance operation because the startDate of it it after the beginning of your state"]
                    ]
                ], 429);
            }
        }
        if ($state->endDate!='' && $prvMtnOp->reformDate!=''){
            if ($prvMtnOp->prvMtnOp_reformDate<$state->state_endDate){
                return response()->json([
                    'errors' => [
                        'operation_maintenance_preventive' => ["You can choose this preventive maintenance operation because the endDate of it it before the ending of your state"]
                    ]
                ], 429);
            }
        }

        if ($request->reason=="update"){
            $prvMtnOpRlz=PreventiveMaintenanceOperationRealized::FindOrFail($request->prvMtnOpRlz_id ) ;
            if ($prvMtnOpRlz->prvMtnOpRlz_validate=="VALIDATED"){
                return response()->json([
                    'errors' => [
                        'prvMtnOpRlz_validate' => ["You can't update a preventive maintenance operation realized already validated"]
                    ]
                ], 429);
            }
        }

        if ($request->reason=="add"){
            $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
            if ($mostRecentlyEqTmp_validate!="VALIDATED"){
                return response()->json([
                    'errors' => [
                        'eqTemp_validate' => ["You can't add a preventive maintenance operation realized while you have'nt finished to complete the Id card of the equipment"]
                    ]
                ], 429);
            }
        }
    }

    /**
     * Function call by EquipmentPrvMtnOpRlzForm.vue when the form is submitted for update with the route :/equipment/update/prvMtnOpRlz/{id} (post)
     * Update an enregistrement of preventive maintenance operation realized in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the preventive maintenance operation realized we want to update
     * */
    public function update_prvMtnOpRlz(Request $request, $id){
        $prvMtnOpRlz=PreventiveMaintenanceOperationRealized::FindOrFail($id) ;
        $prvMtnOpRlz->update([
            'prvMtnOpRlz_reportNumber' => $request->prvMtnOpRlz_reportNumber,
            'prvMtnOpRlz_validate' => $request->prvMtnOpRlz_validate,
        ]);
    }


    /**
     * Function call by EquipmentPrvMtnOpRlzForm.vue when we want to delete a preventive maintenance operation realized with the route : /state/delete/prvMtnOpRlz/{id}(post)
     * Delete a preventive maintenance operation realized thanks to the id given in parameter
     * The id parameter correspond to the id of the preventive maintenance operation realized we want to delete
     * */
    public function delete_prvMtnOpRlz($id){
        $prvMtnOpRlz=PreventiveMaintenanceOperationRealized::findOrFail($id);
        $prvMtnOpRlz->delete() ; 
    }

    /**
     * Function call by ReferenceAPrvMtnOpRlz.vue with the route : /state/prvMtnOpRlz/send/{id}(get)
     * Get the preventive maintenance operations realized of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the state from which we want the preventive maintenance operation realized
     * @return \Illuminate\Http\Response
     */

    public function send_prvMtnOpRlz($id) {
        $state = State::findOrFail($id);
        $container=array() ; 
        if (count($state->preventive_maintenance_operation_realizeds)>0){
            $prvMtnOpRlzs=$state->preventive_maintenance_operation_realizeds ; 
            foreach ($prvMtnOpRlzs as $prvMtnOpRlz) {
                $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($prvMtnOpRlz->prvMtnOp_id) ; 
                $obj=([
                    "id" => $prvMtnOpRlz->id,
                    "prvMtnOpRlz_reportNumber" => $prvMtnOpRlz->prvMtnOpRlz_reportNumber,
                    "prvMtnOpRlz_startDate" => $prvMtnOpRlz->prvMtnOpRlz_startDate,
                    "prvMtnOpRlz_endDate" => $prvMtnOpRlz->prvMtnOpRlz_endDate,
                    "prvMtnOpRlz_entryDate" => $prvMtnOpRlz->prvMtnOpRlz_entryDate,
                    "prvMtnOpRlz_validate" => $prvMtnOpRlz->prvMtnOpRlz_validate,
                    "prvMtnOp_id" => $prvMtnOpRlz->prvMtnOp_id,
                    "prvMtnOp_number" => (string)$prvMtnOp->prvMtnOp_number, 
                    "prvMtnOp_description" => $prvMtnOp->prvMtnOp_description, 
                    "prvMtnOp_protocol" => $prvMtnOp->prvMtnOp_protocol, 
                ]);

                array_push($container,$obj);
            }
        }
        return response()->json($container) ; 
    }

     /**
     * Function call by DimensionController (and more) when we need to copy links between prvMtnOpRlz and a state
     * Copy the links between a state and a preventive maintenance operation realized to the new state
     *  The actualId parameter correspond of the id of the state from which we want to copy the preventive maintenance operation realized
     * The newId parameter correspond of the id of the state where we want to copy the preventive maintenance operation realized
     * The idNotCopy parameter correspond of the id of the preventive maintenance operation realized we don't have to copy 
     * */
    public function copy_prvMtnOpRlz_linked_state($actualId, $newId, $idNotCopy){   
        $actualState= State::findOrFail($actualId) ; 
        $newState= State::findOrFail($newId) ; 
        $prvMtnOpRlzs=$actualState->preventive_maintenance_operation_realizeds ; 
        foreach($prvMtnOpRlzs as $prvMtnOpRlz){
            if ($prvMtnOpRlz->id!=$idNotCopy){
                //Creation of a new preventive maintenance operation realized
                $newPrvMtnOpRlz=PreventiveMaintenanceOperationRealized::create([
                    'prvMtnOpRlz_reportNumber' => $prvMtnOpRlz->prvMtnOpRlz_reportNumber,
                    'prvMtnOpRlz_validate' => $prvMtnOpRlz->prvMtnOpRlz_validate,
                    'prvMtnOpRlz_startDate' => $prvMtnOpRlz->prvMtnOpRlz_startDate,
                    'prvMtnOpRlz_endDate' => $prvMtnOpRlz->prvMtnOpRlz_endDate,
                    'prvMtnOpRlz_entryDate' => $prvMtnOpRlz->prvMtnOpRlz_entryDate,
                    'state_id' => $newId,
                    'prvMtnOp_id' => $prvMtnOpRlz->prvMtnOp_id,
                ]) ; 
            }
        }
    }

     /**
     * Function call by DimensionController (and more) when we need to copy links between prvMtnOpRlz and a prvMtnOpRlz
     * Copy the links between a prvMtnOp and a preventive maintenance operation realized to the new prvMtnOp
     * The actualId parameter correspond of the id of the preventive maintenance operation from which we want to copy the preventive maintenance operation realized
     * The newId parameter correspond of the id of the preventive maintenance operation where we want to copy the preventive maintenance operation realized
     * The idNotCopy parameter correspond of the id of the preventive maintenance operation realized we don't have to copy 
     * */
    public function copy_prvMtnOpRlz_linked_prvMtnOp($actualId, $newId, $idNotCopy){   
        $actualprvMtnOp= PreventiveMaintenanceOperation::findOrFail($actualId) ; 
        $newprvMtnOp= PreventiveMaintenanceOperation::findOrFail($newId) ; 
        $prvMtnOpRlzs=$actualprvMtnOp->preventiveMaintenanceOperationRealizeds ; 
        foreach($prvMtnOpRlzs as $prvMtnOpRlz){
            if ($prvMtnOpRlz->id!=$idNotCopy){
                //Creation of a new preventive maintenance operation realized
                $newPrvMtnOpRlz=PreventiveMaintenanceOperationRealized::create([
                    'prvMtnOpRlz_reportNumber' => $prvMtnOpRlz->prvMtnOpRlz_reportNumber,
                    'prvMtnOpRlz_validate' => $prvMtnOpRlz->prvMtnOpRlz_validate,
                    'prvMtnOpRlz_startDate' => $prvMtnOpRlz->prvMtnOpRlz_startDate,
                    'prvMtnOpRlz_endDate' => $prvMtnOpRlz->prvMtnOpRlz_endDate,
                    'prvMtnOpRlz_entryDate' => $prvMtnOpRlz->prvMtnOpRlz_entryDate,
                    'state_id' => $prvMtnOpRlz->state_id,
                    'prvMtnOp_id' => $newId,
                ]) ; 
            }
        }
    }
}
