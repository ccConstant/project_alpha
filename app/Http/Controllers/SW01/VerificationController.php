<?php

/*
* Filename : VerificationController.php
* Creation date : 15 Jun 2022
* Update date : 22 Feb 2023
* This file is used to link the view files and the database that concern the verification table.
* For example : add a verification for an mme in the data base, update it, delete it...
*/


namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ;
use App\Models\SW01\MmeTemp ;
use App\Models\SW01\Verification ;
use App\Models\SW01\VerificationRealized ;
use App\Models\SW01\EnumVerificationRequiredSkill ;
use App\Models\SW01\Mme ;
use App\Models\SW01\EnumVerifAcceptanceAuthority;
use Carbon\Carbon;
use App\Models\SW01\MmeState;
use App\Http\Controllers\Controller;

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
            if (!$request->verif_preventiveOperation){
                $this->validate(
                    $request,
                    [
                        'verif_name' => 'required|min:3|max:100',
                        'verif_description' => 'required|min:3|max:255',
                        'verif_expectedResult' => 'required|min:3|max:100',
                        'verif_nonComplianceLimit' => 'required|min:3|max:50',
                        'verif_protocol' => 'required|min:3|max:255',
                        'verif_puttingIntoService' => 'required',
                        'verif_preventiveOperation' => 'required',
                        'verif_mesureUncert' => 'required',
                        'verif_mesureRange' => 'required',
                    ],
                    [
                        'verif_puttingIntoService.required' => 'You must enter if the verification is realized during the comissioning',
                        'verif_preventiveOperation.required' => 'You must enter if the verification is realized regularly?',
                        'verif_name.required' => 'You must enter a name for your verification',
                        'verif_name.min' => 'You must enter at least three characters ',
                        'verif_name.max' => 'You must enter a maximum of 100 characters',

                        'verif_description.required' => 'You must enter a description for verification',
                        'verif_description.min' => 'You must enter at least three characters ',
                        'verif_description.max' => 'You must enter a maximum of 255 characters',

                        'verif_expectedResult.required' => 'You must enter an expectedResult for your verification',
                        'verif_expectedResult.min' => 'You must enter at least three character ',
                        'verif_expectedResult.max' => 'You must enter a maximum of 100 characters',

                        'verif_nonComplianceLimit.required' => 'You must enter a nonComplianceLimit for your verification',
                        'verif_nonComplianceLimit.min' => 'You must enter at least three character ',
                        'verif_nonComplianceLimit.max' => 'You must enter a maximum of 50 characters',

                        'verif_protocol.required' => 'You must enter a protocol for your verification',
                        'verif_protocol.min' => 'You must enter at least three characters ',
                        'verif_protocol.max' => 'You must enter a maximum of 255 characters ',

                        'verif_mesureUncert.required' => 'You must enter a mesureUncert for your verification',

                        'verif_mesureRange.required' => 'You must enter a mesureRange for your verification',
                    ]
                );

                //verification about verif_requiredSkill, if no one value is selected we need to alert the user
                if ($request->verif_requiredSkill=='' || $request->verif_requiredSkill==NULL){
                    return response()->json([
                        'errors' => [
                            'verif_requiredSkill' => ["You must choose a required skill"]
                        ]
                    ], 429);
                }
                //verification about verif_verifAcceptanceAuthority, if no one value is selected we need to alert the user
                if ($request->verif_verifAcceptanceAuthority=='' || $request->verif_verifAcceptanceAuthority==NULL ){
                    return response()->json([
                        'errors' => [
                            'verif_verifAcceptanceAuthority' => ["You must choose a verif acceptance authority"]
                        ]
                    ], 429);
                }
            }else{
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
                        'verif_puttingIntoService' => 'required',
                        'verif_preventiveOperation' => 'required',
                        'verif_mesureUncert' => 'required',
                        'verif_mesureRange' => 'required',
                    ],
                    [
                        'verif_puttingIntoService.required' => 'You must enter if the verification is realized during the comissioning',
                        'verif_preventiveOperation.required' => 'You must enter if the verification is realized regularly?',
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

                        'verif_mesureUncert.required' => 'You must enter a mesureUncert for your verification',

                        'verif_mesureRange.required' => 'You must enter a mesureRange for your verification',
                    ]
                );

                //verification about verif_requiredSkill, if no one value is selected we need to alert the user
                if ($request->verif_requiredSkill=='' || $request->verif_requiredSkill==NULL){
                    return response()->json([
                        'errors' => [
                            'verif_requiredSkill' => ["You must choose a required skill"]
                        ]
                    ], 429);
                }
                //verification about verif_verifAcceptanceAuthority, if no one value is selected we need to alert the user
                if ($request->verif_verifAcceptanceAuthority=='' || $request->verif_verifAcceptanceAuthority==NULL ){
                    return response()->json([
                        'errors' => [
                            'verif_verifAcceptanceAuthority' => ["You must choose a verif acceptance authority"]
                        ]
                    ], 429);
                }
            }
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
        //A verification is linked to its verifAcceptanceAuthority. So we need to find the id of the verifAcceptanceAuthority choosen by the user and write it in the attribute of the verification.
        //But if no one verif acceptance authority is choosen by the user we define this id to NULL
        // And if the verifAcceptanceAuthority choosen is find in the data base the NULL value will be replace by the id value
        $verifAcceptanceAuthority_id=NULL ;
        if ($request->verif_verifAcceptanceAuthority!=''){
            $verifAcceptanceAuthority= EnumVerifAcceptanceAuthority::where('value', '=', $request->verif_verifAcceptanceAuthority)->first() ;
            $verifAcceptanceAuthority_id=$verifAcceptanceAuthority->id ;
        }
        //A verification is linked to its required skill. So we need to find the id of the required skill choosen by the user and write it in the attribute of the verification.
        //But if no one required skill is choosen by the user we define this id to NULL
        // And if the required skill choosen is find in the data base the NULL value will be replace by the id value
        $requiredSkill_id=NULL ;
        if ($request->verif_requiredSkill!=''){
            $requiredSkill= EnumVerificationRequiredSkill::where('value', '=', $request->verif_requiredSkill)->first() ;
            $requiredSkill_id=$requiredSkill->id ;
        }

        $id_mme=intval($request->mme_id) ;
        $mme=Mme::findOrfail($request->mme_id) ;
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->orderBy('created_at', 'desc')->first();
        $verifInMmes=Verification::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->get();
        $max_number=1 ;
        if (count($verifInMmes)!=0){
            foreach ($verifInMmes as $verifInMme){
                $number=intval($verifInMme->verif_number) ;
                if ($number>$max_number){
                    $max_number=$verifInMme->verif_number ;
                }
            }
            $max_number=$max_number+1 ;
        }
        $nextDate=NULL ;
        if ($request->verif_preventiveOperation){
            $startDate=Carbon::now('Europe/Paris');
            if ($request->verif_symbolPeriodicity!='' && $request->verif_symbolPeriodicity!=NULL && $request->verif_periodicity!='' && $request->verif_periodicity!=NULL ){
                $nextDate=Carbon::create($startDate->year, $startDate->month, $startDate->day, $startDate->hour, $startDate->minute, $startDate->second);

                if ($request->verif_symbolPeriodicity=='Y'){
                    $nextDate->addYears($request->verif_periodicity) ;
                }

                if ($request->verif_symbolPeriodicity=='M'){
                    $nextDate->addMonths($request->verif_periodicity) ;
                }

                if ($request->verif_symbolPeriodicity=='D'){
                    $nextDate->addDays($request->verif_periodicity) ;
                }

                if ($request->verif_symbolPeriodicity=='H'){
                    $nextDate->addHours($request->verif_periodicity) ;
                }
            }
        }

        //Creation of a new verification
        $verif=Verification::create([
            'verif_number' => $max_number,
            'verif_name' => $request->verif_name,
            'verif_expectedResult' => $request->verif_expectedResult,
            'verif_nonComplianceLimit' => $request->verif_nonComplianceLimit,
            'verif_description' => $request->verif_description,
            'verif_periodicity' => $request->verif_periodicity,
            'verif_symbolPeriodicity' => $request->verif_symbolPeriodicity,
            'verif_protocol' => $request->verif_protocol,
            'verif_startDate' => Carbon::now('Europe/Paris'),
            'verif_nextDate' => $nextDate,
            'verif_validate' => $request->verif_validate,
            'enumRequiredSkill_id' => $requiredSkill_id,
            'enumVerifAcceptanceAuthority_id' => $verifAcceptanceAuthority_id,
            'mmeTemp_id' => $mostRecentlyMmeTmp->id,
            'verif_puttingIntoService' => $request->verif_puttingIntoService,
            'verif_preventiveOperation' => $request->verif_preventiveOperation,
            'verif_mesureUncert' => $request->verif_mesureUncert,
            'verif_mesureRange' => $request->verif_mesureRange,
        ]) ;

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

        $verif_id=$verif->id;
        if ($mostRecentlyMmeTmp!=NULL){
             //If the mme temp is validated and a life sheet has been already created, we need to update the mme temp and increase it's version (that's mean another life sheet version) for add verif
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

                $states=$mostRecentlyMmeTmp->states;
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
                $newState=MmeState::create([
                    'state_remarks' => "MME Update (add verif) : new version of life sheet created",
                    'state_startDate' =>  Carbon::now('Europe/Paris'),
                    'state_validate' => "validated",
                    'state_name' => "Waiting_for_referencing"
                ]) ;

                $newState->mme_temps()->attach($mostRecentlyMmeTmp);
            }
        }
        return response()->json($verif_id) ;
    }


    /**
     * Function call by MmeVerifForm.vue when the form is submitted for update with the route :/mme/update/verif/{id} (post)
     * Update an enregistrement of verification in the data base with the informations entered in the form
     * The id parameter correspond to the id of the verification we want to update
     * */
    public function update_verif(Request $request, $id){
        //A verification is linked to its verifAcceptanceAuthority. So we need to find the id of the verifAcceptanceAuthority choosen by the user and write it in the attribute of the verification.
        //But if no one verif acceptance authority is choosen by the user we define this id to NULL
        // And if the verifAcceptanceAuthority choosen is find in the data base the NULL value will be replace by the id value
        $verifAcceptanceAuthority_id=NULL ;
        if ($request->verif_verifAcceptanceAuthority!=''){
            $verifAcceptanceAuthority= EnumVerifAcceptanceAuthority::where('value', '=', $request->verif_verifAcceptanceAuthority)->first() ;
            $verifAcceptanceAuthority_id=$verifAcceptanceAuthority->id ;
        }
        //A verification is linked to its required skill. So we need to find the id of the required skill choosen by the user and write it in the attribute of the verification.
        //But if no one required skill is choosen by the user we define this id to NULL
        // And if the required skill choosen is find in the data base the NULL value will be replace by the id value
        $requiredSkill_id=NULL ;
        if ($request->verif_requiredSkill!=''){
            $requiredSkill= EnumVerificationRequiredSkill::where('value', '=', $request->verif_requiredSkill)->first() ;
            $requiredSkill_id=$requiredSkill->id ;
        }


        $mme=Mme::findOrfail($request->mme_id) ;
        $oldVerif=Verification::findOrFail($id) ;
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
           //If the mme temp is validated and a life sheet has been already created, we need to update the mme temp and increase it's version (that's mean another life sheet version) for update verification
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

               $states=$mostRecentlyMmeTmp->states;
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
                $newState=MmeState::create([
                    'state_remarks' => "MME Update (update verif) : new version of life sheet created",
                    'state_startDate' =>  Carbon::now('Europe/Paris'),
                    'state_validate' => "validated",
                    'state_name' => "Waiting_for_referencing"
                ]) ;

                $newState->mme_temps()->attach($mostRecentlyMmeTmp);
            }

            if ($request->verif_preventiveOperation){
                if ($request->verif_periodicity!=NULL && $request->verif_symbolPeriodicity!=NULL && ($oldVerif->verif_periodicity!=$request->verif_periodicity || $oldVerif->verif_symbolPeriodicity!=$request->verif_symbolPeriodicity || $oldVerif->verif_preventiveOperation!=$request->verif_preventiveOperation)){

                    $dates=explode(' ', $oldVerif->verif_startDate) ;
                    $ymd=explode('-', $dates[0]);
                    $year=$ymd[0] ;
                    $month=$ymd[1] ;
                    $day=$ymd[2] ;

                    $time=explode(':', $dates[1]);
                    $hour=$time[0] ;
                    $min=$time[1] ;
                    $sec=$time[2] ;

                    $nextDate=Carbon::create($year, $month, $day, $hour, $min, $sec);

                    if ($request->verif_symbolPeriodicity=='Y'){
                        $nextDate->addYears($request->verif_periodicity) ;
                    }

                    if ($request->verif_symbolPeriodicity=='M'){
                        $nextDate->addMonths($request->verif_periodicity) ;
                    }

                    if ($request->verif_symbolPeriodicity=='D'){
                        $nextDate->addDays($request->verif_periodicity) ;
                    }
                    if ($request->verif_symbolPeriodicity=='H'){
                        $nextDate->addHours($request->verif_periodicity) ;
                    }
                    $oldVerif->update([
                        'verif_nextDate' => $nextDate,
                    ]);
                }
            }

            $oldVerif->update([
                'verif_name' => $request->verif_name,
                'verif_expectedResult' => $request->verif_expectedResult,
                'verif_nonComplianceLimit' => $request->verif_nonComplianceLimit,
                'verif_description' => $request->verif_description,
                'verif_periodicity' => $request->verif_periodicity,
                'verif_symbolPeriodicity' => $request->verif_symbolPeriodicity,
                'verif_protocol' => $request->verif_protocol,
                'verif_validate' => $request->verif_validate,
                'enumRequiredSkill_id' => $requiredSkill_id,
                'mmeTemp_id' => $mostRecentlyMmeTmp->id,
                'enumVerifAcceptanceAuthority_id' => $verifAcceptanceAuthority_id,
                'verif_puttingIntoService' => $request->verif_puttingIntoService,
                'verif_preventiveOperation' => $request->verif_preventiveOperation,
                'verif_mesureUncert' => $request->verif_mesureUncert,
                'verif_mesureRange' => $request->verif_mesureRange,
            ]) ;

        }
    }

    /**
     * Function call by ConsultationLifeSheetPdf.vue with the route : /verifs/send/lifesheet/{id} (get)
     * Get the verifications of the mme whose id is passed in parameter for the lifesheet
     * The id parameter corresponds to the id of the mme from which we want the verifications
     * @return \Illuminate\Http\Response
     */

    public function send_verifs_lifesheet($id) {
        $container=array() ;
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id)->first();
        $verifs=Verification::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->get() ;
       foreach ($verifs as $verif) {

            $requiredSkill=NULL;
            if ($verif->enumRequiredSkill_id!=NULL){
                $requiredSkillEnum = EnumVerificationRequiredSkill::findOrFail($verif->enumRequiredSkill_id) ;
                $requiredSkill=$requiredSkillEnum->value ;
            }
            $verifAcceptanceAuthority = NULL ;
            if ($verif->enumVerifAcceptanceAuthority_id!=NULL){
                $verifAcceptanceAuthority_enum= EnumVerifAcceptanceAuthority::findOrFail($verif->enumVerifAcceptanceAuthority_id)->first() ;
                $verifAcceptanceAuthority = $verifAcceptanceAuthority_enum->value ;
            }

            $verifPuttingIntoService = NULL ;
            if ($verif->verif_puttingIntoService==1){
                $verifPuttingIntoService = 'Yes' ;
            }else{
                $verifPuttingIntoService = 'No' ;
            }

            $verifPreventiveOperation = NULL ;
            if ($verif->verif_preventiveOperation==1){
                $verifPreventiveOperation = 'Yes' ;
            }else{
                $verifPreventiveOperation = 'No' ;
            }

            $reformed="no" ;
            if ($verif->verif_reformDate!=NULL){
                $reformed="yes" ;
            }

            $obj=([
                "id" => $verif->id,
                "verif_number" => (string)$verif->verif_number,
                "verif_description" => $verif->verif_description,
                "verif_periodicity" => (string)$verif->verif_periodicity,
                "verif_symbolPeriodicity" => $verif->verif_symbolPeriodicity,
                "verif_protocol" => $verif->verif_protocol,
                "verif_startDate" => $verif->verif_startDate,
                "verif_nextDate" => $verif->verif_nextDate,
                "verif_reformDate" => $verif->verif_reformDate,
                'verif_name' => $verif->verif_name,
                'verif_expectedResult' => $verif->verif_expectedResult,
                'verif_nonComplianceLimit' => $verif->verif_nonComplianceLimit,
                'verif_requiredSkill' => $requiredSkill,
                'verif_verifAcceptanceAuthority' => $verifAcceptanceAuthority,
                'verif_validate' => $verif->verif_validate,
                'verif_puttingIntoService'=> $verifPuttingIntoService,
                'verif_preventiveOperation'=> $verifPreventiveOperation,
                'verif_reformed' => $reformed,
                'verif_mesureUncert' => $verif->verif_mesureUncert,
                'verif_mesureRange' => $verif->verif_mesureRange,
            ]);
            array_push($container,$obj);
       }
        return response()->json($container) ;
    }


    /**
     * Function call by ReferenceAVerif.vue with the route : /verifs/send/{id} (get)
     * Get the verifications of the mme whose id is passed in parameter
     * The id parameter corresponds to the id of the mme from which we want the verifications
     * @return \Illuminate\Http\Response
     */

    public function send_verifs($id) {
        $container=array() ;
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id)->latest()->first();
        $verifs=Verification::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->get() ;
       foreach ($verifs as $verif) {

            $requiredSkill=NULL;
            if ($verif->enumRequiredSkill_id!=NULL){
                $requiredSkillEnum = EnumVerificationRequiredSkill::findOrFail($verif->enumRequiredSkill_id) ;
                $requiredSkill=$requiredSkillEnum->value ;
            }

            $verifAcceptanceAuthority = NULL ;
            if ($verif->enumVerifAcceptanceAuthority_id!=NULL){
                $verifAcceptanceAuthority_enum= EnumVerifAcceptanceAuthority::findOrFail($verif->enumVerifAcceptanceAuthority_id) ;
                $verifAcceptanceAuthority = $verifAcceptanceAuthority_enum->value ;
            }
            $obj=([
                "id" => $verif->id,
                "verif_number" => (string)$verif->verif_number,
                "verif_description" => $verif->verif_description,
                "verif_periodicity" => (string)$verif->verif_periodicity,
                "verif_symbolPeriodicity" => $verif->verif_symbolPeriodicity,
                "verif_protocol" => $verif->verif_protocol,
                "verif_startDate" => $verif->verif_startDate,
                "verif_nextDate" => $verif->verif_nextDate,
                "verif_reformDate" => $verif->verif_reformDate,
                'verif_name' => $verif->verif_name,
                'verif_expectedResult' => $verif->verif_expectedResult,
                'verif_nonComplianceLimit' => $verif->verif_nonComplianceLimit,
                'verif_requiredSkill' => $requiredSkill,
                'verif_verifAcceptanceAuthority' => $verifAcceptanceAuthority,
                'verif_validate' => $verif->verif_validate,
                'verif_puttingIntoService'=> (boolean)$verif->verif_puttingIntoService,
                'verif_preventiveOperation'=> (boolean)$verif->verif_preventiveOperation,
                'verif_mesureUncert' => $verif->verif_mesureUncert,
                'verif_mesureRange' => $verif->verif_mesureRange,
            ]);
            array_push($container,$obj);
       }
        return response()->json($container) ;
    }

     /**
     * Function call by ReferenceAVerif.vue with the route : /verif/send/{id} (get)
     * Get the informations of the verification whose id is passed in parameter
     * The id parameter corresponds to the id of the verification from which we want the informations
     * @return \Illuminate\Http\Response
     */

    public function send_verif($id) {
        $container=array() ;
        $verif=Verification::findOrFail($id) ;
        $requiredSkill=NULL;
        if ($verif->enumRequiredSkill_id!=NULL){
            $requiredSkillEnum = EnumVerificationRequiredSkill::findOrFail($verif->enumRequiredSkill_id) ;
            $requiredSkill=$requiredSkillEnum->value ;
        }

        $verifAcceptanceAuthority = NULL ;
            if ($verif->enumVerifAcceptanceAuthority_id!=NULL){
                $verifAcceptanceAuthority_enum= EnumVerifAcceptanceAuthority::findOrFail($verif->enumVerifAcceptanceAuthority_id)->first() ;
                $verifAcceptanceAuthority = $verifAcceptanceAuthority_enum->value ;
            }
        $obj=([
            "id" => $verif->id,
            "verif_number" => (string)$verif->verif_number,
            "verif_description" => $verif->verif_description,
            "verif_periodicity" => (string)$verif->verif_periodicity,
            "verif_symbolPeriodicity" => $verif->verif_symbolPeriodicity,
            "verif_protocol" => $verif->verif_protocol,
            "verif_startDate" => $verif->verif_startDate,
            "verif_nextDate" => $verif->verif_nextDate,
            "verif_reformDate" => $verif->verif_reformDate,
            'verif_name' => $verif->verif_name,
            'verif_expectedResult' => $verif->verif_expectedResult,
            'verif_nonComplianceLimit' => $verif->verif_nonComplianceLimit,
            'verif_requiredSkill' => $requiredSkill,
            'verif_verifAcceptanceAuthority' => $verifAcceptanceAuthority,
            'verif_validate' => $verif->verif_validate,
            'verif_puttingIntoService'=> (boolean)$verif->verif_puttingIntoService,
            'verif_preventiveOperation'=> (boolean)$verif->verif_preventiveOperation,
            'verif_mesureUncert' => $verif->verif_mesureUncert,
            'verif_mesureRange' => $verif->verif_mesureRange,

        ]);
        array_push($container,$obj);
        return response()->json($container) ;
    }


    /**
     * Function call by MmeVerifRlzForm  with the route : /verif/send/validated/{id} (get)
     * Get the verifications validated of the mme whose id is passed in parameter
     * The id parameter corresponds to the id of the mme from which we want the verifications validated
     * @return \Illuminate\Http\Response
     */
    public function send_verif_from_mme_validated($id) {
        $container=array() ;
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $verifs=Verification::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->where('verif_validate', '=', "validated")->get() ;

       foreach ($verifs as $verif) {
           if ($verif->verif_reformDate=='' || $verif->verif_reformDate===NULL){

            $requiredSkill=NULL;
            if ($verif->enumRequiredSkill_id!=NULL){
                $requiredSkillEnum = EnumVerificationRequiredSkill::findOrFail($verif->enumRequiredSkill_id) ;
                $requiredSkill=$requiredSkillEnum->value ;
            }

            $verifAcceptanceAuthority = NULL ;
            if ($verif->enumVerifAcceptanceAuthority_id!=NULL){
                $verifAcceptanceAuthority_enum= EnumVerifAcceptanceAuthority::findOrFail($verif->enumVerifAcceptanceAuthority_id)->first() ;
                $verifAcceptanceAuthority = $verifAcceptanceAuthority_enum->value ;
            }
                $obj=([
                    "id" => $verif->id,
                    "verif_number" => (string)$verif->verif_number,
                    "verif_description" => $verif->verif_description,
                    "verif_periodicity" => (string)$verif->verif_periodicity,
                    "verif_symbolPeriodicity" => $verif->verif_symbolPeriodicity,
                    "verif_protocol" => $verif->verif_protocol,
                    "verif_startDate" => $verif->verif_startDate,
                    "verif_nextDate" => $verif->verif_nextDate,
                    "verif_reformDate" => $verif->verif_reformDate,
                    'verif_name' => $verif->verif_name,
                    'verif_expectedResult' => $verif->verif_expectedResult,
                    'verif_nonComplianceLimit' => $verif->verif_nonComplianceLimit,
                    'verif_requiredSkill' => $requiredSkill,
                    'verif_verifAcceptanceAuthority' => $verifAcceptanceAuthority,
                    'verif_validate' => $verif->verif_validate,
                    'verif_puttingIntoService'=> (boolean)$verif->verif_puttingIntoService,
                    'verif_preventiveOperation' => (boolean)$verif->verif_preventiveOperation,
                    'verif_mesureUncert' => $verif->verif_mesureUncert,
                    'verif_mesureRange' => $verif->verif_mesureRange,
                ]);
                array_push($container,$obj);
           }
       }
        return response()->json($container) ;
    }

    /**
     * Function call by MmeVerifForm.vue when we want to delete a verification with the route : /mme/delete/verif/{id}(post)
     * Delete a verif thanks to the id given in parameter
     * The id parameter correspond to the id of the verification we want to delete
     * */
    public function delete_verif(Request $request, $id){
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
        //If the mme temp is validated and a life sheet has been already created, we need to update the mme temp and increase it's version (that's mean another life sheet version) for update verification
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

            $states=$mostRecentlyMmeTmp->states;
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
            $newState=MmeState::create([
                'state_remarks' => "MME Update (delete verification) : new version of life sheet created",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_validate' => "validated",
                'state_name' => "Waiting_for_referencing"
            ]) ;

            $newState->mme_temps()->attach($mostRecentlyMmeTmp);
        }

        $verifsInMme=Verification::where('mmeTemp_id', '=', $request->mme_id)->get() ;
        $verifRlzs=VerificationRealized::where('verif_id', '=', $id)->get() ;
        $verif=Verification::findOrFail($id) ;
        if (count($verifRlzs)==0){
            foreach($verifsInMme as $verifInMme){
                if ($verifInMme->verif_number>$verif->verif_number){
                    $verifInMme->verif_number=$verifInMme->verif_number-1 ;
                    $verifInDB=Verification::findOrFail($verifInMme->id) ;
                    $verifInDB->update([
                        'verif_number' =>  $verifInMme->verif_number,
                    ]);
                }
            }
            $verif->delete() ;
        }else{
            return response()->json([
                'errors' => [
                    'verif_delete' => ["You can't delete a verification that is already realized"]
                ]
            ], 429);
        }
    }

        /**
     * Function call by ReferenceAVerif.vue when we want to reform a verif with the route : '/mme/reform/verif/{id} (post)
     * Reform a verif thanks to the id given in parameter
     * The id parameter correspond to the id of the verif we want to reform
     *
     * */

    public function reform_verif(Request $request, $id){
        $verif=Verification::findOrFail($id) ;
        if ($request->verif_reformDate<$verif->verif_startDate){
            return response()->json([
                'errors' => [
                    'verif_reformDate' => ["You must entered a reformDate that is after the startDate"]
                ]
            ], 429);
        }

        $oneMonthAgo=Carbon::now()->subMonth(1) ;
        if ($request->verif_reformDate!=NULL && $request->verif_reformDate<$oneMonthAgo){
            return response()->json([
                'errors' => [
                    'verif_reformDate' => ["You can't enter a date that is older than one month"]
                ]
            ], 429);
        }

        $verif->update([
            'verif_reformDate' => $request->verif_reformDate,
        ]) ;
    }

}


