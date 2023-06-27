<?php

/*
* Filename : PreventiveMaintenanceOperationController.php
* Creation date : 17 May 2022
* Update date : 7 Mar 2023
* This file is used to link the view files and the database that concern the preventiveMaintenanceOperation table.
* For example : add a preventiveMaintenanceOperation for an equipment in the data base, update it, delete it...
*/

namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ;
use App\Models\SW01\EquipmentTemp ;
use App\Models\SW01\PreventiveMaintenanceOperation ;
use App\Models\SW01\PreventiveMaintenanceOperationRealized ;
use App\Models\SW01\Equipment ;
use App\Models\SW01\Risk ;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\SW01\State;
use App\Models\User;

class PreventiveMaintenanceOperationController extends Controller
{

    /**
     * Function call by EquipmentPrvMtnOpForm.vue when the form is submitted for check data with the route : /prvMtnOp/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_prvMtnOp(Request $request){
        $user=User::findOrFail($request->user_id);
        if (!$user->user_validateDescriptiveLifeSheetDataRight && $request->prvMtnOp_validate=="validated"){
            return response()->json([
                'errors' => [
                    'prvMtnOp_description' => ["You don't have the user right to save a preventive maintenance operation as validated"]
                ]
            ], 429);
        }
        if ($request->reason=="update"){
            $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($request->prvMtnOp_id);
            if (!$user->user_updateDataInDraftRight && ($prvMtnOp->prvMtnOp_validate=="drafted" || $prvMtnOp->prvMtnOp_validate=="to_be_validated")){
                return response()->json([
                    'errors' => [
                        'prvMtnOp_description' => ["You don't have the user right to update a preventive maintenance operation save as drafted or in to be validated"]
                    ]
                ], 429);
            }

            if (!$user->user_updateDataValidatedButNotSignedRight && $prvMtnOp->prvMtnOp_validate=="validated"){
                return response()->json([
                    'errors' => [
                        'prvMtnOp_description' => ["You don't have the user right to update a preventive maintenance operation save as validated"]
                    ]
                ], 429);
            }
            if (!$user->user_updateDescriptiveLifeSheetDataSignedRight && $request->lifesheet_created==true){
                return response()->json([
                    'errors' => [
                        'prvMtnOp_description' => ["You don't have the user right to update a preventive maintenance operation signed"]
                    ]
                ], 429);
            }
        }
                //-----CASE prvMtnOp->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his preventive maintenance operation, so he must enter all the attributes
        if ($request->prvMtnOp_validate=='validated'){
            if (!$request->prvMtnOp_preventiveOperation){
                $this->validate(
                    $request,
                    [
                        'prvMtnOp_description' => 'required|min:3|max:255',
                        'prvMtnOp_protocol' => 'required|min:3|max:255',
                        'prvMtnOp_periodicity' => 'max:4',
                    ],
                    [
                        'prvMtnOp_description.required' => 'You must enter a description for your preventive maintenance operation',
                        'prvMtnOp_description.min' => 'You must enter at least three characters ',
                        'prvMtnOp_description.max' => 'You must enter a maximum of 255 characters',
                        'prvMtnOp_protocol.required' => 'You must enter a protocol for your preventive maintenance operation',
                        'prvMtnOp_protocol.min' => 'You must enter at least three characters ',
                        'prvMtnOp_protocol.max' => 'You must enter a maximum of 255 characters',
                        'prvMtnOp_periodicity.max' => 'You must enter a maximum of 4 characters',
                    ]
                );

            }else{
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
            }
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


        if ($request->prvMtnOp_periodicity!='' && $request->prvMtnOp_periodicity!=NULL && $request->prvMtnOp_symbolPeriodicity!='' && $request->prvMtnOp_symbolPeriodicity!=NULL){
            if ($request->prvMtnOp_symbolPeriodicity=='Y' && $request->prvMtnOp_periodicity>15){
                return response()->json([
                    'errors' => [
                        'prvMtnOp_periodicity' => ["You can't enter a periodicity higher than 15 years"]
                    ]
                ], 429);
            }

            if ($request->prvMtnOp_symbolPeriodicity=='M' && $request->prvMtnOp_periodicity>180){
                return response()->json([
                    'errors' => [
                        'prvMtnOp_periodicity' => ["You can't enter a periodicity higher than 180 months"]
                    ]
                ], 429);
            }

            if ($request->prvMtnOp_symbolPeriodicity=='D' && $request->prvMtnOp_periodicity>5475){
                return response()->json([
                    'errors' => [
                        'prvMtnOp_periodicity' => ["You can't enter a periodicity higher than 5475 days"]
                    ]
                ], 429);
            }
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
        if ($request->prvMtnOp_preventiveOperation){
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
            'prvMtnOp_puttingIntoService' => $request->prvMtnOp_puttingIntoService,
            'prvMtnOp_preventiveOperation' => $request->prvMtnOp_preventiveOperation,
            'equipmentTemp_id' => $mostRecentlyEqTmp->id,
            'typeValidation' => $request->typeValidation,
        ]) ;

        $prvMtnOp_id=$prvMtnOp->id;
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
                 'eqTemp_lifeSheetCreated' => false,
                 'eqTemp_signatureDate' => NULL,
                ]);

                $states=$mostRecentlyEqTmp->states;
                if ($states!==NULL){
                    $mostRecentlyState=NULL ;
                    $first=true ;
                    foreach($states as $state){
                        if ($first){
                            $mostRecentlyState=$state ;
                            $first=false;
                        }else{
                            $date=$state->created_at ;
                            $date2=$mostRecentlyState->created_at;
                            if ($date>=$date2){
                                $mostRecentlyState=$state ;
                            }
                        }
                    }
                    if ($mostRecentlyState!=NULL){
                        $mostRecentlyState->update([
                            'state_endDate' => Carbon::now('Europe/Paris'),
                        ]);
                    }
                }

                //Creation of a new state
                $newState=State::create([
                    'state_remarks' => "Equipment Update (add prv mtn op) : new version of life sheet created",
                    'state_startDate' =>  Carbon::now('Europe/Paris'),
                    'state_validate' => "validated",
                    'state_name' => "Waiting_for_referencing"
                ]) ;

                $newState->equipment_temps()->attach($mostRecentlyEqTmp);
            }
        }
        return response()->json($prvMtnOp_id) ;
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
                'eqTemp_lifeSheetCreated' => false,
                'eqTemp_signatureDate' => NULL,
               ]);


               $states=$mostRecentlyEqTmp->states;
                if ($states!==NULL){
                    $mostRecentlyState=NULL ;
                    $first=true ;
                    foreach($states as $state){
                        if ($first){
                            $mostRecentlyState=$state ;
                            $first=false;
                        }else{
                            $date=$state->created_at ;
                            $date2=$mostRecentlyState->created_at;
                            if ($date>=$date2){
                                $mostRecentlyState=$state ;
                            }
                        }
                    }
                    if ($mostRecentlyState!=NULL){
                        $mostRecentlyState->update([
                            'state_endDate' => Carbon::now('Europe/Paris'),
                        ]);
                    }
                }

               //Creation of a new state
               $newState=State::create([
                'state_remarks' => "Equipment Update (update prv mtn op) : new version of life sheet created",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_validate' => "validated",
                'state_name' => "Waiting_for_referencing"
            ]) ;

            $newState->equipment_temps()->attach($mostRecentlyEqTmp);
            }

            if ($request->prvMtnOp_periodicity!=NULL && $request->prvMtnOp_symbolPeriodicity!=NULL && ($oldPrvMtnOp->prvMtnOp_periodicity!=$request->prvMtnOp_periodicity || $oldPrvMtnOp->prvMtnOp_symbolPeriodicity!=$request->prvMtnOp_symbolPeriodicity || $oldPrvMtnOp->prvMtnOp_preventiveOperation!=$request->prvMtnOp_preventiveOperation)){

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
                }
                 if ($request->prvMtnOp_symbolPeriodicity=='H'){
                    $nextDate->addHours($request->prvMtnOp_periodicity) ;
                }
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
                'prvMtnOp_puttingIntoService' => $request->prvMtnOp_puttingIntoService,
                'prvMtnOp_preventiveOperation' => $request->prvMtnOp_preventiveOperation,
                'typeValidation' => $request->typeValidation,
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
            $riskExist="no" ;
            $risks=Risk::where('preventiveMaintenanceOperation_id', '=', $prvMtnOp->id)->get() ;
            if (count($risks)>0){
                $riskExist="yes" ;
            }

            $puttinIntoService="no";
            if ($prvMtnOp->prvMtnOp_puttingIntoService==true){
                $puttinIntoService="yes";
            }

            $preventiveOperation="no";
            if ($prvMtnOp->prvMtnOp_preventiveOperation==true){
                $preventiveOperation="yes";
            }

            $reformed="no";
            if ($prvMtnOp->prvMtnOp_reformDate!=NULL){
                $reformed="yes";
            }

            $obj=([
                "id" => $prvMtnOp->id,
                "Number" => (string)$prvMtnOp->prvMtnOp_number,
                "Description" => $prvMtnOp->prvMtnOp_description,
                "Periodicity" => (string)$prvMtnOp->prvMtnOp_periodicity,
                "Symbol" => $prvMtnOp->prvMtnOp_symbolPeriodicity,
                "Protocol" => $prvMtnOp->prvMtnOp_protocol,
                "Risk" => $riskExist,
                "PuttingIntoService"=>$puttinIntoService,
                "PreventiveOperation"=>$preventiveOperation,
                "Reformed"=>$reformed,
                'typeValidation' => $prvMtnOp->typeValidation,
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
                "prvMtnOp_puttingIntoService" => (boolean)$prvMtnOp->prvMtnOp_puttingIntoService,
                "prvMtnOp_preventiveOperation" => (boolean)$prvMtnOp->prvMtnOp_preventiveOperation,
                'typeValidation' => $prvMtnOp->typeValidation,

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
            "prvMtnOp_puttingIntoService" => (boolean)$prvMtnOp->prvMtnOp_puttingIntoService,
            "prvMtnOp_preventiveOperation" => (boolean)$prvMtnOp->prvMtnOp_preventiveOperation,
            'typeValidation' => $prvMtnOp->typeValidation,

        ]);
        array_push($container,$obj);
        return response()->json($container) ;
    }

    /**
     * Function call by EquipementMaintenanceCalendar.vue  with the route : /prvMtnOp/send/revisionTimeLimitPassed/{id} (get)
     * Get the preventive maintenance operations validated whose time limit has passed of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the preventive maintenance operations validated
     * @return \Illuminate\Http\Response
     */
    /*public function send_prvMtnOp_from_eq_revisionTimeLimitPassed($id) {
        $container=array() ;
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->where('prvMtnOp_validate', '=', "validated")->where('prvMtnOp_reformDate','=',NULL)->get() ;

        $today=Carbon::now() ;
       foreach ($prvMtnOps as $prvMtnOp) {
            if ($prvMtnOp_preventiveOperation){
                $OneWeekLater=$prvMtnOp->prvMtnOp_nextDate->addDays(7) ;
                if (($prvMtnOp->prvMtnOp_reformDate=='' || $prvMtnOp->prvMtnOp_reformDate===NULL) && $OneWeekLater<$today ){
                        $obj=([
                            "id" => $prvMtnOp->id,
                            "prvMtnOp_number" => (string)$prvMtnOp->prvMtnOp_number,
                            "prvMtnOp_description" => $prvMtnOp->prvMtnOp_description,
                            "prvMtnOp_protocol" => $prvMtnOp->prvMtnOp_protocol,
                            "prvMtnOp_nextDate" => $prvMtnOp->prvMtnOp_nextDate,
                            'typeValidation' => $prvMtnOp->typeValidation,
                        ]);
                        array_push($container,$obj);
                }
            }
       }
        return response()->json($container) ;
    }*/

