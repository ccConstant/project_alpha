<?php

/*
* Filename : PreventiveMaintenanceOperationController.php 
* Creation date : 17 May 2022
* Update date : 17 May 2022
* This file is used to link the view files and the database that concern the preventiveMaintenanceOperation table. 
* For example : add a preventiveMaintenanceOperation for an equipment in the data base, update it, delete it...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EquipmentTemp ; 
use App\Models\PreventiveMaintenanceOperation ; 
use App\Models\PreventiveMaintenanceOperationRealized ; 
use App\Models\Equipment ; 
use App\Http\Controllers\PowerController ; 
use App\Http\Controllers\FileController ; 
use App\Http\Controllers\UsageController ; 
use App\Http\Controllers\StateController ; 
use App\Http\Controllers\RiskController ; 
use App\Http\Controllers\DimensionController;
use App\Http\Controllers\SpecialProcessController ; 
use Carbon\Carbon;

class PreventiveMaintenanceOperationController extends Controller
{

    /**
     * Function call by EquipmentPrvMtnOpForm.vue when the form is submitted for check data with the route : /prvMtnOp/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_prvMtnOp(Request $request){

        //-----CASE prvMtnOp->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his preventive maintenance operation, so he must enter all the attributes
        if ($request->prvMtnOp_validate=='validated'){
            $this->validate(
                $request,
                [
                    'prvMtnOp_description' => 'required|min:3|max:255',
                    'prvMtnOp_periodicity' => 'required|min:1|max:4',
                    'prvMtnOp_protocol' => 'required|min:3',
                    'prvMtnOp_symbolPeriodicity' => 'required',
                ],
                [
                    'prvMtnOp_description.required' => 'You must enter a description for your preventive maintenance operation',
                    'prvMtnOp_description.min' => 'You must enter at least three characters ',
                    'prvMtnOp_description.max' => 'You must enter a maximum of 255 characters',
                    'prvMtnOp_periodicity.required' => 'You must enter a periodicity for your preventive maintenance operation',
                    'prvMtnOp_periodicity.min' => 'You must enter at least one character ',
                    'prvMtnOp_periodicity.max' => 'You must enter a maximum of 4 characters',
                    'prvMtnOp_protocol.required' => 'You must enter a protocol for your preventive maintenance operation',
                    'prvMtnOp_protocol.min' => 'You must enter at least three characters ',
                    'prvMtnOp_symbolPeriodicity.required' => 'You must enter a periodicity symbol for your preventive maintenance operation',

                
                ]
            );

        }else{
             //-----CASE prvMtnOp->validate=drafted or prvMtnOp->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations 
            $this->validate(
                $request,
                [
                    'prvMtnOp_description' => 'required|min:3|max:255',
                    'prvMtnOp_periodicity' => 'max:4',
                ],
                [
                    'prvMtnOp_description.required' => 'You must enter a description for your preventive maintenance operation',
                    'prvMtnOp_description.min' => 'You must enter at least three characters ',
                    'prvMtnOp_description.max' => 'You must enter a maximum of 255 characters',
                    'prvMtnOp_periodicity.max' => 'You must enter a maximum of 4 characters',

                
                ]
            );
        }
    }


    /**
     * Function call by EquipmenPrvMtnOpForm.vue when the form is submitted for insert with the route : /equipment/add/prvMtnOp(post)
     * Add a new enregistrement of preventive maintenance operation in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : the id of the new prvMtnOp
     */
    public function add_prvMtnOp(Request $request){
        
        $id_eq=intval($request->eq_id) ; 
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        $prvMtnOpsInEq=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get();
        $max_number=1 ; 
        if (count($prvMtnOpsInEq)!=0){
            foreach ($prvMtnOpsInEq as $prvMtnOpInEq){
                $number=intval($prvMtnOpInEq->prvMtnOp_number) ; 
                if ($number>$max_number){
                    $max_number=$prvMtnOpInEq->prvMtnOp_number ; 
                }
            }
            $max_number=$max_number+1 ;
        }
        $nextDate=NULL ; 
       
        $startDate=Carbon::now('Europe/Paris');
        if ($request->prvMtnOp_symbolPeriodicity!='' && $request->prvMtnOp_symbolPeriodicity!=NULL && $request->prvMtnOp_periodicity!='' && $request->prvMtnOp_periodicity!=NULL ){
            $nextDate=Carbon::create($startDate->year, $startDate->month, $startDate->day, $startDate->hour, $startDate->minute, $startDate->second);
           
            if ($request->prvMtnOp_symbolPeriodicity=='Y'){
                $nextDate->addYears($request->prvMtnOp_periodicity) ; 
            }

            if ($request->prvMtnOp_symbolPeriodicity=='M'){
                $nextDate->addMonths($request->prvMtnOp_periodicity) ; 
            }

            if ($request->prvMtnOp_symbolPeriodicity=='D'){
                $nextDate->addDays($request->prvMtnOp_periodicity) ; 
            }

            if ($request->prvMtnOp_symbolPeriodicity=='H'){
                $nextDate->addHours($request->prvMtnOp_periodicity) ; 
            }
        }
        
        //Creation of a new preventive maintenance operation
        $prvMtnOp=PreventiveMaintenanceOperation::create([
            'prvMtnOp_number' => $max_number,
            'prvMtnOp_description' => $request->prvMtnOp_description,
            'prvMtnOp_periodicity' => $request->prvMtnOp_periodicity,
            'prvMtnOp_symbolPeriodicity' => $request->prvMtnOp_symbolPeriodicity,
            'prvMtnOp_protocol' => $request->prvMtnOp_protocol,
            'prvMtnOp_startDate' => Carbon::now('Europe/Paris'),
            'prvMtnOp_nextDate' => $nextDate,
            'prvMtnOp_validate' => $request->prvMtnOp_validate,
            'equipmentTemp_id' => $mostRecentlyEqTmp->id,
        ]) ; 
            
        $prvMtnOp_id=$prvMtnOp->id;
        if ($mostRecentlyEqTmp!=NULL){
             //If the equipment temp is validated and a life sheet has been already created, we need to create another equipment temp (that's mean another life sheet version) for add preventive maintenance operation
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

               $prvMtnOp->update([
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
               $RiskController->copy_risEqTemp($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

               $PreventiveMaintenanceOperationController= new PreventiveMaintenanceOperationController() ; 
               $PreventiveMaintenanceOperationController->copy_preventiveMaintenanceOperation($mostRecentlyEqTmp->id, $new_eqTemp->id, $prvMtnOp_id) ; 

             // In the other case, we can add informations without problems
            }
            return response()->json($prvMtnOp_id) ; 
        }
    }


     /**
     * Function call by DimensionController (and more) when we need to copy links between equipment temp and a preventive maintenance operation
     * Copy the links between a equipment temp and a preventive maintenance operation to the new equipment temp
     * The actualId parameter correspond of the id of the equipment from which we want to copy the preventive maintenance operations
     * The newId parameter correspond of the id of the equipment where we want to copy the preventive maintenance operations
     * The idNotCopy parameter correspond of the id of the preventive maintenance operation we don't have to copy 
     * */
    public function copy_preventiveMaintenanceOperation($actualId, $newId, $idNotCopy){ 
        $actualEqTemp= EquipmentTemp::findOrFail($actualId) ; 
        $newEqTemp= EquipmentTemp::findOrFail($newId) ; 
        $prv_mtn_ops=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $actualId)->get() ; 
        foreach($prv_mtn_ops as $prv_mtn_op){
            if ($prv_mtn_op->id!=$idNotCopy){
                //Creation of a new preventive maintenance operation
                $newPrvMtnOp=PreventiveMaintenanceOperation::create([
                    'prvMtnOp_number' => $prv_mtn_op->prvMtnOp_number,
                    'prvMtnOp_description' => $prv_mtn_op->prvMtnOp_description,
                    'prvMtnOp_periodicity' => $prv_mtn_op->prvMtnOp_periodicity,
                    'prvMtnOp_symbolPeriodicity' => $prv_mtn_op->prvMtnOp_symbolPeriodicity,
                    'prvMtnOp_protocol' => $prv_mtn_op->prvMtnOp_protocol,
                    'prvMtnOp_startDate' => $prv_mtn_op->prvMtnOp_startDate,
                    'prvMtnOp_reformDate' => $prv_mtn_op->prvMtnOp_reformDate,
                    'prvMtnOp_validate' => $prv_mtn_op->prvMtnOp_validate,
                    'equipmentTemp_id' => $newId,
                ]) ; 
            } 
        }
    }


    /**
     * Function call by EquipmentPrvMtnOpForm.vue when the form is submitted for update with the route :/equipment/update/prvMtnOp/{id} (post)
     * Update an enregistrement of preventive maintenance operation in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the preventive maintenance operation we want to update
     * */
    public function update_prvMtnOp(Request $request, $id){
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        $oldPrvMtnOp=PreventiveMaintenanceOperation::findOrFail($id) ; 
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
                
                $prvMtnOpOld=PreventiveMaintenanceOperation::findOrFail($id) ; 
        
                //Creation of a new preventive maintenance operation
                $prvMtnOp=PreventiveMaintenanceOperation::create([
                    'prvMtnOp_number' => $prvMtnOpOld->prvMtnOp_number,
                    'prvMtnOp_description' => $request->prvMtnOp_description,
                    'prvMtnOp_periodicity' => $request->prvMtnOp_periodicity,
                    'prvMtnOp_symbolPeriodicity' => $request->prvMtnOp_symbolPeriodicity,
                    'prvMtnOp_protocol' => $request->prvMtnOp_protocol,
                    'prvMtnOp_startDate' => $prvMtnOpOld->prvMtnOp_startDate,
                    'prvMtnOp_nextDate' => $prvMtnOpOld->prvMtnOp_nextDate,
                    'prvMtnOp_validate' => $request->prvMtnOp_validate,
                    'equipmentTemp_id' => $new_eqTemp->id,
                ]) ;

                /*if ($request->prvMtnOp_periodicity!=NULL && $request->prvMtnOp_symbolPeriodicity!=NULL && ($oldPrvMtnOp->prvMtnOp_periodicity!=$request->prvMtnOp_periodicity ||  $oldPrvMtnOp->prvMtnOp_symbolPeriodicity!=$request->prvMtnOp_symbolPeriodicity)){
                    
                    $nextDate=NULL ; 
    
                    $nextDate=Carbon::create($oldPrvMtnOp->prvMtnOp_startDate->year, $oldPrvMtnOp->prvMtnOp_startDate->month, $oldPrvMtnOp->prvMtnOp_startDate->day, $oldPrvMtnOp->prvMtnOp_startDate->hour, $oldPrvMtnOp->prvMtnOp_startDate->minute, $oldPrvMtnOp->prvMtnOp_startDate->second);
                    if ($request->prvMtnOp_symbolPeriodicity=='Y'){
                        $nextDate->addYears($prvMtnOp->prvMtnOp_periodicity) ; 
                    }
        
                    if ($request->prvMtnOp_symbolPeriodicity=='M'){
                        $nextDate->addMonths($prvMtnOp->prvMtnOp_periodicity) ; 
                    }
                    
                    if ($request->prvMtnOp_symbolPeriodicity=='D'){
                        $nextDate->addDays($prvMtnOp->prvMtnOp_periodicity) ; 
                    }
        
                    if ($request->prvMtnOp_symbolPeriodicity=='H'){
                        $nextDate->addHours($prvMtnOp->prvMtnOp_periodicity) ; 
                    }

                    $prvMtnOp->update([
                        'prvMtnOp_nextDate' => $nextDate,
                    ]);
                }*/
                
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
                $StateController->copy_state($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 
            
                $RiskController= new RiskController() ; 
                $RiskController->copy_risk($mostRecentlyEqTmp->id, $new_eqTemp->id, -1) ; 

                $PreventiveMaintenanceOperationController= new PreventiveMaintenanceOperationController() ; 
                $PreventiveMaintenanceOperationController->copy_preventiveMaintenanceOperation($mostRecentlyEqTmp->id, $new_eqTemp->id, $id) ; 
        

                // In the other case, we can modify the informations without problems
            }else{
               
               /*if ($request->prvMtnOp_periodicity!=NULL && $request->prvMtnOp_symbolPeriodicity!=NULL && ($oldPrvMtnOp->prvMtnOp_periodicity!=$request->prvMtnOp_periodicity || $oldPrvMtnOp->prvMtnOp_symbolPeriodicity!=$request->prvMtnOp_symbolPeriodicity)){
                    $nextDate=Carbon::create($oldPrvMtnOp->prvMtnOp_startDate->year, $oldPrvMtnOp->prvMtnOp_startDate->month, $oldPrvMtnOp->prvMtnOp_startDate->day, $oldPrvMtnOp->prvMtnOp_startDate->hour, $oldPrvMtnOp->prvMtnOp_startDate->minute, $oldPrvMtnOp->prvMtnOp_startDate->second);
                    return response()->json($nextDate) ;

                    if ($request->prvMtnOp_symbolPeriodicity=='Y'){
                        $nextDate->addYears($request->prvMtnOp_periodicity) ; 
                    }
        
                    if ($request->prvMtnOp_symbolPeriodicity=='M'){
                        $nextDate->addMonths($request->prvMtnOp_periodicity) ; 
                    }
                    
                    if ($request->prvMtnOp_symbolPeriodicity=='D'){
                        $nextDate->addDays($request->prvMtnOp_periodicity) ; 
                        return response()->json($nextDate) ;
                    }
                   /* if ($request->prvMtnOp_symbolPeriodicity=='H'){
                        $nextDate->addHours($request->prvMtnOp_periodicity) ; 
                    }
                   /* $oldPrvMtnOp->update([
                        'prvMtnOp_nextDate' => $nextDate,
                    ]);
                }
                */
                $oldPrvMtnOp->update([
                    'prvMtnOp_description' => $request->prvMtnOp_description,
                    'prvMtnOp_periodicity' => $request->prvMtnOp_periodicity,
                    'prvMtnOp_symbolPeriodicity' => $request->prvMtnOp_symbolPeriodicity,
                    'prvMtnOp_protocol' => $request->prvMtnOp_protocol,
                    'prvMtnOp_validate' => $request->prvMtnOp_validate,
                ]) ;
            }
        }
    }

    /**
     * Function call by ReferenceAPrvMtnOp.vue with the route : /prvMtnOps/send/{id} (get)
     * Get the preventive maintenance operations of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the preventive maintenance operations 
     * @return \Illuminate\Http\Response
     */

    public function send_prvMtnOps($id) {
        $container=array() ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->latest()->first();
        $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ; 
       foreach ($prvMtnOps as $prvMtnOp) {
            $obj=([
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
            array_push($container,$obj);
       }
        return response()->json($container) ;
    }

     /**
     * Function call by ReferenceAPrvMtnOp.vue with the route : /prvMtnOp/send/{id} (get)
     * Get the informations of the preventive maintenance operation whose id is passed in parameter
     * The id parameter corresponds to the id of the preventive maintenance operations from which we want the informations
     * @return \Illuminate\Http\Response
     */

    public function send_prvMtnOp($id) {
        $container=array() ; 
        $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($id) ; 
        $obj=([
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
        array_push($container,$obj);
        return response()->json($container) ;
    }


    /**
     * Function call by ???  with the route : /prvMtnOp/send/validated/{id} (get)
     * Get the preventive maintenance operations validated of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the preventive maintenance operations VALIDATED
     * @return \Illuminate\Http\Response
     */
    public function send_prvMtnOp_from_eq_validated($id) {
        $container=array() ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->where('prvMtnOp_validate', '=', "VALIDATED")->get() ; 

       foreach ($prvMtnOps as $prvMtnOp) {
           if ($prvMtnOp->prvMtnOp_reformDate=='' || $prvMtnOp->prvMtnOp_reformDate===NULL){
                $obj=([
                    "id" => $prvMtnOp->id,
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
     * Function call by ???  with the route : /prvMtnOp/send/validated/ (get)
     * Get all the preventive maintenance operations validated present in the data base 
     * @return \Illuminate\Http\Response
     */
   /* public function send_all_prvMtnOp_validated() {
        $container=array() ; 
        $prvMtnOps=PreventiveMaintenanceOperation::where('prvMtnOp_validate', '=', "VALIDATED")->get() ; 
       foreach ($prvMtnOps as $prvMtnOp) {
           if ($prvMtnOp->prvMtnOp_reformDate=='' && $prvMtnOp->prvMtnOp_reformDate==NULL){
                $obj=([
                    "id" => $prvMtnOp->id,
                    "prvMtnOp_number" => (string)$prvMtnOp->prvMtnOp_number,
                    "prvMtnOp_description" => $prvMtnOp->prvMtnOp_description,
                    "prvMtnOp_protocol" => $prvMtnOp->prvMtnOp_protocol,
                ]);
                array_push($container,$obj);
           }
       }
        return response()->json($container) ;
    }*/

    /**
     * Function call by EquipmentPrvMtnOpForm.vue when we want to delete a dimension with the route : /equipment/delete/prvMtnOp/{id}(post)
     * Delete a preventive maintenance operation thanks to the id given in parameter
     * The id parameter correspond to the id of the preventive maintenance operation we want to delete
     * */
    public function delete_prvMtnOp(Request $request, $id){
        $prvMtnOpsInEq=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $request->eq_id)->get() ; 
        $prvMtnOpRlzs=PreventiveMaintenanceOperationRealized::where('prvMtnOp_id', '=', $id)->get() ; 
        $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($id) ; 
        if (count($prvMtnOpRlzs)==0){
            foreach($prvMtnOpsInEq as $prvMtnOpInEq){
                if ($prvMtnOpInEq->prvMtnOp_number>$prvMtnOp->prvMtnOp_number){
                    $prvMtnOpInEq->prvMtnOp_number=$prvMtnOpInEq->prvMtnOp_number-1 ; 
                    $prvMtnOpInDB=PreventiveMaintenanceOperation::findOrFail($prvMtnOpInEq->id) ; 
                    $prvMtnOpInDB->update([
                        'prvMtnOp_number' =>  $prvMtnOpInEq->prvMtnOp_number,
                    ]);
                }
            }
            $prvMtnOp->delete() ; 
        }else{
            return response()->json([
                'errors' => [
                    'prvMtnOp_delete' => ["You can't delete a preventive maintenance operation that is already realized"]
                ]
            ], 429);
        }
    }

}



