<?php

/*
* Filename : UsageController.php
* Creation date : 17 May 2022
* Update date : 27 Jun 2023
* This file is used to link the view files and the database that concern the usage table.
* For example : add a usage for an equipment in the data base, update a usage, delete it...
*/



namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ;
use App\Models\SW01\EquipmentTemp ;
use App\Models\SW01\Equipment ;
use App\Models\SW01\Usage ;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\SW01\State;
use App\Models\User ; 



class UsageController extends Controller
{


    /**
     * Function call by EquipmentUsgForm.vue when the form is submitted for check data with the route :/usage/verif''(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_usage(Request $request){

        $user=User::findOrFail($request->user_id);
        if (!$user->user_validateDescriptiveLifeSheetDataRight && $request->usg_validate=="validated"){
            return response()->json([
                'errors' => [
                    'usg_type' => ["You don't have the user right to save an usage as validated"]
                ]
            ], 429);
        }

        if ($request->reason=="update"){
            $usg=Usage::findOrFail($request->usg_id) ;
            if (!$user->user_updateDataInDraftRight && ($usg->usg_validate=="drafted" || $usg->usg_validate=="to_be_validated")){
                return response()->json([
                    'errors' => [
                        'usg_type' => ["You don't have the user right to update an usage save as drafted or in to be validated"]
                    ]
                ], 429);
            }

            if (!$user->user_updateDataValidatedButNotSignedRight && $usg->usg_validate=="validated"){
                return response()->json([
                    'errors' => [
                        'usg_type' => ["You don't have the user right to update an usage save as validated"]
                    ]
                ], 429);
            }
            if (!$user->user_updateDescriptiveLifeSheetDataSignedRight && $request->lifesheet_created==true){
                return response()->json([
                    'errors' => [
                        'usg_type' => ["You don't have the user right to update an usage signed"]
                    ]
                ], 429);
            }
        }

        //-----CASE usg->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his usage, so he must enter all the attributes
        if ($request->usg_validate=='validated'){
            $this->validate(
                $request,
                [
                    'usg_type' => 'required|min:3|max:255',
                    'usg_precaution' => 'required|min:3|max:255',
                ],
                [
                    'usg_type.required' => 'You must enter a type for your usage ',
                    'usg_type.min' => 'You must enter at least three characters ',
                    'usg_type.max' => 'You must enter a maximum of 255 characters',
                    'usg_precaution.required' => 'You must enter a precaution for your usage ',
                    'usg_precaution.min' => 'You must enter at least three characters ',
                    'usg_precaution.max' => 'You must enter a maximum of 255 characters',


                ]
            );
        }else{
             //-----CASE usg->validate=drafted or usg->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations
            $this->validate(
                $request,
                [
                    'usg_type' => 'required|min:3|max:255',
                    'usg_precaution' => 'max:255',
                ],
                [
                    'usg_type.required' => 'You must enter a type for your usage ',
                    'usg_type.min' => 'You must enter at least three characters ',
                    'usg_type.max' => 'You must enter a maximum of 255 characters',
                    'usg_precaution.max' => 'You must enter a maximum of 255 characters',
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
              //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for add usage
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
                    'state_remarks' => "Equipment Update (add usage) : new version of life sheet created",
                    'state_startDate' =>  Carbon::now('Europe/Paris'),
                    'state_validate' => "validated",
                    'state_name' => "Waiting_for_referencing"
                ]) ;

                $newState->equipment_temps()->attach($mostRecentlyEqTmp);
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
            //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for add usage
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
                'state_remarks' => "Equipment Update (update usage) : new version of life sheet created",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_validate' => "validated",
                'state_name' => "Waiting_for_referencing"
            ]) ;

            $newState->equipment_temps()->attach($mostRecentlyEqTmp);

                // In the other case, we can modify the informations without problems
            }
            $usage=Usage::findOrFail($id) ;
            $usage->update([
                'usg_type' => $request->usg_type,
                'usg_validate' => $request->usg_validate,
                'usg_precaution' => $request->usg_precaution,
            ]);
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
    public function delete_usage(Request $request,$id){
        $equipment=Equipment::findOrfail($request->eq_id) ;
        //We search the most recently equipment temp of the equipment
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->latest()->first();
        
        $user=User::findOrFail($request->user_id);
        $usage=Usage::findOrFail($id);

        if (($usage->usg_validate=="drafted" || $usage->usg_validate=="to_be_validated") && !$user->user_deleteDataNotValidatedLinkedToEqOrMmeRight){
            return response()->json([
                'errors' => [
                    'usg_type' => ["You don't have the user right to delete a usage save as drafted or in to be validated"]
                ]
            ], 429);
        }
        if ($usage->usg_validate=="validated" && !$user->user_deleteDataValidatedLinkedToEqOrMmeRight){
            return response()->json([
                'errors' => [
                    'usg_type' => ["You don't have the user right to delete a usage save as validated"]
                ]
            ], 429);
        }
        if ($request->lifesheet_created && !$user->user_deleteDataSignedLinkedToEqOrMmeRight){
            return response()->json([
                'errors' => [
                    'usg_type' => ["You don't have the user right to delete a usage signed"]
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
                'state_remarks' => "Equipment Update (delete usage) : new version of life sheet created",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_validate' => "validated",
                'state_name' => "Waiting_for_referencing"
            ]) ;

            $newState->equipment_temps()->attach($mostRecentlyEqTmp);
        }
        $usage->delete() ; 
    }


        /**
     * Function call by EquipmentUsgForm.vue when we want to reform a usage with the route : /equipment/reform/usg/{id} (post)
     * Reform a usage thanks to the id given in parameter
     * The id parameter correspond to the id of the usage we want to reform
     *
     * */

    public function reform_usage(Request $request, $id){
        $usg=Usage::findOrFail($id) ; 
        $user=User::findOrFail($request->user_id);
        if (!$user->user_makeReformRight){
            return response()->json([
                'errors' => [
                    'usg_reformDate' => ["You don't have the user right to reform a usage"]
                ]
            ], 429);
        }
        if ($request->usg_reformDate<$usg->usg_startDate){
            return response()->json([
                'errors' => [
                    'usg_reformDate' => ["You must entered a reformDate that is after the startDate"]
                ]
            ], 429);

        }

        $oneMonthAgo=Carbon::now()->subMonth(1) ;
        if ($request->usg_reformDate!=NULL && $request->usg_reformDate<$oneMonthAgo){
            return response()->json([
                'errors' => [
                    'usg_reformDate' => ["You can't enter a date that is older than one month"]
                ]
            ], 429);
        }

        $usg->update([
            'usg_reformDate' => $request->usg_reformDate,
        ]) ;
    }


}