    /**
     * Function call by EquipementMaintenanceCalendar.vue  with the route : /prvMtnOp/send/revisionDatePassed/{id} (get)
     * Get the preventive maintenance operations validated whose time limit has passed of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the preventive maintenance operations validated
     * @return \Illuminate\Http\Response
     */
    /*public function send_prvMtnOp_from_eq_revisionDatePassed($id) {
        $container=array() ;
        $mostRecentlyEqTmp = EquipmentTemp::all()->where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $prvMtnOps=PreventiveMaintenanceOperation::all()->where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->where('prvMtnOp_validate', '=', "validated")->where('prvMtnOp_reformDate','=',NULL)->get() ;

        $today=Carbon::now() ;
       foreach ($prvMtnOps as $prvMtnOp) {
            if ($prvMtnOp_preventiveOperation){
                if (($prvMtnOp->prvMtnOp_reformDate=='' || $prvMtnOp->prvMtnOp_reformDate===NULL) && $prvMtnOp->prvMtnOp_nextDate<$now ){
                        $obj=([
                            "id" => $prvMtnOp->id,
                            "prvMtnOp_number" => (string)$prvMtnOp->prvMtnOp_number,
                            "prvMtnOp_description" => $prvMtnOp->prvMtnOp_description,
                            "prvMtnOp_protocol" => $prvMtnOp->prvMtnOp_protocol,
                            "prvMtnOp_nextDate" => $prvMtnOp->prvMtnOp_nextDate,
                            'typeValidation' => $prvMtnOp->typeValidation,
                        ]);
                        array_push($container,$obj);
                }
            }
        }
        return response()->json($container) ;
    }*/


