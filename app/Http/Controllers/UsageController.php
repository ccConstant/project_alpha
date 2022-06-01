<?php

/*
* Filename : UsageController.php 
* Creation date : 17 May 2022
* Update date : 19 May 2022
* This file is used to link the view files and the database that concern the usage table. 
* For example : add a usage for an equipment in the data base, update a file, delete it...
*/ 



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EquipmentTemp ; 
use App\Models\Equipment ; 
use App\Http\Controllers\PowerController ; 
use App\Http\Controllers\DimensionController ; 
use App\Http\Controllers\FileController ; 
use App\Http\Controllers\UsageController ; 
use App\Http\Controllers\StateController ; 
use App\Http\Controllers\RiskController ; 
use App\Models\Usage ;
use App\Http\Controllers\SpecialProcessController ; 
use App\Http\Controllers\PreventiveMaintenanceOperationController ; 
use Carbon\Carbon;



class UsageController extends Controller
{

      /**
     * Function call by DimensionController (and more) when we need to copy links between equipment temp and usage
     * Copy the links between a equipment temp and a usage to the new equipment temp
     * * The actualId parameter correspond of the id of the equipment from which we want to copy the usages
     * The newId parameter correspond of the id of the equipment where we want to copy the usages
     * The idNotCopy parameter correspond of the id of the usage we don't have to copy 
     * */
    public function copy_usage($actualId, $newId, $idNotCopy){
        $actualEqTemp= EquipmentTemp::findOrFail($actualId) ; 
        $newEqTemp= EquipmentTemp::findOrFail($newId) ; 
        $usages=Usage::where('equipmentTemp_id', '=', $actualId)->get();
        foreach($usages as $usage){
            if ($usage->id!=$idNotCopy){
                //Creation of a new usage
                $newUsage=Usage::create([
                    'usg_type' => $usage->usg_type,
                    'usg_validate' => $usage->usg_validate,
                    'usg_precaution' => $usage->usg_precaution,
                    'usg_startDate' => $usage->usg_startDate,
                    'equipmentTemp_id' => $newId,
                ]) ;
            }
        }
    }


