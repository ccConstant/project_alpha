<?php

/*
* Filename : PowerController.php 
* Creation date : 17 May 2022
* Update date : 17 May 2022
* This file is used to link the view files and the database that concern the power table. 
* For example : add a power for an equipment in the data base, update a power, delete it...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EquipmentTemp ; 
use App\Models\EnumPowerType ; 
use App\Models\Power ; 
use App\Models\Equipment ; 
use App\Http\Controllers\DimensionController ; 
use App\Http\Controllers\FileController ; 
use App\Http\Controllers\UsageController ; 
use App\Http\Controllers\StateController ; 
use App\Http\Controllers\RiskController ; 
use App\Http\Controllers\SpecialProcessController ; 
use App\Http\Controllers\PreventiveMaintenanceOperationController ; 
use Carbon\Carbon;



class PowerController extends Controller
{


     /**
     * Function call by EquipmentPowForm.vue with the route : /power/names (get)
    * Get the fields of the power names to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */
    public function send_names (){
        $names=DB::select(DB::raw('SELECT DISTINCT pow_name FROM powers'));
        return response()->json($names) ; 
    }

    /**
     * Function call by EquipmentPowForm.vue when the form is submitted for check data with the route : /equipment/add/power/${id}  (post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_power(Request $request){

        //-----CASE pow->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his power, so he must enter all the attributes
        if ($request->pow_validate=='validated'){
            $this->validate(
                $request,
                [
                    'pow_name' => 'required|min:1|max:25',
                    'pow_value' => 'required|min:1|max:25',
                    'pow_unit' => 'required|min:1|max:25',
                    'pow_consumptionValue' => 'required|min:1|max:25',
                    'pow_consumptionUnit' => 'required|min:1|max:25',

                ],
                [
                    'pow_name.required' => 'You must enter a name for the power ',
                    'pow_name.min' => 'You must enter at least one characters ',
                    'pow_name.max' => 'You must enter a maximum of 25 characters',
                    'pow_value.required' => 'You must enter a value for the power ',
                    'pow_value.min' => 'You must enter at least one character ',
                    'pow_value.max' => 'You must enter a maximum of 25 characters',
                    'pow_unit.required' => 'You must enter a unit for the power ',
                    'pow_unit.min' => 'You must enter at least one character ',
                    'pow_unit.max' => 'You must enter a maximum of 25 characters',
                    'pow_consumptionValue.required' => 'You must enter a value for the consuption of the power ',
                    'pow_consumptionValue.min' => 'You must enter at least 1 characters ',
                    'pow_consumptionValue.max' => 'You must enter a maximum of 25 characters',
                    'pow_consumptionUnit.required' => 'You must enter a unit for the consumption of the power ',
                    'pow_consumptionUnit.min' => 'You must enter at least one character ',
                    'pow_consumptionUnit.max' => 'You must enter a maximum of 25 characters',
                
                ]
            );

            //verification about pow_type, if no one value is selected we need to alert the user
            if ($request->pow_type=='' || $request->pow_type==NULL ){
                return response()->json([
                    'errors' => [
                        'pow_type' => ["You must choose a type for your power"]
                    ]
                ], 429);
            }

        }else{
             //-----CASE pow->validate=drafted or pow->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations 
            $this->validate(
                $request,
                [
                    'pow_name' => 'required|min:3|max:50',
                ],
                [
                    'pow_name.required' => 'You must enter a name for the power ',
                    'pow_name.min' => 'You must enter at least three characters ',
                    'pow_name.max' => 'You must enter a maximum of 50 characters',

                
                ]
            );
        }

    }

    /**
     * Function call by EquipmentPowForm.vue when the form is submitted for insert with the route : /equipment/add/pow/${id} (post)
     * Add a new enregistrement of power in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : the id of the new power
     */
    public function add_power(Request $request){

       //A power is linked to its type. So we need to find the id of the type choosen by the user and write it in the attribute of the power.
        //But if no one type is choosen by the user we define this id to NULL
        // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $type_id=NULL ; 
       if ($request->pow_type!='' && $request->pow_type!=NULL){
            $type= EnumPowerType::where('value', '=', $request->pow_type)->first() ;
            $type_id=$type->id ; 
        }

        $equipment=Equipment::findOrfail($request->eq_id) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        
        //Creation of a new power
        $power=Power::create([
            'pow_name' => $request->pow_name,
            'pow_value' => $request->pow_value,
            'pow_unit' => $request->pow_unit,
            'pow_consumptionValue' => $request->pow_consumptionValue,
            'pow_consumptionUnit' => $request->pow_consumptionUnit,
            'pow_validate' => $request->pow_validate,
            'enumPowerType_id' => $type_id,
            'equipmentTemp_id' => $mostRecentlyEqTmp->id,
        ]) ; 

        $power_id=$power->id;
        $id_eq=intval($request->eq_id) ; 
        if ($mostRecentlyEqTmp!=NULL){
             //If the equipment temp is validated and a life sheet has been already created, we need to create another equipment temp (that's mean another life sheet version) for add power
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

               $power->update([
                'equipmentTemp_id' => $new_eqTemp->id,
               ]) ;
               
               //We copy the links of the actual Equipment temp to the new equipment temp 
                $DimController= new DimensionController() ; 
                $DimController->copy_dimension($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

                $SpProcController= new SpecialProcessController() ; 
                $SpProcController->copy_specialProcess($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
 
                $PowerController= new PowerController() ; 
                $PowerController->copy_power($mostRecentlyEqTmp->id, $new_eqTemp->id, $power_id) ; 
 
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
             // In the other case, we can add informations without problems
            }
            return response()->json($power_id) ; 
        }
    }


    /**
     * Function call by EquipmentPowForm.vue when the form is submitted for update with the route : /equipment/update/pow/{id} (post)
     * Update an enregistrement of power in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the power we want to update
     * */
    public function update_power(Request $request, $id){

        //A power is linked to its type. So we need to find the id of the type choosen by the user and write it in the attribute of the power.
        //But if no one type is choosen by the user we define this id to NULL
        // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $type_id=NULL ; 
        if ($request->pow_type!='' && $request->pow_type!=NULL){
            $type= EnumPowerType::where('value', '=', $request->pow_type)->first() ;
            $type_id=$type->id ; 
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
                
                //Creation of a new power
                $power=Power::create([
                    'pow_name' => $request->pow_name,
                    'pow_value' => $request->pow_value,
                    'pow_unit' => $request->pow_unit,
                    'pow_consumptionValue' => $request->pow_consumptionValue,
                    'pow_consumptionUnit' => $request->pow_consumptionUnit,
                    'pow_validate' => $request->pow_validate,
                    'enumPowerType_id' => $type_id,
                    'equipmentTemp_id' => $new_eqTemp,
                ]) ; 
                    
                //Dédoubler les liens de eqTemps 
                $DimController= new DimensionController() ; 
                $DimController->copy_dimension($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

                $SpProcController= new SpecialProcessController() ; 
                $SpProcController->copy_specialProcess($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
        
               $PowerController= new PowerController() ; 
               $PowerController->copy_power($mostRecentlyEqTmp->id, $new_eqTemp->id, $id) ; 

               $FileController= new FileController() ; 
               $FileController->copy_file($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $UsageController= new UsageController() ; 
               $UsageController->copy_usage($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $StateController= new StateController() ; 
               $StateController->copy_state($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
           
               $RiskController= new RiskController() ; 
               $RiskController->copy_risk_eqTemp($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $PreventiveMaintenanceOperationController= new PreventiveMaintenanceOperationController() ; 
               $PreventiveMaintenanceOperationController->copy_preventiveMaintenanceOperation($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

                // In the other case, we can modify the informations without problems
            }else{

                $power=Power::findOrFail($id) ; 
                $power->update([
                    'pow_name' => $request->pow_name,
                    'pow_value' => $request->pow_value,
                    'pow_unit' => $request->pow_unit,
                    'pow_consumptionValue' => $request->pow_consumptionValue,
                    'pow_consumptionUnit' => $request->pow_consumptionUnit,
                    'pow_validate' => $request->pow_validate,
                    'enumPowerType_id' => $type_id,
                ]) ; 
            }
        }
    }

    /**
     * Function call by ReferenceAPow.vue with the route : /power/send/{$id} (get)
     * Get the powers of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the powers
     * @return \Illuminate\Http\Response
     */

    public function send_powers($id) {
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $container=array() ; 
        $powers= $powers=Power::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ;
        foreach ($powers as $power) {
            $type = NULL ; 
            if ($power->enumPowerType_id!=NULL){
                $type = $power->enumPowerType->value ;
            }
            $obj=([
                'id' => $power->id,
                'pow_name' => $power->pow_name,
                'pow_value' => (string)$power->pow_value,
                'pow_unit' => $power->pow_unit,
                'pow_consumptionValue' => (string)$power->pow_consumptionValue,
                'pow_consumptionUnit' => $power->pow_consumptionUnit,
                'pow_validate' => $power->pow_validate,
                'pow_type' => $type,
            ]);
            array_push($container,$obj);
        }
        return response()->json($container) ;
    }





     /**
     * Function call by DimensionController (and more) when we need to copy links between equipment temp and power
     * Copy the links between a equipment temp and a power to the new equipment temp
     * The actualId parameter correspond of the id of the equipment from which we want to copy the powers
     * The newId parameter correspond of the id of the equipment where we want to copy the powers
     * The idNotCopy parameter correspond of the id of the power we don't have to copy 
     * */
    public function copy_power($actualId, $newId, $idNotCopy){
        $actualEqTemp= EquipmentTemp::findOrFail($actualId) ; 
        $newEqTemp= EquipmentTemp::findOrFail($newId) ; 
        $powers=Power::where('equipmentTemp_id', '=', $actualId)->get() ;
        foreach($powers as $power){
            if ($power->id!=$idNotCopy){
                //Creation of a new power
                $newPower=Power::create([
                    'pow_name' => $power->pow_name,
                    'pow_value' => $power->pow_value,
                    'pow_unit' => $power->pow_unit,
                    'pow_consumptionValue' => $power->pow_consumptionValue,
                    'pow_consumptionUnit' => $power->pow_consumptionUnit,
                    'pow_validate' => $power->pow_validate,
                    'enumPowerType_id' => $power->enumPowerType_id,
                    'equipmentTemp_id' => $newId,
                ]) ; 
            }
        }
    }


     /**
     * Function call by EquipmentPowForm.vue when we want to delete a power with the route : /equipment/delete/pow{id} (post)
     * Delete a power thanks to the id given in parameter
     * The id parameter correspond to the id of the power we want to delete
     * */
    public function delete_power($id){
        $power=Power::findOrFail($id);
        $power->delete() ; 
    }


}