    /**
     * Function call by EquipmentPrvMtnOpRlzForm  with the route : /prvMtnOp/send/validated/{id} (get)
     * Get the preventive maintenance operations validated of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the preventive maintenance operations validated
     * @return \Illuminate\Http\Response
     */
    public function send_prvMtnOp_from_eq_validated($id) {
        $container=array() ;
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->where('prvMtnOp_validate', '=', "validated")->where('prvMtnOp_reformDate','=',NULL)->get() ;

       foreach ($prvMtnOps as $prvMtnOp) {
           if ($prvMtnOp->prvMtnOp_reformDate=='' || $prvMtnOp->prvMtnOp_reformDate===NULL){
                if ($prvMtnOp->prvMtnOp_preventiveOperation){
                    $obj=([
                        "id" => $prvMtnOp->id,
                        "prvMtnOp_number" => (string)$prvMtnOp->prvMtnOp_number,
                        "prvMtnOp_description" => $prvMtnOp->prvMtnOp_description,
                        "prvMtnOp_protocol" => $prvMtnOp->prvMtnOp_protocol,
                        "prvMtnOp_nextDate" => $prvMtnOp->prvMtnOp_nextDate,
                        'typeValidation' => $prvMtnOp->typeValidation,
                    ]);
                    array_push($container,$obj);
                }
           }
       }
        return response()->json($container) ;
    }


