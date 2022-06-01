<?php

/*
* Filename : SpecialProcessController.php 
* Creation date : 18 May 2022
* Update date : 18 May 2022
* This file is used to link the view files and the database that concern the specialProcess table. 
* For example : add a specialProcess for an equipment, update a specialProcess, import it, delete it...
*/ 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\SpecialProcess ; 
use App\Models\EquipmentTemp ; 
use App\Models\Equipment ; 
use App\Http\Controllers\PowerController ; 
use App\Http\Controllers\DimensionController ; 
use App\Http\Controllers\FileController ; 
use App\Http\Controllers\UsageController ; 
use App\Http\Controllers\StateController ; 
use App\Http\Controllers\RiskController ; 
use App\Http\Controllers\PreventiveMaintenanceOperationController ; 
use Carbon\Carbon;

class SpecialProcessController extends Controller
{

    /**
     * Function call by EquipmentSpecProcForm.vue when the form is submitted for check data with the route : /equipment/add/spProc' (post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_specialProcess(Request $request){

        //The most important attribute in this table is "exist" : it allow to know if the equipment has any special process
       // So, whatever the special process is validated or not we need this attribute.
       //So, we begin to check if the user entered it.
        if ($request->spProc_exist!==NULL){
            //Next, we treat the case where exist=true and the special process is validated : we need the name and the remarks or precaution
            //If the special process isn't validated, the user don't must entered the name and the remarks now (he can later)
            //If no special process exist, we don't need a name or a remark
            if ($request->spProc_validate=='validated' && $request->spProc_exist){
                $this->validate(
                    $request,
                    [
                        'spProc_remarksOrPrecaution' => 'required|min:3',
                        'spProc_name' => 'required|min:1|max:50',
                    ],
                    [
                        'spProc_remarksOrPrecaution.required' => 'You must enter a remark or a precaution for your special process',
                        'spProc_remarksOrPrecaution.min' =>'You must enter at least three characters ',
                        'spProc_name.required' => 'You must enter a name for your special process',
                        'spProc_name.min' =>'You must enter at least one character ',
                        'spProc_name.max' => 'You must enter a maximum of 50 characters',
                    ]
                );
            }
            //If the exist isn't entered, we need to explain the problem to the user and send him a error
        }else{
            return response()->json([
                'errors' => [
                    'spProc_exist' => ["You have to tell is any special process exist for this equipment"]
                ]
            ], 429);
        }
    }


    /**
     * Function call by EquipmentSpecProcForm.vue when the form is submitted for insert with the route : /equipment/add/spProc(post)
     * Add a new enregistrement of specialProcess in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : the id of the new special process
     */
    public function add_specialProcess(Request $request){
        
        //Creation of a new specialProcess
        $spProc=SpecialProcess::create([
            'spProc_exist' => $request->spProc_exist,
            'spProc_remarksOrPrecaution' => $request->spProc_remarksOrPrecaution,
            'spProc_name' => $request->spProc_name,
            'spProc_validate' => $request->spProc_validate,
        ]) ; 

        $spProc_id=$spProc->id;
        $id_eq=intval($request->eq_id) ; 
        $equipment=Equipment::findOrfail($id_eq) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyEqTmp!=NULL){
             //If the equipment temp is validated and a life sheet has been already created, we need to create another equipment temp (that's mean another life sheet version) for add specialProcess
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
                   
               //We copy the links of the actual Equipment temp to the new equipment temp 
               $DimController= new DimensionController() ; 
               $DimController->copy_dimension($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $SpProcController= new SpecialProcessController() ; 
               $SpProcController->copy_specialProcess($mostRecentlyEqTmp->id, $new_eqTemp->id, $spProc_id) ; 

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

             // In the other case, we can add informations without problems
            }else{

                //Update of equipment temp
                $mostRecentlyEqTmp->update([
                    'specialProcess_id' => $spProc_id,
                ]);
            }
            return response()->json($spProc_id) ; 
        }
    }


    /**
     * Function call by EquipmentSpecProcForm.vue when the form is submitted for update with the route :/equipment/update/spProc/{id} (post)
     * Update an enregistrement of special processn in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the specialProcess we want to update
     * */
    public function update_specialProcess(Request $request, $id){
        
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

                //Creation of a new specialProcess
                $spProc=SpecialProcess::create([
                    'spProc_exist' => $request->spProc_exist,
                    'spProc_remarksOrPrecaution' => $request->spProc_remarksOrPrecaution,
                    'spProc_name' => $request->spProc_name,
                    'spProc_validate' => $request->spProc_validate,
                ]) ; 

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
                

                //We copy the links of the actual Equipment temp to the new equipment temp 
               $DimController= new DimensionController() ; 
               $DimController->copy_dimension($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $SpProcController= new SpecialProcessController() ; 
               $SpProcController->copy_specialProcess($mostRecentlyEqTmp->id, $new_eqTemp->id, $id) ; 

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
                
                $spProc=SpecialProcess::findOrFail($id) ; 
                $spProc->update([
                    'spProc_exist' => $request->spProc_exist,
                    'spProc_remarksOrPrecaution' => $request->spProc_remarksOrPrecaution,
                    'spProc_name' => $request->spProc_name,
                    'spProc_validate' => $request->spProc_validate,
                ]);
            }
        }
    }

    /**
     * Function call by ReferenceASpecProc.vue with the route : '/spProc/send/{id}' (get)
     * Get the special process of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the specialProcess
     * @return \Illuminate\Http\Response
     */

    public function send_specialProcess($id) {
        $container=array() ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyEqTmp->specialProcess_id!=NULL){
            $spProc_id=$mostRecentlyEqTmp->specialProcess_id ; 
            $spProc=SpecialProcess::findOrFail($spProc_id) ; 
            $obj=([
                "id" => $spProc_id,
                'spProc_exist' => (boolean)$spProc->spProc_exist,
                'spProc_remarksOrPrecaution' => $spProc->spProc_remarksOrPrecaution,
                'spProc_name' => $spProc->spProc_name,
                'spProc_validate' => $spProc->spProc_validate,
            ]);
            array_push($container,$obj);
        return response()->json($container) ;
        }
    }


      /**
     * Function call by DimensionController (and more) when we need to copy links between equipment temp and special process
     * Copy the links between a equipment temp and a special process to the new equipment temp
     * The actualId parameter correspond of the id of the equipment from which we want to copy the states
     * The newId parameter correspond of the id of the equipment where we want to copy the special processes
     * The idNotCopy parameter correspond of the id of the special process we don't have to copy 
     * */
    public function copy_specialProcess($actualId, $newId, $idNotCopy){ 
        $actualEqTemp= EquipmentTemp::findOrFail($actualId) ; 
        $newEqTemp= EquipmentTemp::findOrFail($newId) ; 
        $specialProcess=SpecialProcess::findOFail($actualEqTemp->specialProcess_id) ; 
        if ($specialProcess->id!=$idNotCopy){
            ///Creation of a new specialProcess
            $spProc=SpecialProcess::create([
                'spProc_exist' => $specialProcess->spProc_exist,
                'spProc_remarksOrPrecaution' => $specialProcess->spProc_remarksOrPrecaution,
                'spProc_name' => $specialProcess->spProc_name,
                'spProc_validate' => $specialProcess->spProc_validate,
            ]) ; 

            $new_eqTemp->update([
                'specialProcess_id' => $spProc->id,
            ]) ; 
        }
    }
}


    

