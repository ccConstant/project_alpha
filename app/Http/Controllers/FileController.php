<?php

/*
* Filename : FileController.php 
* Creation date : 17 May 2022
* Update date : 17 May 2022
* This file is used to link the view files and the database that concern the file table. 
* For example : add a file for an equipment in the data base, update a file, delete it...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EquipmentTemp ; 
use App\Models\Equipment ; 
use App\Models\File ; 
use App\Http\Controllers\PowerController ; 
use App\Http\Controllers\FileController ; 
use App\Http\Controllers\UsageController ; 
use App\Http\Controllers\StateController ; 
use App\Http\Controllers\RiskController ; 
use App\Http\Controllers\SpecialProcessController ; 
use App\Http\Controllers\PreventiveMaintenanceOperationController ; 
use Carbon\Carbon;


class FileController extends Controller
{
    /**
     * Function call by DimensionController (and more) when we need to copy links between equipment temp and file
     * Copy the links between a equipment temp and a file to the new equipment temp
     * The actualId parameter correspond of the id of the equipment from which we want to copy the files
     * The newId parameter correspond of the id of the equipment where we want to copy the files
     * The idNotCopy parameter correspond of the id of the file we don't have to copy 
     * */
    public function copy_file($actualId, $newId, $idNotCopy){
        $actualEqTemp= EquipmentTemp::findOrFail($actualId) ; 
        $newEqTemp= EquipmentTemp::findOrFail($newId) ; 
        $files = File::where('equipmentTemp_id', '=', $actualId)->get();
        foreach($files as $file){
            if($file->id!=$idNotCopy){
                //Creation of a new file
                $newFile=File::create([
                    'file_name' => $file->file_name,
                    'file_location' => $file->file_location,
                    'file_validate' => $file->file_validate,
                    'equipmentTemp_id' => $newId,
                ]) ; 
            }
        }
    }


    /**
     * Function call by EquipmentFileForm.vue when the form is submitted for check data with the route : /file/verif''(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_file(Request $request){

        //-----CASE file->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his file, so he must enter all the attributes
        if ($request->file_validate=='validated'){
            $this->validate(
                $request,
                [
                    'file_name' => 'required|min:3|max:50',
                    'file_location' => 'required|min:3|max:255',
                ],
                [
                    'file_name.required' => 'You must enter a name for your file ',
                    'file_name.min' => 'You must enter at least three characters ',
                    'file_name.max' => 'You must enter a maximum of 50 characters',
                    'file_location.required' => 'You must enter a location for your file ',
                    'file_location.min' => 'You must enter at least three characters ',
                    'file_location.max' => 'You must enter a maximum of 255 characters',

                
                ]
            );

           
        }else{
             //-----CASE file->validate=drafted or file->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations 
            $this->validate(
                $request,
                [
                    'file_name' => 'required|min:3|max:50',
                    'file_location' => 'max:255',
                ],
                [
                    'file_name.required' => 'You must enter a name for your file ',
                    'file_name.min' => 'You must enter at least three characters ',
                    'file_name.max' => 'You must enter a maximum of 50 characters',
                    'file_location.max' => 'You must enter a maximum of 255 characters',
                ]
            );
        }
    }


    /**
     * Function call by EquipmentFileForm.vue when the form is submitted for insert with the route : /equipment/add/file/${id} (post)
     * Add a new enregistrement of file in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new file
     */
    public function add_file(Request $request){

        $id_eq=intval($request->eq_id) ; 
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();

        //Creation of a new file
        $file=File::create([
            'file_name' => $request->file_name,
            'file_location' => $request->file_location,
            'file_validate' => $request->file_validate,
            'file_validate' => $request->file_validate,
            'equipmentTemp_id' => $mostRecentlyEqTmp->id,
        ]) ; 

        $file_id=$file->id;


        if ($mostRecentlyEqTmp!=NULL){
             //If the equipment temp is validated and a life sheet has been already created, we need to create another equipment temp (that's mean another life sheet version) for add file
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

               $file->update([
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
               $FileController->copy_file($mostRecentlyEqTmp->id, $new_eqTemp->id, $file_id) ; 

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
            return response()->json($file_id) ; 
        }
    }


     /**
     * Function call by EquipmentFileForm.vue when the form is submitted for update with the route :/equipment/update/file/{id} (post)
     * Update an enregistrement of file in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the file we want to update
     * */
    public function update_file(Request $request, $id){

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
                
                
                //Creation of a new file
                $file=File::create([
                    'file_name' => $request->file_name,
                    'file_location' => $request->file_location,
                    'file_validate' => $request->file_validate,
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
               $FileController->copy_file($mostRecentlyEqTmp->id, $new_eqTemp->id, $id) ; 

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

                $file=File::findOrFail($id) ; 
                $file->update([
                    'file_name' => $request->file_name,
                    'file_location' => $request->file_location,
                    'file_validate' => $request->file_validate,
                ]);
            }
        }
    }

    /**
     * Function call by ReferenceAFile.vue with the route : /file/send/{$id} (get)
     * Get the files of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the files
     * @return \Illuminate\Http\Response
     */

    public function send_files($id) {
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $files = File::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get();
        $container=array() ; 
        foreach ($files as $file) {
            $obj=([
                "id" => $file->id,
                "file_name" => $file->file_name,
                "file_location" => $file->file_location,
                "file_validate" => $file->file_validate,
            ]);
            array_push($container,$obj);
        }
        return response()->json($files) ;
    }

    /**
     * Function call by EquipmentFileForm.vue when we want to delete a file with the route : /equipment/delete/file{id}(post)
     * Delete a file thanks to the id given in parameter
     * The id parameter correspond to the id of the file we want to delete
     * */
    public function delete_file($id){
        $file=File::findOrFail($id);
        $file->delete() ; 
    }
}