    /**
     * Function call by EquipmentUsgForm.vue when the form is submitted for check data with the route :/usage/verif''(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_usage(Request $request){

        //-----CASE usg->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his usage, so he must enter all the attributes
        if ($request->usg_validate=='validated'){
            $this->validate(
                $request,
                [
                    'usg_type' => 'required|min:3',
                    'usg_precaution' => 'required|min:3',
                ],
                [
                    'usg_type.required' => 'You must enter a type for your usage ',
                    'usg_type.min' => 'You must enter at least three characters ',
                    'usg_precaution.required' => 'You must enter a precaution for your usage ',
                    'usg_precaution.min' => 'You must enter at least three characters ',

                
                ]
            );
        }else{
             //-----CASE usg->validate=drafted or usg->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations 
            $this->validate(
                $request,
                [
                    'usg_type' => 'required|min:3',
                ],
                [
                    'usg_type.required' => 'You must enter a type for your usage ',
                    'usg_type.min' => 'You must enter at least three characters ',
                ]
            );
        }
    }


    /**
     * Function call by EquipmentUsgForm.vue when the form is submitted for insert with the route : /equipment/add/usg/${id} (post)
     * Add a new enregistrement of usage in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new usage
     */
    public function add_usage(Request $request){
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();

        //Creation of a new usage
        $usage=Usage::create([
            'usg_type' => $request->usg_type,
            'usg_validate' => $request->usg_validate,
            'usg_precaution' => $request->usg_precaution,
            'usg_startDate' => Carbon::now('Europe/Paris'),
            'equipmentTemp_id' => $mostRecentlyEqTmp->id,
        ]) ;

        $usg_id=$usage->id;
        $id_eq=intval($request->eq_id) ; 
        if ($mostRecentlyEqTmp!=NULL){
             //If the equipment temp is validated and a life sheet has been already created, we need to create another equipment temp (that's mean another life sheet version) for add usage
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

               $usage->update([
                   'equipmentTemp_id' => $new_eqTemp->id,
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
              $UsageController->copy_usage($mostRecentlyEqTmp->id, $new_eqTemp->id, $usg_id) ; 

              $StateController= new StateController() ; 
              $StateController->copy_state($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
          
              $RiskController= new RiskController() ; 
              $RiskController->copy_risk($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

              $PreventiveMaintenanceOperationController= new PreventiveMaintenanceOperationController() ; 
              $PreventiveMaintenanceOperationController->copy_preventiveMaintenanceOperation($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
             // In the other case, we can add informations without problems
            }
            return response()->json($usg_id) ; 
        }
    }

    /**
     * Function call by EquipmentUsgForm.vue when the form is submitted for update with the route :/equipment/update/usg/{id} (post)
     * Update an enregistrement of usage in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the usage we want to update
     * */
    public function update_usage(Request $request, $id){

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
                
                 //Creation of a new usage
                $usage=Usage::create([
                    'usg_type' => $request->usg_type,
                    'usg_validate' => $request->usg_validate,
                    'usg_precaution' => $request->usg_precaution,
                    'usg_startDate' => Carbon::now('Europe/Paris'),
                    'equipmentTemp_id' => $new_eqTemp->id,
                ]) ;
                    
                
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
               $UsageController->copy_usage($mostRecentlyEqTmp->id, $new_eqTemp->id, $id) ; 

               $StateController= new StateController() ; 
               $StateController->copy_state($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
           
               $RiskController= new RiskController() ; 
               $RiskController->copy_risk($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $PreventiveMaintenanceOperationController= new PreventiveMaintenanceOperationController() ; 
               $PreventiveMaintenanceOperationController->copy_preventiveMaintenanceOperation($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
            
            

                // In the other case, we can modify the informations without problems
            }else{

                $usage=Usage::findOrFail($id) ; 
                $usage->update([
                    'usg_type' => $request->usg_type,
                    'usg_validate' => $request->usg_validate,
                    'usg_precaution' => $request->usg_precaution,
                    'usg_startDate' => Carbon::now('Europe/Paris'),
                ]);
            }
        }
    }

    /**
     * Function call by ReferenceAUsg.vue with the route : /usage/send/{$id} (get)
     * Get the usages of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the usages
     * @return \Illuminate\Http\Response
     */

    public function send_usages($id) {
        $container=array() ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $usages=Usage::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get();
        foreach ($usages as $usage) {
            $dates=explode("-", $usage->usg_startDate)  ;
            $year=$dates[0] ; 
            $month="" ; 
            switch ($dates[1]){
                case "01" : case "1" : 
                    $month="JAN" ; 
                    break ; 
                case "02" : case "2" : 
                    $month="FEB" ; 
                    break ; 
                case "03" : case "3" : 
                    $month="MAR" ; 
                    break ; 
                case "04" : case "4" : 
                    $month="APR" ; 
                    break ; 
                case "05" : case "5" : 
                    $month="MAY" ; 
                    break ; 
                case "06" : case "6" : 
                    $month="JUN" ; 
                    break ; 
                case "07" : case "7" : 
                    $month="JUL" ; 
                    break ; 
                case "08" : case "8" : 
                    $month="AUG" ; 
                    break ; 
                case "09" : case "9" : 
                    $month="SEP" ; 
                    break ; 
                case "10" : 
                    $month="OCT" ; 
                    break ; 
                case "11" : 
                    $month="NOV" ; 
                    break ; 
                case "12" : 
                    $month="DEC" ; 
                    break ; 

            }
            $day=$dates[2] ;
            $newDate=$day." ".$month." ".$year ; 

            $obj=([
                'id' => $usage->id,
                'usg_type' => $usage->usg_type,
                'usg_validate' => $usage->usg_validate,
                'usg_precaution' => $usage->usg_precaution,
                'usg_startDate' => $newDate,
                'usg_reformDate' => $usage->usg_reformDate,
                
            ]);
            array_push($container,$obj);
        }
        return response()->json( $container) ;
    }


    /**
     * Function call by EquipmentUsgForm.vue when we want to delete a usage with the route : /equipment/delete/usg{id}(post)
     * Delete a usage thanks to the id given in parameter
     * The id parameter correspond to the id of the usage we want to delete
     * */
    public function delete_usage($id){
        $usage=Usage::findOrFail($id);
        $usage->delete() ; 
    }

}


   
