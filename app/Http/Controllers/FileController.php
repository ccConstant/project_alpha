<?php

/*
* Filename : FileController.php 
* Creation date : 17 May 2022
* Update date : 9 Feb 2023
* This file is used to link the view files and the database that concern the file table. 
* For example : add a file for an equipment in the data base, update a file, delete it...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\SW01\EquipmentTemp ; 
use App\Models\SW01\Equipment ; 
use App\Models\SW01\MmeTemp ; 
use App\Models\SW01\Mme ; 
use App\Models\File ; 
use Carbon\Carbon;


class FileController extends Controller
{
    
    /* ---------------------------------------- GENERALS FUNCTION FOR FILES --------------------------------------------*/
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

    /* ---------------------------------------- FUNCTIONS FOR FILES LINKED TO EQUIPMENT  --------------------------------------------*/
    /**
     * Function call by EquipmentFileForm.vue when the form is submitted for insert with the route : /equipment/add/file/${id} (post)
     * Add a new enregistrement of file in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new file
     */
    public function add_file_eq(Request $request){

        $id_eq=intval($request->eq_id) ; 
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyEqTmp!=NULL){

            //Creation of a new file
            $file=File::create([
                'file_name' => $request->file_name,
                'file_location' => $request->file_location,
                'file_validate' => $request->file_validate,
                'file_validate' => $request->file_validate,
                'equipmentTemp_id' => $mostRecentlyEqTmp->id,
            ]) ; 

            $file_id=$file->id;
            
            if ($mostRecentlyEqTmp->qualityVerifier_id!=null){
                $mostRecentlyEqTmp->update([
                    'qualityVerifier_id' => NULL,
                ]);
            }
            if ($mostRecentlyEqTmp->technicalVerifier_id!=null){
                $mostRecentlyEqTmp->update([
                    'technicalVerifier_id' => NULL,
                ]);
            }
             //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for add file
            if ((boolean)$mostRecentlyEqTmp->eqTemp_lifeSheetCreated==true && $mostRecentlyEqTmp->eqTemp_validate=="validated"){
                
            //We need to increase the number of equipment temp linked to the equipment
            $version_eq=$equipment->eq_nbrVersion+1 ; 
            //Update of equipment
            $equipment->update([
                'eq_nbrVersion' =>$version_eq,
            ]);
            
            //We need to increase the version of the equipment temp (because we create a new equipment temp)
            $version =  $mostRecentlyEqTmp->eqTemp_version+1 ; 
                //update of equipment temp
            $mostRecentlyEqTmp->update([
                'eqTemp_version' => $version,
                'eqTemp_date' => Carbon::now('Europe/Paris'),
                'eqTemp_lifeSheetCreated' => false,
            ]);
            }
            return response()->json($file_id) ; 
        }
    }


     /**
     * Function call by EquipmentFileForm.vue when the form is submitted for update with the route :/equipment/update/file/{id} (post)
     * Update an enregistrement of file in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the file we want to update
     * */
    public function update_file_eq(Request $request, $id){

        $equipment=Equipment::findOrfail($request->eq_id) ; 
        //We search the most recently equipment temp of the equipment 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->latest()->first();
        if ($mostRecentlyEqTmp!=NULL){
            if ($mostRecentlyEqTmp->qualityVerifier_id!=null){
                $mostRecentlyEqTmp->update([
                    'qualityVerifier_id' => NULL,
                ]);
            }
            if ($mostRecentlyEqTmp->technicalVerifier_id!=null){
                $mostRecentlyEqTmp->update([
                    'technicalVerifier_id' => NULL,
                ]);
            }
            
            //We checked if the most recently equipment temp is validate and if a life sheet has been already created.
            //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for update file 
            if ($mostRecentlyEqTmp->eqTemp_validate=="validated" && (boolean)$mostRecentlyEqTmp->eqTemp_lifeSheetCreated==true){
            
                //We need to increase the number of equipment temp linked to the equipment
                $version_eq=$equipment->eq_nbrVersion+1 ; 
                //Update of equipment
                $equipment->update([
                    'eq_nbrVersion' =>$version_eq,
                ]);
                
               //We need to increase the version of the equipment temp (because we create a new equipment temp)
                $version =  $mostRecentlyEqTmp->eqTemp_version+1 ; 
                 //update of equipment temp
               $mostRecentlyEqTmp->update([
                'eqTemp_version' => $version,
                'eqTemp_date' => Carbon::now('Europe/Paris'),
                'eqTemp_lifeSheetCreated' => false,
               ]);
            }

            $file=File::findOrFail($id) ; 
            $file->update([
                'file_name' => $request->file_name,
                'file_location' => $request->file_location,
                'file_validate' => $request->file_validate,
            ]);
        }
    }

    /**
     * Function call by ReferenceAFile.vue with the route : /file/send/{$id} (get)
     * Get the files of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the files
     * @return \Illuminate\Http\Response
     */

    public function send_files_eq($id) {
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
    public function delete_file_eq(Request $request, $id){
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        //We search the most recently equipment temp of the equipment 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->latest()->first();
        //We checked if the most recently equipment temp is validate and if a life sheet has been already created.
        //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for update dimension
        if ($mostRecentlyEqTmp->qualityVerifier_id!=null){
            $mostRecentlyEqTmp->update([
                'qualityVerifier_id' => NULL,
            ]);
        }
        if ($mostRecentlyEqTmp->technicalVerifier_id!=null){
            $mostRecentlyEqTmp->update([
                'technicalVerifier_id' => NULL,
            ]);
        }
        
        if ($mostRecentlyEqTmp->eqTemp_validate=="validated" && (boolean)$mostRecentlyEqTmp->eqTemp_lifeSheetCreated==true){
            //We need to increase the number of equipment temp linked to the equipment
            $version_eq=$equipment->eq_nbrVersion+1 ; 
            //Update of equipment
            $equipment->update([
                'eq_nbrVersion' =>$version_eq,
            ]);

            //We need to increase the version of the equipment temp (because we create a new equipment temp)
            $version =  $mostRecentlyEqTmp->eqTemp_version+1 ; 
            //update of equipment temp
            $mostRecentlyEqTmp->update([
            'eqTemp_version' => $version,
            'eqTemp_date' => Carbon::now('Europe/Paris'),
            'eqTemp_lifeSheetCreated' => false,
            ]);
        }
        $file=File::findOrFail($id);
        $file->delete() ; 
    }


     /* ---------------------------------------- FUNCTIONS FOR FILES LINKED TO MME  --------------------------------------------*/


     /**
     * Function call by MmeFileForm.vue when the form is submitted for insert with the route : /mme/add/file/${id} (post)
     * Add a new enregistrement of file in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new file
     */
    public function add_file_mme(Request $request){

        $id_mme=intval($request->mme_id) ; 
        $mme=Mme::findOrfail($request->mme_id) ; 
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyMmeTmp!=NULL){

            //Creation of a new file
            $file=File::create([
                'file_name' => $request->file_name,
                'file_location' => $request->file_location,
                'file_validate' => $request->file_validate,
                'file_validate' => $request->file_validate,
                'mmeTemp_id' => $mostRecentlyMmeTmp->id,
            ]) ; 

            $file_id=$file->id;

            if ($mostRecentlyMmeTmp->qualityVerifier_id!=null){
                $mostRecentlyMmeTmp->update([
                    'qualityVerifier_id' => NULL,
                ]);
            }
            if ($mostRecentlyMmeTmp->technicalVerifier_id!=null){
                $mostRecentlyMmeTmp->update([
                    'technicalVerifier_id' => NULL,
                ]);
            }
            
             //If the mme temp is validated and a life sheet has been already created, we need to update the mme temp and increase it's version (that's mean another life sheet version) for add file
            if ((boolean)$mostRecentlyMmeTmp->mmeTemp_lifeSheetCreated==true && $mostRecentlyMmeTmp->mmeTemp_validate=="validated"){
                
            //We need to increase the number of mme temp linked to the mme
            $version_mme=$mme->mme_nbrVersion+1 ; 
            //Update of mme
            $mme->update([
                'mme_nbrVersion' =>$version_mme,
            ]);
            
            //We need to increase the version of the mme temp (because we create a new mme temp)
            $version =  $mostRecentlyMmeTmp->mmeTemp_version+1 ; 
                //update of mme temp
            $mostRecentlyMmeTmp->update([
                'mmeTemp_version' => $version,
                'mmeTemp_date' => Carbon::now('Europe/Paris'),
                'mmeTemp_lifeSheetCreated' => false,
            ]);
            }
            return response()->json($file_id) ; 
        }
    }


     /**
     * Function call by MmeFileForm.vue when the form is submitted for update with the route :/mme/update/file/{id} (post)
     * Update an enregistrement of file in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the file we want to update
     * */
    public function update_file_mme(Request $request, $id){

        $mme=Mme::findOrfail($request->mme_id) ; 
        //We search the most recently mme temp of the mme 
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->latest()->first();
        if ($mostRecentlyMmeTmp!=NULL){

            if ($mostRecentlyMmeTmp->qualityVerifier_id!=null){
                $mostRecentlyMmeTmp->update([
                    'qualityVerifier_id' => NULL,
                ]);
            }
            if ($mostRecentlyMmeTmp->technicalVerifier_id!=null){
                $mostRecentlyMmeTmp->update([
                    'technicalVerifier_id' => NULL,
                ]);
            }

            //We checked if the most recently mme temp is validate and if a life sheet has been already created.
            //If the mme temp is validated and a life sheet has been already created, we need to update the mme temp and increase it's version (that's mean another life sheet version) for update file 
        
            if ($mostRecentlyMmeTmp->mmeTemp_validate=="validated" && (boolean)$mostRecentlyMmeTmp->mmeTemp_lifeSheetCreated==true){
            
                //We need to increase the number of mme temp linked to the mme
                $version_mme=$mme->mme_nbrVersion+1 ; 
                //Update of mme
                $mme->update([
                    'mme_nbrVersion' =>$version_mme,
                ]);
                
                 //update of mme temp
               $mostRecentlyMmeTmp->update([
                'mmeTemp_version' => $version_mme,
                'mmeTemp_date' => Carbon::now('Europe/Paris'),
                'mmeTemp_lifeSheetCreated' => false,
               ]);
            }

            $file=File::findOrFail($id) ; 
            $file->update([
                'file_name' => $request->file_name,
                'file_location' => $request->file_location,
                'file_validate' => $request->file_validate,
            ]);
        }
    }

    /**
     * Function call by ReferenceAFile.vue with the route : /file/send/{$id} (get)
     * Get the files of the mme whose id is passed in parameter
     * The id parameter corresponds to the id of the mme from which we want the files
     * @return \Illuminate\Http\Response
     */

    public function send_files_mme($id) {
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $files = File::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->get();
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
     * Function call by MmeFileForm.vue when we want to delete a file with the route : /mme/delete/file{id}(post)
     * Delete a file thanks to the id given in parameter
     * The id parameter correspond to the id of the file we want to delete
     * */
    public function delete_file_mme(Request $request, $id){
        $mme=Mme::findOrfail($request->mme_id) ; 
        //We search the most recently mme temp of the mme 
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->latest()->first();

        if ($mostRecentlyMmeTmp->qualityVerifier_id!=null){
            $mostRecentlyMmeTmp->update([
                'qualityVerifier_id' => NULL,
            ]);
        }
        if ($mostRecentlyMmeTmp->technicalVerifier_id!=null){
            $mostRecentlyMmeTmp->update([
                'technicalVerifier_id' => NULL,
            ]);
        }
        //We checked if the most recently mme temp is validate and if a life sheet has been already created.
        //If the mme temp is validated and a life sheet has been already created, we need to update the mme temp and increase it's version (that's mean another life sheet version) for update dimension
        if ($mostRecentlyMmeTmp->mmeTemp_validate=="validated" && (boolean)$mostRecentlyMmeTmp->mmeTemp_lifeSheetCreated==true){
            //We need to increase the number of mme temp linked to the mme
            $version_mme=$mme->mme_nbrVersion+1 ; 
            //Update of mme
            $mme->update([
                'mme_nbrVersion' =>$version_mme,
            ]);

            //We need to increase the version of the mme temp (because we create a new mme temp)
            $version =  $mostRecentlyMmeTmp->mmeTemp_version+1 ; 
            //update of mme temp
            $mostRecentlyMmeTmp->update([
            'mmeTemp_version' => $version,
            'mmeTemp_date' => Carbon::now('Europe/Paris'),
            'mmeTemp_lifeSheetCreated' => false,
            ]);
        }
        $file=File::findOrFail($id);
        $file->delete() ; 
    }
}