    /**
     * Function call by EquipmentPrvMtnOpForm.vue when we want to delete a prvMtnOp with the route : /equipment/delete/prvMtnOp/{id}(post)
     * Delete a preventive maintenance operation thanks to the id given in parameter
     * The id parameter correspond to the id of the preventive maintenance operation we want to delete
     * */
    public function delete_prvMtnOp(Request $request, $id){
        $equipment=Equipment::findOrfail($request->eq_id) ;
        //We search the most recently equipment temp of the equipment
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->latest()->first();

        $user=User::findOrFail($request->user_id);
        $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($id) ;

        if (($prvMtnOp->prvMtnOp_validate=="drafted" || $prvMtnOp->prvMtnOp_validate=="to_be_validated") && !$user->user_deleteDataNotValidatedLinkedToEqOrMmeRight){
            return response()->json([
                'errors' => [
                    'prvMtnOp_description' => ["You don't have the user right to delete a preventive maintenance operation save as drafted or in to be validated"]
                ]
            ], 429);
        }
        if ($prvMtnOp->prvMtnOp_validate=="validated" && !$user->user_deleteDataValidatedLinkedToEqOrMmeRight){
            return response()->json([
                'errors' => [
                    'prvMtnOp_description' => ["You don't have the user right to delete a preventive maintenance operation save as validated"]
                ]
            ], 429);
        }
        if ($request->lifesheet_created && !$user->user_deleteDataValidatedLinkedToEqOrMmeRight){
            return response()->json([
                'errors' => [
                    'prvMtnOp_description' => ["You don't have the user right to delete a preventive maintenance operation signed"]
                ]
            ], 429);
        }


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
            'eqTemp_lifeSheetCreated' => false,
            'eqTemp_signatureDate' => NULL,
            ]);

            $states=$mostRecentlyEqTmp->states;
                if ($states!==NULL){
                    $mostRecentlyState=NULL ;
                    $first=true ;
                    foreach($states as $state){
                        if ($first){
                            $mostRecentlyState=$state ;
                            $first=false;
                        }else{
                            $date=$state->created_at ;
                            $date2=$mostRecentlyState->created_at;
                            if ($date>=$date2){
                                $mostRecentlyState=$state ;
                            }
                        }
                    }
                    if ($mostRecentlyState!=NULL){
                        $mostRecentlyState->update([
                            'state_endDate' => Carbon::now('Europe/Paris'),
                        ]);
                    }
                }

            //Creation of a new state
            $newState=State::create([
                'state_remarks' => "Equipment Update (delete prv mtn op) : new version of life sheet created",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_validate' => "validated",
                'state_name' => "Waiting_for_referencing"
            ]) ;

            $newState->equipment_temps()->attach($mostRecentlyEqTmp);
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
        $user=User::findOrFail($request->user_id);
        if (!$user->user_makeReformRight){
            return response()->json([
                'errors' => [
                    'prvMtnOp_reformDate' => ["You don't have the user right to reform a preventive maintenance operation"]
                ]
            ], 429);
        }
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
        ]) ;
    }

}



