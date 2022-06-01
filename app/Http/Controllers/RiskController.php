<?php

/*
* Filename : RiskController.php 
* Creation date : 17 May 2022
* Update date : 23 May 2022
* This file is used to link the view files and the database that concern the risk table. 
* For example : add a risk for an equipment in the data base, update it, delete it...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EquipmentTemp ; 
use App\Models\Risk ; 
use App\Models\Equipment ; 
use App\Models\EnumRiskFor; 
use App\Models\PreventiveMaintenanceOperation ; 
use App\Http\Controllers\PowerController ; 
use App\Http\Controllers\FileController ; 
use App\Http\Controllers\UsageController ; 
use App\Http\Controllers\StateController ; 
use App\Http\Controllers\DimensionController ; 
use App\Http\Controllers\PreventiveMaintenanceOperationController ; 
use App\Http\Controllers\SpecialProcessController ; 
use Carbon\Carbon;


class RiskController extends Controller
{

    /*************************************************** TREATMENTS FOR AN EQUIPMENT ***************************************************\
     

     /**
     * Function call by DimensionController (and more) when we need to copy links between equipment temp and a risk
     * Copy the links between a equipment temp and a risk to the new equipment temp
     * The actualId parameter correspond of the id of the equipment from which we want to copy the risk
     * The newId parameter correspond of the id of the equipment where we want to copy the risk
     * The idNotCopy parameter correspond of the id of the risk we don't have to copy 
     * */
    public function copy_risk_eqTemp($actualId, $newId, $idNotCopy){   
        $risks = Risk::where('equipmentTemp_id', '=', $actualId)->get();
        foreach($risks as $risk){
            if ($risk->id!=$idNotCopy){
                //Creation of a new risk
                $newRisk=Risk::create([
                    'risk_remarks' => $risk->risk_remarks,
                    'risk_wayOfControl' => $risk->risk_wayOfControl,
                    'risk_validate' => $risk->risk_validate,
                    'enumRiskFor_id'=> $risk-> enumRiskFor_id,
                    'equipmentTemp_id' =>$newId,
                ]) ; 
                
            }
        }
    }

    /**
     * Function call by EquipmentRiskForm.vue when the form is submitted for insert with the route : /equipment/add/risk/ (post)
     * Add a new enregistrement of risk in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new risk
     */
    public function add_risk_eqTemp(Request $request){

        //A risk is linked to its target. So we need to find the id of the type choosen by the user and write it in the attribute of the risk.
         //But if no one type is choosen by the user we define this id to NULL
         // And if the type choosen is find in the data base the NULL value will be replace by the id value
         $target_id=NULL ; 
        if ($request->risk_for!='' && $request->risk_for!=NULL){
             $target= EnumRiskFor::where('value', '=', $request->risk_for)->first() ;
             $target_id=$target->id ; 
         }

         //Creation of a new risk
         $risk=Risk::create([
            'risk_remarks' => $request->risk_remarks,
            'risk_wayOfControl' => $request->risk_wayOfControl,
            'risk_validate' => $request->risk_validate,
            'enumRiskFor_id'=> $target_id,
        ]) ;
            
         $risk_id=$risk->id;
         $id_eq=intval($request->eq_id) ; 
         $equipment=Equipment::findOrfail($request->eq_id) ; 
         $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
         if ($mostRecentlyEqTmp!=NULL){
              //If the equipment temp is validated and a life sheet has been already created, we need to create another equipment temp (that's mean another life sheet version) for add risk
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
 
                $risk->update([
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
                 $UsageController->copy_usage($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
  
                 $StateController= new StateController() ; 
                 $StateController->copy_state($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
             
                 $RiskController= new RiskController() ; 
                 $RiskController->copy_risk($mostRecentlyEqTmp->id, $new_eqTemp->id, $risk_id) ; 
  
                 $PreventiveMaintenanceOperationController= new PreventiveMaintenanceOperationController() ; 
                 $PreventiveMaintenanceOperationController->copy_preventiveMaintenanceOperation($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
              // In the other case, we can add informations without problems
             }else{
 
                $risk->update([
                    'equipmentTemp_id' => $mostRecentlyEqTmp->id,
                ]);
 
             }
             return response()->json($risk_id) ; 
         }
    }

     /**
     * Function call by EquipmentRiskForm.vue when the form is submitted for update with the route : /equipment/update/risk/{id} (post)
     * Update an enregistrement of risk in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the risk we want to update
     * */
    public function update_risk_eqTemp(Request $request, $id){

        //A risk is linked to its target. So we need to find the id of the type choosen by the user and write it in the attribute of the risk.
         //But if no one type is choosen by the user we define this id to NULL
         // And if the type choosen is find in the data base the NULL value will be replace by the id value
         $target_id=NULL ; 
        if ($request->risk_for!='' && $request->risk_for!=NULL){
             $target= EnumRiskFor::where('value', '=', $request->risk_for)->first() ;
             $target_id=$target->id ; 
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
                
                //Creation of a new risk
                $risk=Risk::create([
                    'risk_remarks' => $request->risk_remarks,
                    'risk_wayOfControl' => $request->risk_wayOfControl,
                    'risk_validate' => $request->risk_validate,
                    'enumRiskFor_id'=> $target_id,
                    'equipmentTemp_id'=>$new_eqTemp->id,
                ]) ; 


                //Dédoubler les liens de eqTemps 
                $DimController= new DimensionController() ; 
                $DimController->copy_dimension($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
        
               $PowerController= new PowerController() ; 
               $PowerController->copy_power($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $FileController= new FileController() ; 
               $FileController->copy_file($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $UsageController= new UsageController() ; 
               $UsageController->copy_usage($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $StateController= new StateController() ; 
               $StateController->copy_state($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
           
               $RiskController= new RiskController() ; 
               $RiskController->copy_risk($mostRecentlyEqTmp->id, $new_eqTemp->id, $id) ; 

               $PreventiveMaintenanceOperationController= new PreventiveMaintenanceOperationController() ; 
               $PreventiveMaintenanceOperationController->copy_preventiveMaintenanceOperation($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

                // In the other case, we can modify the informations without problems
            }else{

                $risk=Risk::findOrFail($id) ; 
                $risk->update([
                    'risk_remarks' => $request->risk_remarks,
                    'risk_wayOfControl' => $request->risk_wayOfControl,
                    'risk_validate' => $request->risk_validate,
                    'enumRiskFor_id'=> $target_id,
                ]) ; 
            }
        }
    }


     /**
     * Function call by ReferenceARisk.vue with the route : /equipment/risk/send/{id} (get)
     * Get the risks of the equipment whose id is passed in parameter
     *  The id parameter corresponds to the id of the equipment from which we want the risks
     * @return \Illuminate\Http\Response
     */

    public function send_risks_eqTemp($id) {
        $mostRecentlyEqTmp=EquipmentTemp::where('equipment_id', '=', $id)->latest()->first();
        $risks = Risk::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get();
        $container=array() ; 
        foreach ($risks as $risk) {
            $target = NULL ; 
            if ($risk->enumRiskFor_id!=NULL){
                $target = $risk->enumRiskFor->value ;
            }
            $obj=([
                'id' => $risk->id,
                'risk_remarks' => $risk->risk_remarks,
                'risk_wayOfControl' => $risk->risk_wayOfControl,
                'risk_validate' => $risk->risk_validate,
                'risk_for'=> $target,
            ]) ; 
            array_push($container,$obj);
        }
        return response()->json($container) ;
    }



    /*************************************************** TREATMENTS FOR AN PREVENTIVE MAINTENANCE OPERATION  ***************************************************\
    
    /**
     * Function call by DimensionController (and more) when we need to copy links between equipment temp and a risk
     * Copy the links between a preventive maintenance operation and a risk to the preventive maintenance operation
     *  The id parameter corresponds to the id of the preventive maintenance operation from which we want the risks
     * */
    public function copy_risk_linked_prvMtnOp($actualId, $newId, $idNotCopy){   
        $actualPrvMtnOp= PreventiveMaintenanceOperation::findOrFail($actualId) ; 
        $newPrvMtnOp= PreventiveMaintenanceOperation::findOrFail($newId) ; 
        $risks=$actualPrvMtnOp->risks ; 
        foreach($risks as $risk){
            if ($risk->id!=$idNotCopy){
                //Creation of a new risk
                $newRisk=Risk::create([
                    'risk_remarks' => $request->risk_remarks,
                    'risk_wayOfControl' => $request->risk_wayOfControl,
                    'risk_validate' => $request->risk_validate,
                    'enumRiskFor_id'=> $request-> $target_id,
                    'equipmentTemp_id' => $new_eqTemp->id,
                ]) ; 
            }
        }
    }

    /**
     * Function call by EquipmentRiskForm.vue when the form is submitted for insert with the route : /equipment/add/prvMtnOp/risk/ (post)
     * Add a new enregistrement of risk in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new risk
     */
    public function add_risk_prvMtnOp(Request $request){

        //A risk is linked to its target. So we need to find the id of the type choosen by the user and write it in the attribute of the risk.
         //But if no one type is choosen by the user we define this id to NULL
         // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $target_id=NULL ; 
        if ($request->risk_for!='' && $request->risk_for!=NULL){
             $target= EnumRiskFor::where('value', '=', $request->risk_for)->first() ;
             $target_id=$target->id ; 
         }
         
         //Creation of a new risk
         $risk=Risk::create([
             'risk_remarks' => $request->risk_remarks,
             'risk_wayOfControl' => $request->risk_wayOfControl,
             'risk_validate' => $request->risk_validate,
             'enumRiskFor_id'=> $target_id,
         ]) ; 

 
         $risk_id=$risk->id;
         $id_eq=intval($request->eq_id) ; 
         $equipment=Equipment::findOrfail($request->eq_id) ; 
         $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($request->prvMtnOp_id) ;
         $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
         if ($mostRecentlyEqTmp!=NULL){
              //If the equipment temp is validated and a life sheet has been already created, we need to create another equipment temp (that's mean another life sheet version) for add risk
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
  
                 $PowerController= new PowerController() ; 
                 $PowerController->copy_power($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
  
                 $FileController= new FileController() ; 
                 $FileController->copy_file($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
  
                 $UsageController= new UsageController() ; 
                 $UsageController->copy_usage($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
  
                 $StateController= new StateController() ; 
                 $StateController->copy_state($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
             
                 $RiskController= new RiskController() ; 
                 $RiskController->copy_risk($mostRecentlyEqTmp->id, $new_eqTemp->id, $risk_id) ; 
  
                 $PreventiveMaintenanceOperationController= new PreventiveMaintenanceOperationController() ; 
                 $PreventiveMaintenanceOperationController->copy_preventiveMaintenanceOperation($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 


                 //Creation of a new preventive maintenance operation
                $newPrvMtnOp=PreventiveMaintenanceOperation::create([
                    'prvMtnOp_number' => $prvMtnOp->prvMtnOp_number,
                    'prvMtnOp_description' => $prvMtnOp->prvMtnOp_description,
                    'prvMtnOp_periodicity' => $prvMtnOp->prvMtnOp_periodicity,
                    'prvMtnOp_symbolPeriodicity' => $prvMtnOp->prvMtnOp_symbolPeriodicity,
                    'prvMtnOp_protocol' => $prvMtnOp->prvMtnOp_protocol,
                    'prvMtnOp_startDate' => $prvMtnOp->prvMtnOp_startDate,
                    'prvMtnOp_validate' => $prvMtnOp->prvMtnOp_validate,
                ]) ; 


                $newPrvMtnOp->equipment_temps()->attach($new_eqTemp);

                $risk->update([
                    'preventiveMaintenanceOperation_id' => $newPrvMtnOp->id,
                ]);

                //tout rattacher à op prev mtn :  op mtn prev realized
                $RiskController= new RiskController() ; 
                $RiskController->copy_risk_linked_prvMtnOp($prvMtnOp->id, $newPrvMtnOp->id, $risk_id) ; 


              // In the other case, we can add informations without problems
             }else{
 
                $risk->update([
                    'preventiveMaintenanceOperation_id' => $prvMtnOp->id,
                ]);
                
 
             }
             return response()->json($risk_id) ; 
         }
    }

     /**
     * Function call by EquipmentRiskForm.vue when the form is submitted for update with the route : /equipment/update/prvMtnOp/risk/{id} (post)
     * Update an enregistrement of risk linked to the preventive maintenance operation in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the risk we want to update
     * */
    public function update_risk_prvMtnOp(Request $request, $id){

        //A risk is linked to its target. So we need to find the id of the type choosen by the user and write it in the attribute of the risk.
         //But if no one type is choosen by the user we define this id to NULL
         // And if the type choosen is find in the data base the NULL value will be replace by the id value
         $target_id=NULL ; 
        if ($request->risk_for!='' && $request->risk_for!=NULL){
             $target= EnumRiskFor::where('value', '=', $request->risk_for)->first() ;
             $target_id=$target->id ; 
         }

         $id_eq=intval($request->eq_id) ; 
         $equipment=Equipment::findOrfail($request->eq_id) ; 
         $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($request->prvMtnOp_id) ;
         $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyEqTmp!=NULL){
            //We checked if the most recently equipment temp is validate and if a life sheet has been already created.
            //If the equipment temp is validated and a life sheet has been already created, we need to create another equipment temp (that's mean another life sheet version)
            if ($mostRecentlyEqTmp->eqTemp_validate=="VALIDATED" && (boolean)$mostRecentlyEqTmp->eqTemp_lifeSheetCreated==true){
            
                //Creation of a new risk
                $risk=Risk::create([
                    'risk_remarks' => $request->risk_remarks,
                    'risk_wayOfControl' => $request->risk_wayOfControl,
                    'risk_validate' => $request->risk_validate,
                    'enumRiskFor_id'=> $target_id,
                ]) ; 

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
                $RiskController->copy_risk($mostRecentlyEqTmp->id, $new_eqTemp->id, $id) ; 
 
                $PreventiveMaintenanceOperationController= new PreventiveMaintenanceOperationController() ; 
                $PreventiveMaintenanceOperationController->copy_preventiveMaintenanceOperation($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

                 //Creation of a new preventive maintenance operation
                 $newPrvMtnOp=PreventiveMaintenanceOperation::create([
                    'prvMtnOp_number' => $prvMtnOp->prvMtnOp_number,
                    'prvMtnOp_description' => $prvMtnOp->prvMtnOp_description,
                    'prvMtnOp_periodicity' => $prvMtnOp->prvMtnOp_periodicity,
                    'prvMtnOp_symbolPeriodicity' => $prvMtnOp->prvMtnOp_symbolPeriodicity,
                    'prvMtnOp_protocol' => $prvMtnOp->prvMtnOp_protocol,
                    'prvMtnOp_startDate' => $prvMtnOp->prvMtnOp_startDate,
                    'prvMtnOp_validate' => $prvMtnOp->prvMtnOp_validate,
                ]) ; 


                $newPrvMtnOp->equipment_temps()->attach($new_eqTemp);
                $risk->update([
                    'preventiveMaintenanceOperation_id' => $newPrvMtnOp->id,
                ]);


                //tout rattacher à op prev mtn :  op mtn prev realized
                $RiskController= new RiskController() ; 
                $RiskController->copy_risk_linked_prvMtnOp($prvMtnOp->id, $newPrvMtnOp->id, $risk_id) ; 
                

                // In the other case, we can modify the informations without problems
            }else{

                $risk=Risk::findOrFail($id) ; 
                $risk->update([
                    'risk_remarks' => $request->risk_remarks,
                    'risk_wayOfControl' => $request->risk_wayOfControl,
                    'risk_validate' => $request->risk_validate,
                    'enumRiskFor_id'=> $target_id,
                ]) ; 
            }
        }
    }


    /**
     * Function call by ReferenceARisk.vue with the route : /prvMtnOp/risk/send/{$id} (get)
     * Get the risks of the preventive maintenance operation whose id is passed in parameter
     * The id parameter corresponds to the id of the preventive maintenance operation from which we want the risks
     * @return \Illuminate\Http\Response
     */

    public function send_risks_prvMtnOp($id) {
        $risks = Risk::where('preventiveMaintenanceOperation_id', '=', $id)->get();
        $container=array() ; 
        foreach ($risks as $risk) {
            $target = NULL ; 
            if ($risk->enumRiskFor_id!=NULL){
                $target = $risk->enumRiskFor->value ;
            }
            $obj=([
                'id' => $risk->id,
                'risk_remarks' => $risk->risk_remarks,
                'risk_wayOfControl' => $risk->risk_wayOfControl,
                'risk_validate' => $risk->risk_validate,
                'risk_for'=> $target,
            ]) ; 
            array_push($container,$obj);
        }
        return response()->json($container) ;
    }
         


    /*************************************************** GENERALS TREATMENTS   ***************************************************\



    /**
     * Function call by EquipmentRiskForm.vue when the form is submitted for check data with the route : /risk/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_risk(Request $request){

        //-----CASE risk->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his risk, so he must enter all the attributes
        if ($request->risk_validate=='validated'){
            $this->validate(
                $request,
                [
                    'risk_remarks' => 'required|min:3',
                    'risk_wayOfControl' => 'required|min:3',
                ],
                [
                    'risk_remarks.required' => 'You must enter a remark for your risk ',
                    'risk_remarks.min' => 'You must enter at least three characters ',
                    'risk_wayOfControl.required' => 'You must enter a way of control for your risk',
                    'risk_wayOfControl.min' => 'You must enter at least three characters ',
                ]
            );

            //verification about risk_for, if no one value is selected we need to alert the user
            if ($request->risk_for=='' || $request->risk_for==NULL ){
                return response()->json([
                    'errors' => [
                        'risk_for' => ["You must choose a target for your risk"]
                    ]
                ], 429);
            }

        }else{
             //-----CASE risk->validate=drafted or risk->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations 
            $this->validate(
                $request,
                [
                    'risk_remarks' => 'required|min:3',
                ],
                [
                    'risk_remarks.required' => 'You must enter a remark for your risk ',
                    'risk_remarks.min' => 'You must enter at least three characters ',
                ]
            );
        }
    }


      /**
     * Function call by EquipmentRiskForm.vue when we want to delete a risk with the route : /equipment/delete/risk{id} (post)
     * Delete a risk thanks to the id given in parameter
     * The id parameter correspond to the id of the risk we want to delete
     * */
    public function delete_risk($id){
        $risk=Risk::findOrFail($id);
        $risk->delete() ; 
    }
}


