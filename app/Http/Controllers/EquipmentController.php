<?php

/*
* Filename : EquipmentController.php 
* Creation date : 27 Apr 2022
* Update date : 17 May 2022
* This file is used to link the view files and the database that concern the equipment table. 
* For example : add the identity card of an equipment in the database, update the identity card, delete the identity card... 
*/ 

namespace App\Http\Controllers;

use Illuminate\Http\Request ; 
use Illuminate\Support\Facades\DB ; 
use App\Models\Equipment;
use App\Models\EquipmentTemp;
use App\Models\PreventiveMaintenanceOperation;
use App\Models\State;
use App\Models\EnumEquipmentMassUnit ;
use App\Models\EnumEquipmentType ;
use App\Models\EnumStateName ;
use App\Http\Controllers\PowerController ; 
use App\Http\Controllers\FileController ; 
use App\Http\Controllers\UsageController ; 
use App\Http\Controllers\StateController ; 
use App\Http\Controllers\RiskController ; 
use App\Http\Controllers\SpecialProcessController ; 
use App\Http\Controllers\PreventiveMaintenanceOperationController ; 
use Carbon\Carbon;

class EquipmentController extends Controller{
    

    /**
     * Function call by ListOfEquipment.vue with the route : /equipment/equipments (get)
     * Get all the internalReference and the id of equipment in the data base for print them in the vue
     * @return \Illuminate\Http\Response
     */

