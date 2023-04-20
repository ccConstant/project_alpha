<?php

/*
* Filename : MmeUsageController.php 
* Creation date : 21 Jun 2022
* Update date : 9 Feb 2023
* This file is used to link the view files and the database that concern the mme usage table. 
* For example : add a usage for an mme in the data base, update a mme usage, delete it...
*/ 

namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\SW01\MmeTemp ; 
use App\Models\SW01\Mme ; 
use App\Models\SW01\MmeUsage ; 
use App\Models\SW01\EnumUsageMetrologicalLevel;
use App\Models\SW01\Usage ;
use Carbon\Carbon;
use App\Http\Controllers\Controller;



class MmeUsageController extends Controller
{


    /**
     * Function call by MmeUsgForm.vue when the form is submitted for check data with the route :/mme_usage/verif''(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_usage(Request $request){

        //-----CASE usg->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his usage, so he must enter all the attributes
        if ($request->usg_validate=='validated'){
            $this->validate(
                $request,
                [
                    'usg_measurementType' => 'required|min:3|max:255',
                    'usg_precision' => 'required|min:3|max:255',
                    'usg_application' => 'required|min:3|max:255',
                ],
                [
                    'usg_measurementType.required' => 'You must enter a measurement type for your usage ',
                    'usg_measurementType.min' => 'You must enter at least three characters ',
                    'usg_measurementType.max' => 'You must enter a maximum of 255 characters',
                    'usg_precision.required' => 'You must enter a precision for your usage ',
                    'usg_precision.min' => 'You must enter at least three characters ',
                    'usg_precision.max' => 'You must enter a maximum of 255 characters',
                    'usg_application.required' => 'You must enter an application method for your usage ',
                    'usg_application.min' => 'You must enter at least three characters ',
                    'usg_application.max' => 'You must enter a maximum of 255 characters',
                ]
            );
        }else{
             //-----CASE usg->validate=drafted or usg->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations 
            $this->validate(
                $request,
                [
                    'usg_measurementType' => 'max:255',
                    'usg_application' => 'required|min:3|max:255',
                    'usg_precision' => 'max:255',

                ],
                [
                    'usg_application.required' => 'You must enter an application method for your usage ',
                    'usg_application.min' => 'You must enter at least three characters ',
                    'usg_application.max' => 'You must enter a maximum of 255 characters',
                    'usg_measurementType.max' => 'You must enter a maximum of 255 characters',
                    'usg_precision.max' => 'You must enter a maximum of 255 characters',
                ]
            );
        }
    }


    /**
     * Function call by EquipmentUsgForm.vue when the form is submitted for insert with the route : /mme/add/usg/ (post)
     * Add a new enregistrement of mme usage in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new usage
     */
    public function add_usage(Request $request){

        //A usage is linked to its metrologicalLevel. So we need to find the id of the metrologicalLevel choosen by the user and write it in the attribute of the usage.
        //But if no one metrologicalLevel is choosen by the user we define this id to NULL
        // And if the metrologicalLevel choosen is find in the data base the NULL value will be replace by the id value
        $metrologicalLevel_id=NULL ;
        if ($request->usg_metrologicalLevel!=''){
            $metrologicalLevel= EnumUsageMetrologicalLevel::where('value', '=', $request->usg_metrologicalLevel)->first() ;
            $metrologicalLevel_id=$metrologicalLevel->id ; 
        }
        
        $mme=Mme::findOrfail($request->mme_id) ; 
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->orderBy('created_at', 'desc')->first();

        //Creation of a new usage
        $usg=MmeUsage::create([
            'usg_measurementType' => $request->usg_measurementType,
            'usg_validate' => $request->usg_validate,
            'usg_precision' => $request->usg_precision,
            'usg_application' => $request->usg_application,
            'usg_startDate' => Carbon::now('Europe/Paris'),
            'enumUsageMetrologicalLevel_id' => $metrologicalLevel_id,
            'mmeTemp_id' => $mostRecentlyMmeTmp->id,
        ]) ;
    

        $usg_id=$usg->id;
        $id_mme=intval($request->mme_id) ; 
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

              //If the mme temp is validated and a life sheet has been already created, we need to update the mme temp and increase it's version (that's mean another life sheet version) for add usage
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
            return response()->json($usg_id) ; 
        }
    }

    /**
     * Function call by MmeUsgForm.vue when the form is submitted for update with the route :/mme/update/usg/{id} (post)
     * Update an enregistrement of usage in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the usage we want to update
     * */
    public function update_usage(Request $request, $id){

        //A usage is linked to its metrologicalLevel. So we need to find the id of the metrologicalLevel choosen by the user and write it in the attribute of the usage.
        //But if no one metrologicalLevel is choosen by the user we define this id to NULL
        // And if the metrologicalLevel choosen is find in the data base the NULL value will be replace by the id value
        $metrologicalLevel_id=NULL ;
        if ($request->usg_metrologicalLevel!=''){
            $metrologicalLevel= EnumUsageMetrologicalLevel::where('value', '=', $request->usg_metrologicalLevel)->first() ;
            $metrologicalLevel_id=$metrologicalLevel->id ; 
        }

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
            //If the mme temp is validated and a life sheet has been already created, we need to update the mme temp and increase it's version (that's mean another life sheet version) for add usage
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
                
                // In the other case, we can modify the informations without problems
            }
            $usage=MmeUsage::findOrFail($id) ; 
            $usage->update([
                'usg_measurementType' => $request->usg_measurementType,
                'usg_validate' => $request->usg_validate,
                'usg_precision' => $request->usg_precision,
                'usg_application' => $request->usg_application,
                'enumUsageMetrologicalLevel_id' => $metrologicalLevel_id,
                'mmeTemp_id' => $mostRecentlyMmeTmp->id,
            ]);
        }
    }

    /**
     * Function call by ReferenceAUsg.vue with the route : /mme_usage/send/{$id} (get)
     * Get the usages of the mme whose id is passed in parameter
     * The id parameter corresponds to the id of the mme from which we want the usages
     * @return \Illuminate\Http\Response
     */

    public function send_usages($id) {
        $container=array() ; 
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $usages=MmeUsage::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->get();
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
            $metrologicalLevel=NULL;

            if ($usage->enumUsageMetrologicalLevel_id!=NULL){

                $metrologicalLevel_enum= EnumUsageMetrologicalLevel::findOrFail($usage->enumUsageMetrologicalLevel_id)->first() ;
                $metrologicalLevel = $metrologicalLevel_enum->value ;
            }

            $day=$dates[2] ;
            $newDate=$day." ".$month." ".$year ; 

            $obj=([
                'id' => $usage->id,
                'usg_measurementType' => $usage->usg_measurementType,
                'usg_validate' => $usage->usg_validate,
                'usg_precision' => $usage->usg_precision,
                'usg_application' => $usage->usg_application,
                'mmeTemp_id' => $usage->mmeTemp_id,
                'usg_metrologicalLevel' => $metrologicalLevel,
                'usg_startDate' => $newDate,
                'usg_reformDate' => $usage->usg_reformDate,
            ]);
            array_push($container,$obj);
        }
        return response()->json( $container) ;
    }


    /**
     * Function call by MmeUsgForm.vue when we want to delete a usage with the route : /mme/delete/usg{id}(post)
     * Delete a usage thanks to the id given in parameter
     * The id parameter correspond to the id of the usage we want to delete
     * */
    public function delete_usage(Request $request,$id){
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
        $usage=MmeUsage::findOrFail($id);
        $usage->delete() ; 
    }


        /**
     * Function call by MmeUsgForm.vue when we want to reform a usage with the route : add_usage(post)
     * Reform a usage thanks to the id given in parameter
     * The id parameter correspond to the id of the usage we want to reform
     * 
     * */

    public function reform_usage(Request $request, $id){
        $usg=MmeUsage::findOrFail($id) ; 
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
