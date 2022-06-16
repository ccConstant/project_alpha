<?php

/*
* Filename : VerificationController.php 
* Creation date : 15 June 2022
* Update date : 15 June 2022
* This file is used to link the view files and the database that concern the verification table. 
* For example : add a verification for an mme in the data base, update it, delete it...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EquipmentTemp ; 
use App\Models\PreventiveMaintenanceOperation ; 
use App\Models\PreventiveMaintenanceOperationRealized ; 
use App\Models\Equipment ; 
use Carbon\Carbon;

class VerificationController extends Controller
{

    /**
     * Function call by MmeVerifForm.vue when the form is submitted for check data with the route : /verif/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_verif(Request $request){

        //-----CASE verif->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his verification, so he must enter all the attributes
        if ($request->verif_validate=='validated'){
            $this->validate(
                $request,
                [
                    'verif_name' => 'required|min:3|max:100',
                    'verif_description' => 'required|min:3|max:255',
                    'verif_periodicity' => 'required|min:1|max:4',
                    'verif_expectedResult' => 'required|min:3|max:100',
                    'verif_nonComplianceLimit' => 'required|min:3|max:50',
                    'verif_protocol' => 'required|min:3|max:255',
                    'verif_symbolPeriodicity' => 'required',
                ],
                [
                    'verif_name.required' => 'You must enter a name for your verification',
                    'verif_name.min' => 'You must enter at least three characters ',
                    'verif_name.max' => 'You must enter a maximum of 100 characters',

                    'verif_description.required' => 'You must enter a description for verification',
                    'verif_description.min' => 'You must enter at least three characters ',
                    'verif_description.max' => 'You must enter a maximum of 255 characters',

                    'verif_periodicity.required' => 'You must enter a periodicity for your verification',
                    'verif_periodicity.min' => 'You must enter at least one character ',
                    'verif_periodicity.max' => 'You must enter a maximum of 4 characters',

                    'verif_expectedResult.required' => 'You must enter an expectedResult for your verification',
                    'verif_expectedResult.min' => 'You must enter at least three character ',
                    'verif_expectedResult.max' => 'You must enter a maximum of 100 characters',

                    'verif_nonComplianceLimit.required' => 'You must enter a nonComplianceLimit for your verification',
                    'verif_nonComplianceLimit.min' => 'You must enter at least three character ',
                    'verif_nonComplianceLimit.max' => 'You must enter a maximum of 50 characters',

                    'verif_protocol.required' => 'You must enter a protocol for your verification',
                    'verif_protocol.min' => 'You must enter at least three characters ',
                    'verif_protocol.max' => 'You must enter a maximum of 255 characters ',

                    'verif_symbolPeriodicity.required' => 'You must enter a periodicity symbol for your verification',
                ]
            );

        }else{
             //-----CASE verif->validate=drafted or verif->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations 
            $this->validate(
                $request,
                [
                    'verif_name' => 'required|min:3|max:100',
                    'verif_description' => 'required|min:3|max:255',
                    'verif_periodicity' => 'max:4',
                    'verif_expectedResult' => 'max:100',
                    'verif_nonComplianceLimit' => 'max:50',
                    'verif_protocol' => 'max:255',
                ],
                [
                    'verif_name.required' => 'You must enter a name for your verification',
                    'verif_name.min' => 'You must enter at least three characters ',
                    'verif_name.max' => 'You must enter a maximum of 100 characters',

                    'verif_description.required' => 'You must enter a description for verification',
                    'verif_description.min' => 'You must enter at least three characters ',
                    'verif_description.max' => 'You must enter a maximum of 255 characters',

                    'verif_periodicity.max' => 'You must enter a maximum of 4 characters',
                    'verif_expectedResult.max' => 'You must enter a maximum of 100 characters',
                    'verif_nonComplianceLimit.max' => 'You must enter a maximum of 50 characters',
                    'verif_protocol.max' => 'You must enter a maximum of 255 characters ',
                ]
            );
        }


        if ($request->verif_periodicity!='' && $request->verif_periodicity!=NULL && $request->verif_symbolPeriodicity!='' && $request->verif_symbolPeriodicity!=NULL){
            if ($request->verif_symbolPeriodicity=='Y' && $request->verif_periodicity>15){
                return response()->json([
                    'errors' => [
                        'verif_periodicity' => ["You can't enter a periodicity higher than 15 years"]
                    ]
                ], 429);
            }

            if ($request->verif_symbolPeriodicity=='M' && $request->verif_periodicity>180){
                return response()->json([
                    'errors' => [
                        'verif_periodicity' => ["You can't enter a periodicity higher than 180 months"]
                    ]
                ], 429);
            }

            if ($request->verif_symbolPeriodicity=='D' && $request->verif_periodicity>5475){
                return response()->json([
                    'errors' => [
                        'verif_periodicity' => ["You can't enter a periodicity higher than 5475 days"]
                    ]
                ], 429);
            }
        }
    }


    /**
     * Function call by MmeVerifForm.vue when the form is submitted for insert with the route : /mme/add/verif(post)
     * Add a new enregistrement of verification in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : the id of the new verification
     */
    public function add_verif(Request $request){
        
        $id_mme=intval($request->mme_id) ; 
        $mme=Mme::findOrfail($request->mme_id) ; 
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->orderBy('created_at', 'desc')->first();
        $verifInMmes=Verification::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->get();
        $max_number=1 ; 
        if (count($mmeMtnOpsInMme)!=0){
            foreach ($verifInMmes as $verifInMme){
                $number=intval($verifInMme->verif_number) ; 
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
             //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for add prvMtnOp
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
                ]);
            }
        }
        return response()->json($prvMtnOp_id) ; 
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
           //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for update prvMtnOp
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
               ]);
            }

            if ($request->prvMtnOp_periodicity!=NULL && $request->prvMtnOp_symbolPeriodicity!=NULL && ($oldPrvMtnOp->prvMtnOp_periodicity!=$request->prvMtnOp_periodicity || $oldPrvMtnOp->prvMtnOp_symbolPeriodicity!=$request->prvMtnOp_symbolPeriodicity)){
                
                $dates=explode(' ', $oldPrvMtnOp->prvMtnOp_startDate) ; 
                $ymd=explode('-', $dates[0]);
                $year=$ymd[0] ; 
                $month=$ymd[1] ;
                $day=$ymd[2] ;

                $time=explode(':', $dates[1]); 
                $hour=$time[0] ;
                $min=$time[1] ; 
                $sec=$time[2] ;
                
                $nextDate=Carbon::create($year, $month, $day, $hour, $min, $sec);

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
                 if ($request->prvMtnOp_symbolPeriodicity=='H'){
                    $nextDate->addHours($request->prvMtnOp_periodicity) ; 
                }

                //return response()->json($nextDate) ;
                 $oldPrvMtnOp->update([
                    'prvMtnOp_nextDate' => $nextDate,
                ]);
            }
            
            $oldPrvMtnOp->update([
                'prvMtnOp_description' => $request->prvMtnOp_description,
                'prvMtnOp_periodicity' => $request->prvMtnOp_periodicity,
                'prvMtnOp_symbolPeriodicity' => $request->prvMtnOp_symbolPeriodicity,
                'prvMtnOp_protocol' => $request->prvMtnOp_protocol,
                'prvMtnOp_validate' => $request->prvMtnOp_validate,
            ]) ;

        }
    }

    /**
     * Function call by ConsultationLifeSheetPdf.vue with the route : /prvMtnOps/send/lifesheet/{id} (get)
     * Get the preventive maintenance operations of the equipment whose id is passed in parameter for the lifesheet 
     * The id parameter corresponds to the id of the equipment from which we want the preventive maintenance operations 
     * @return \Illuminate\Http\Response
     */

    public function send_prvMtnOps_lifesheet($id) {
        $container=array() ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->latest()->first();
        $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ; 
       foreach ($prvMtnOps as $prvMtnOp) {
            $riskExist=false ; 
            $risks=Risk::where('preventiveMaintenanceOperation_id', '=', $prvMtnOp->id)->get() ; 
            if (count($risks)>0){
                $riskExist=true ; 
            }
            $obj=([
                "id" => $prvMtnOp->id,
                "Number" => (string)$prvMtnOp->prvMtnOp_number,
                "Description" => $prvMtnOp->prvMtnOp_description,
                "Periodicity" => (string)$prvMtnOp->prvMtnOp_periodicity,
                "Symbol" => $prvMtnOp->prvMtnOp_symbolPeriodicity,
                "Protocol" => $prvMtnOp->prvMtnOp_protocol,
                "Risk" => $riskExist,
                
            ]);
            array_push($container,$obj);
       }
        return response()->json($container) ;
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
     * The id parameter corresponds to the id of the equipment from which we want the preventive maintenance operations validated
     * @return \Illuminate\Http\Response
     */
    public function send_prvMtnOp_from_eq_validated($id) {
        $container=array() ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->where('prvMtnOp_validate', '=', "validated")->get() ; 

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
        $prvMtnOps=PreventiveMaintenanceOperation::where('prvMtnOp_validate', '=', "validated")->get() ; 
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
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        //We search the most recently equipment temp of the equipment 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->latest()->first();
        //We checked if the most recently equipment temp is validate and if a life sheet has been already created.
        //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for update dimension
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
            ]);
        }
        
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

        /**
     * Function call by ReferenceAPrvMtnOp.vue when we want to reform a prvMtnOp with the route : '/equipment/reform/prvMtnOp/{id} (post)
     * Reform a prvMtnOp thanks to the id given in parameter
     * The id parameter correspond to the id of the prvMtnOp we want to reform
     * 
     * */

    public function reform_prvMtnOp(Request $request, $id){
        $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($id) ; 
        if ($request->prvMtnOp_reformDate<$prvMtnOp->prvMtnOp_startDate){
            return response()->json([
                'errors' => [
                    'prvMtnOp_reformDate' => ["You must entered a reformDate that is after the startDate"] 
                ]
            ], 429);
        }

        $oneMonthAgo=Carbon::now()->subMonth(1) ; 
        if ($request->prvMtnOp_reformDate!=NULL && $request->prvMtnOp_reformDate<$oneMonthAgo){
            return response()->json([
                'errors' => [
                    'prvMtnOp_reformDate' => ["You can't enter a date that is older than one month"]
                ]
            ], 429);
        }
        
        $prvMtnOp->update([
            'prvMtnOp_reformDate' => $request->prvMtnOp_reformDate,
            //REVENIR ICI POUR REFORMED BY 
        ]) ;
    }

}