    public function send_internalReferences_ids (){
        $equipments= Equipment::all() ;
        $container=array() ; 
        foreach($equipments as $equipment){
            $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $equipment->id)->orderBy('created_at', 'desc')->first();
            $states=$mostRecentlyEqTmp->states;
            $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
            foreach($states as $state){
                $date=$state->created_at ; 
                $date2=$mostRecentlyState->created_at;
               if ($date>=$date2){
                     $mostRecentlyState=$state ; 
                }
            }
            $obj=([
                'id' => $equipment->id,
                'eq_internalReference' => $equipment->eq_internalReference,
                'eq_state' =>  $mostRecentlyState->enumStateName->value,
                'state_id' => $mostRecentlyState->id,
            ]);
            array_push($container,$obj);
        }
        return response()->json($container) ;
    }


     /**
     * Function call by ImportationModal.vue with the route : /equipments/same_set/{$set} (get)
     * Get the Equipments with the same set as the one in parameters
     * The set in parameter correspond of the set of equipment we actually create : this set allow us to import many characteritics from another equipment if the set is the same 
     * @return \Illuminate\Http\Response
     */

    public function send_equipments_same_set($set){
        $equipments_same_set=Equipment::where('eq_set', $set)->get();
        return response()->json($equipments_same_set) ;    
    }

    /**
     * Function call by EquipmentConsult.vue with the route : /equipment/{id} (get)
     * Get equipment corresponding to the id in the data base for print it in the vue
     * The id parameter corresponds to the id of the equipment from which we want the informations (internalReference, externalReference...)
     * @return \Illuminate\Http\Response
     */

    public function send_equipment ($id){
        $equipment= Equipment::findOrFail($id) ;
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $validate=NULL ; 
        $massUnit=NULL;
        $type = NULL ; 
        if ($mostRecentlyEqTmp!=NULL){
            $validate=$mostRecentlyEqTmp->eqTemp_validate ; 
            $mass=$mostRecentlyEqTmp->eqTemp_mass ;
            $remarks=$mostRecentlyEqTmp->eqTemp_remarks ;
            $mobility=$mostRecentlyEqTmp->eqTemp_mobility;

            if ($mostRecentlyEqTmp->enumMassUnit_id!=NULL){
                $massUnit = $mostRecentlyEqTmp->enumEquipmentMassUnit->value ;
            }

            if ($mostRecentlyEqTmp->enumType_id!=NULL){
                $type = $mostRecentlyEqTmp->enumEquipmentType->value ;
            }
        }
        return response()->json([
            'eq_internalReference' => $equipment->eq_internalReference,
            'eq_externalReference' => $equipment->eq_externalReference,
            'eq_name' => $equipment->eq_name,
            'eq_type'=> $type,
            'eq_serialNumber' => $equipment->eq_serialNumber,
            'eq_constructor'  => $equipment->eq_constructor,
            'eq_mass'  => (string)$mass,
            'eq_remarks'  => $remarks,
            'eq_set'  => $equipment->eq_set,
            'eq_massUnit'=> $massUnit,
            'eq_mobility'=> (boolean)$mobility,
            'eq_validate' => $validate,
        ]);
    }


     /**
     * Function call by EquipmentIDForm.vue with the route : /equipment/sets (get)
     * Get all the differents sets in the data base and send them to the vue 
     * @return \Illuminate\Http\Response
     */

    public function send_sets (){
        $sets=DB::select(DB::raw('SELECT DISTINCT eq_set FROM equipment'));
        return response()->json($sets) ;
    }


    /**
     * Function call by EquipmentIDForm.vue when the form is submitted for insert with the route : /equipment/add (post)
     * Add a new enregistrement of equipment and equipment_temp in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new equipment
     */
    public function add_equipment(Request $request){

        $state_id=NULL ;
        $state= EnumStateName::where('value', '=', "WAITING_FOR_REFERENCING")->first() ;
        if ($state===NULL){
            $state=EnumStateName::create([
                'value' => "WAITING_FOR_REFERENCING", 
            ]);
        }
        $state_id=$state->id ; 

        
        //An equipment is linked to its mass unit. So we need to find the id of the massUnit choosen by the user and write it in the attribute of the equipment.
        //But if no one mass unit is choosen by the user we define this id to NULL
        // And if the massUnit choosen is find in the data base the NULL value will be replace by the id value
        $massUnit_id=NULL ;
        if ($request->eq_massUnit!=''){
            $massUnit= EnumEquipmentMassUnit::where('value', '=', $request->eq_massUnit)->first() ;
            $massUnit_id=$massUnit->id ; 
        }

        //An equipment is linked to its type. So we need to find the id of the type choosen by the user and write it in the attribute of the equipment.
        //But if no one type is choosen by the user we define this id to NULL
        // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $type_id=NULL ; 
        if ($request->eq_type!=''){
            $type= EnumEquipmentType::where('value', '=', $request->eq_type)->first() ;
            $type_id=$type->id ; 
        }
        
        //Creation of a new equipment
        $equipment=Equipment::create([
            'eq_internalReference' => $request->eq_internalReference,
            'eq_externalReference' => $request->eq_externalReference, 
            'eq_name' => $request->eq_name,
            'eq_serialNumber' => $request->eq_serialNumber,
            'eq_constructor' => $request->eq_constructor,
            'eq_set' => $request->eq_set,
        ]) ; 

        $equipment_id=$equipment->id ; 

        
        //Creation of a new equipment temp
        $new_eqTemp=EquipmentTemp::create([
            'equipment_id'=> $equipment_id,
            'eqTemp_version' => '1',
            'eqTemp_date' => Carbon::now('Europe/Paris'),
            'eqTemp_validate' => $request->eq_validate,
            'enumMassUnit_id' => $massUnit_id,
            'eqTemp_mass' => $request->eq_mass,
            'eqTemp_remarks' => $request->eq_remarks,
            'eqTemp_mobility' => $request->eq_mobility,
            'enumType_id' => $type_id,
        ]);

        //Creation of a new state
        $newState=State::create([
            'state_remarks' => "State by default",
            'state_startDate' =>  Carbon::now('Europe/Paris'),
            'state_isOk' => true,
            'state_validate' => "drafted",
            'enumStateName_id' => $state_id
        ]) ; 
        
        $newState->equipment_temps()->attach($new_eqTemp);
        return response()->json($equipment_id) ; 
    }

    /**
     * Function call by EquipmentIDForm.vue when the form is submitted for check data with the route : /equipment/add (post)
     * Check the informations entered in the form and send the errors if it exists
     */
    public function verif_equipment(Request $request){

        // We need to do many verifications on the data entered by the user.
        // If the user make a mistake, we send to the vue an error to explain it and print it to the user.


        //-----CASE eq->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his equipment, so he must enter all the attributes
        if ($request->eq_validate=='validated'){
            $this->validate(
                $request,
                [
                    'eq_internalReference' => 'required|min:3|max:16',
                    'eq_externalReference' => 'required|min:3|max:100',
                    'eq_name'  => 'required|min:3|max:100', 
                    'eq_serialNumber'  => 'required|min:3|max:50',
                    'eq_constructor'  => 'required|min:3|max:30',
                    'eq_mass'  => 'required|max:8',
                    'eq_remarks'  => 'required|min:3|max:400',
                    'eq_set'  => 'required|min:1|max:20',
                ],
                [
                    'eq_internalReference.required' => 'You must enter an internal reference ',
                    'eq_internalReference.min' => 'You must enter at least 3 characters ',
                    'eq_internalReference.max' => 'You must enter a maximum of 16 characters',

                    'eq_externalReference.required' => 'You must enter an external reference',
                    'eq_externalReference.min' => 'You must enter at least 3 characters ',
                    'eq_externalReference.max' => 'You must enter a maximum of 100 characters',

                    'eq_name.required' => 'You must enter a name',
                    'eq_name.min' => 'You must enter at least 3 characters ',
                    'eq_name.max' => 'You must enter a maximum of 100 characters',

                    'eq_serialNumber.required'  => 'You must enter a serial number', 
                    'eq_serialNumber.min'  => 'You must enter at least 3 characters ',
                    'eq_serialNumber.max'  =>  'You must enter a maximum of 50 characters',

                    'eq_constructor.required'  => 'You must enter a constructor', 
                    'eq_constructor.min'  => 'You must enter at least 3 characters ',
                    'eq_constructor.max'  =>  'You must enter a maximum of 30 characters',

                    'eq_mass.required'  => 'You must enter a mass', 
                    'eq_mass.max'  => 'You must enter a maximum of 8 characters',

                    'eq_remarks.required'  => 'You must enter a remark',
                    'eq_remarks.min'  =>  'You must enter at least 3 characters ',
                    'eq_remarks.max'  => 'You must enter a maximum of 400 characters',

                    'eq_set.required'  => 'You must enter a set',
                    'eq_set.min'  => 'You must enter at least 1 characters ',
                    'eq_set.max'  => 'You must enter a maximum of 20 characters',
                     
                ]
            );

            //verification about eq_type, if no one value is selected we need to alert the user
            if ($request->eq_type=='' || $request->eq_type==NULL ){
                return response()->json([
                    'errors' => [
                        'eq_type' => ["You must choose a type"]
                    ]
                ], 429);
            }

            //verification about eq_massUnit, if no one value is selected we need to alert the user
            if ($request->eq_massUnit=='' || $request->eq_massUnit==NULL){
                return response()->json([
                    'errors' => [
                        'eq_massUnit' => ["You must choose a unit of mass"]
                    ]
                ], 429);
            }
        }else{
             //-----CASE eq->validate=drafted or eq->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" value he must enter only the internReference and externReference
            $this->validate(
                $request,
                [
                    'eq_internalReference' => 'required|min:3|max:16',
                    'eq_externalReference' => 'required|min:3|max:100',
                ],
                [
                    
                    'eq_internalReference.required' => 'You must enter an internal reference ',
                    'eq_internalReference.min' => 'You must enter at least 3 characters ',
                    'eq_internalReference.max' => 'You must enter a maximum of 16 characters',

                    'eq_externalReference.required' => 'You must enter an external reference',
                    'eq_externalReference.min' => 'You must enter at least 3 characters ',
                    'eq_externalReference.max' => 'You must enter a maximum of 100 characters',
                ]
            );
        }

        if ($request->reason=="update"){
            //we checked if the internal reference entered is already used for another equipment
            $equipment_already_exist=Equipment::where('eq_internalReference', '=', $request->eq_internalReference, 'and')->where('id', '<>', $request->eq_id)->first() ; 
            if ($equipment_already_exist!=null){
                return response()->json([
                    'errors' => [
                        'eq_internalReference' => ["This internal reference is already use for another equipment"]
                    ]
                ], 429);
            }

            //We search the most recently equipment temp of the equipment 
            $equipment= Equipment::findOrFail($request->eq_id) ;
            $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
            if ($mostRecentlyEqTmp!=NULL){
                //we checked if a life sheet is already created. If it's the case we can't update the internalReference
                if ((boolean)$mostRecentlyEqTmp->eqTemp_lifeSheetCreated==true){                
                    if($equipment->eq_internalReference!=$request->eq_internalReference){
                        return response()->json([
                            'errors' => [
                                'eq_internalReference' => ["You can't modify the internal reference because a life sheet has already been created"]
                            ]
                        ], 429);
                    }
                    if($equipment->eq_externalReference!=$request->eq_externalReference){
                        return response()->json([
                            'errors' => [
                                'eq_externalReference' => ["You can't modify the external reference because a life sheet has already been created"]
                            ]
                        ], 429);
                    }
                    if($equipment->eq_name!=$request->eq_name){
                        return response()->json([
                            'errors' => [
                                'eq_name' => ["You can't modify the name because a life sheet has already been created"]
                            ]
                        ], 429);
                    }
                    if($equipment->eq_serialNumber!=$request->eq_serialNumber){
                        return response()->json([
                            'errors' => [
                                'eq_serialNumber' => ["You can't modify the serial number because a life sheet has already been created"]
                            ]
                        ], 429);
                    }
                    if($equipment->eq_constructor!=$request->eq_constructor){
                        return response()->json([
                            'errors' => [
                                'eq_constructor' => ["You can't modify the constructor because a life sheet has already been created"]
                            ]
                        ], 429);
                    }

                    if($equipment->eq_set!=$request->eq_set){
                        return response()->json([
                            'errors' => [
                                'eq_set' => ["You can't modify the set because a life sheet has already been created"]
                            ]
                        ], 429);
                    }

                    if($equipment->eq_mobility!=$request->eq_mobility){
                        return response()->json([
                            'errors' => [
                                'eq_mobility' => ["You can't modify the set because a life sheet has already been created"]
                            ]
                        ], 429);
                    }
                }
                
            }
        }else{
            if ($request->reason=="add"){
                //we checked if the internal reference entered is already used for another equipment
                $equipment_already_exist= Equipment::where('eq_internalReference', '=', $request->eq_internalReference)->first() ; 
                
                if ($equipment_already_exist!=null){
                    return response()->json([
                        'errors' => [
                            'eq_internalReference' => ["This internal reference is already use for another equipment"]
                        ]
                    ], 429);
                }
            }
        }
    }


    /**
     * Function call by EquipmentIDForm.vue when the form is submitted for update with the route : /equipment/update (post)
     * Update an enregistrement of equipment and equipment_temp in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the equipment we want to update
     * */
    public function update_equipment(Request $request, $id){
        $equipment= Equipment::findOrFail($id) ;
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        
        //An equipment is linked to its mass unit. So we need to find the id of the massUnit choosen by the user and write it in the attribute of the equipment.
        //But if no one mass unit is choosen by the user we define this id to NULL
        // And if the massUnit choosen is find in the data base the NULL value will be replace by the id value
        $massUnit_id=NULL ;
        if ($request->eq_massUnit!=''){
            $massUnit= EnumEquipmentMassUnit::where('value', '=', $request->eq_massUnit)->first() ;
            $massUnit_id=$massUnit->id ; 
        }

        //An equipment is linked to its type. So we need to find the id of the type choosen by the user and write it in the attribute of the equipment.
        //But if no one type is choosen by the user we define this id to NULL
        // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $type_id=NULL ; 
        if ($request->eq_type!=''){
            $type= EnumEquipmentType::where('value', '=', $request->eq_type)->first() ;
            $type_id=$type->id ; 
        }

        //We checked if the most recently equipment temp is validate and if a life sheet has been already created.
        //If the equipment temp is validated and a life sheet has been already created, we need to create another equipment temp (that's mean another life sheet version)
        if ($mostRecentlyEqTmp->eqTemp_validate=="VALIDATED" && (boolean)$mostRecentlyEqTmp->eqTemp_lifeSheetCreated===true && ($type_id != $mostRecentlyEqTmp->enumType_id || $request->eq_mass != $mostRecentlyEqTmp->eqTemp_mass || $massUnit_id != $mostRecentlyEqTmp->enumMassUnit_id || $request->eq_remarks!=$mostRecentlyEqTmp->eqTemp_remarks || $request->eq_mobility!=$mostRecentlyEqTmp->eqTemp_mobility)){
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
                'equipment_id'=> $id,
                'eqTemp_version' => $version,
                'eqTemp_date' => Carbon::now('Europe/Paris'),
                'eqTemp_validate' => $request->eq_validate,
                'enumMassUnit_id' => $massUnit_id,
                'eqTemp_mass' => $request->eq_mass,
                'eqTemp_remarks' => $request->eq_remarks,
                // NEED TO UPDATE
                'qualityVerifier_id' => $mostRecentlyEqTmp->qualityVerifier_id,
                'technicalVerifier_id' => $mostRecentlyEqTmp->technicalVerifier_id,
                'createdBy_id' => $mostRecentlyEqTmp->createdBy_id,
                'eqTemp_mobility' => $request->eq_mobility,
                'enumType_id' => $type_id,
                'specialProcess_id' => $mostRecentlyEqTmp->specialProcess_id,
            ]);

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
             $StateController->copy_state($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
         
             $RiskController= new RiskController() ; 
             $RiskController->copy_risk($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

             $PreventiveMaintenanceOperationController= new PreventiveMaintenanceOperationController() ; 
             $PreventiveMaintenanceOperationController->copy_preventiveMaintenanceOperation($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

            // In the other case, we can modify the informations without problems
        }else{

            //Update of equipment
            $equipment->update([
                'eq_internalReference' => $request->eq_internalReference,
                'eq_externalReference' => $request->eq_externalReference, 
                'eq_name' => $request->eq_name,
                'eq_serialNumber' => $request->eq_serialNumber,
                'eq_constructor' => $request->eq_constructor,
                'eq_set' => $request->eq_set,
            ]);

            //Update of equipment temp
            $mostRecentlyEqTmp->update([
                'eqTemp_validate' => $request->eq_validate,
                'enumMassUnit_id' => $massUnit_id,
                'eqTemp_mass' => $request->eq_mass,
                'eqTemp_remarks' => $request->eq_remarks,
                'eq_mobility' => $request->eq_mobility,
                'enumType_id' => $type_id,
                // NEED TO UPDATE
               /* 'qualityVerifier_id' => $mostRecentlyEqTmp->qualityVerifier_id,
                'technicalVerifier_id' => $mostRecentlyEqTmp->technicalVerifier_id,
                'createdBy_id' => $mostRecentlyEqTmp->createdBy_id,
                'specialProcess_id' => $mostRecentlyEqTmp->specialProcess_id,*/
            ]);
        }
    }

    public function send_eq_prvMtnOp_for_planning(){
        $equipments=Equipment::all() ;
        $container=array() ; 
        $containerOp=array() ;
        foreach($equipments as $equipment){
            $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $equipment->id)->orderBy('created_at', 'desc')->first();
            if ($mostRecentlyEqTmp->eqTemp_validate!=="VALIDATED"){
                $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', "=", $mostRecentlyEqTmp->id)->get() ;
                foreach( $prvMtnOps as $prvMtnOp){
                    $opMtn=([
                        "id" => $prvMtnOp->id,
                        "prvMtnOp_number" => (string)$prvMtnOp->prvMtnOp_number,
                        "prvMtnOp_description" => $prvMtnOp->prvMtnOp_description,
                        "prvMtnOp_periodicity" => (string)$prvMtnOp->prvMtnOp_periodicity,
                        "prvMtnOp_symbolPeriodicity" => $prvMtnOp->prvMtnOp_symbolPeriodicity,
                        "prvMtnOp_protocol" => $prvMtnOp->prvMtnOp_protocol,
                        "prvMtnOp_startDate" => $prvMtnOp->prvMtnOp_startDate,
                        "prvMtnOp_nextDate" => $prvMtnOp->prvMtnOp_nextDate,
                        "prvMtnOp_reformDate" => $prvMtnOp->prvMtnOp_reformDate,
                        "prvMtnOp_validate" => $prvMtnOp->prvMtnOp_validate,
                        
                    ]);
                    array_push($containerOp,$opMtn);
                }
                
                $eq = ([
                    "internalReference" => $equipment->eq_internalReference,
                    "preventive_maintenance_operations" => $containerOp,
                ]) ; 

                array_push($container,$eq);


            }
        }
        return response()->json($container) ;
    }
}





