<?php

/*
* Filename : MmeController.php 
* Creation date : 14 Jun 2022
* Update date : 14 Jun 2022
* This file is used to link the view files and the database that concern the mme table. 
* For example : add the identity card of an mme in the database, update the identity card, delete the identity card... 
*/ 

namespace App\Http\Controllers;

use Illuminate\Http\Request ; 
use Illuminate\Support\Facades\DB ; 
use App\Models\Mme;
use App\Models\File;
use App\Models\MmeUsage;
use App\Models\Verification;
use App\Models\MmeTemp;
use App\Models\MmeState;
use Carbon\Carbon;

class MmeController extends Controller{

    /**
     * Function call by ListOfMme.vue with the route : /mme/mmes (get)
     * Get all the internalReference and the id of mme in the data base for print them in the vue
     * @return \Illuminate\Http\Response
     */

    public function send_internalReferences_ids (){
        $mmes= Mme::all() ;
        $container=array() ; 
        foreach($mmes as $mme){
            $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme->id)->orderBy('created_at', 'desc')->first();
            $states=$mostRecentlyMmeTmp->states;
            $mostRecentlyState=MmeState::orderBy('created_at', 'asc')->first();
            foreach($states as $state){
                $date=$state->created_at ; 
                $date2=$mostRecentlyState->created_at;
               if ($date>=$date2){
                     $mostRecentlyState=$state ; 
                }
            }
            $isAlreadyQualityValidated=false ; 
            if ($mostRecentlyMmeTmp->qualityVerifier_id!=NULL){
                $isAlreadyQualityValidated=true ; 
            }

            $isAlreadyTechnicalValidated=false ; 
            if ($mostRecentlyMmeTmp->technicalVerifier_id!=NULL){
                $isAlreadyTechnicalValidated=true ; 
            }
        
            $obj=([
                'id' => $mme->id,
                'mme_internalReference' => $mme->mme_internalReference,
                'mme_state' =>  $mostRecentlyState->state_name,
                'state_id' => $mostRecentlyState->id,
                'mmeTemp_lifeSheetCreated' => $mostRecentlyMmeTmp->mmeTemp_lifeSheetCreated,
                'alreadyValidatedQuality' =>$isAlreadyQualityValidated,
                'alreadyValidatedTechnical' =>$isAlreadyTechnicalValidated,
                'mme_version' => $mostRecentlyMmeTmp->mmeTemp_version,
            ]);
            array_push($container,$obj);
        }
        return response()->json($container) ;
    }


     /**
     * Function call by ImportationModal.vue with the route : /mmes/same_set/{$set} (get)
     * Get the MMEs with the same set as the one in parameters
     * The set in parameter correspond of the set of mme we actually create : this set allow us to import many characteritics from another mme if the set is the same 
     * @return \Illuminate\Http\Response
     */

    public function send_mmes_same_set($set){
        $mmes_same_set=Mme::where('mme_set', $set)->get();
        return response()->json($mmes_same_set) ;    
    }

    /**
     * Function call by MmeConsult.vue with the route : /mme/{id} (get)
     * Get mme corresponding to the id in the data base for print it in the vue
     * The id parameter corresponds to the id of the mme from which we want the informations (internalReference, externalReference...)
     * @return \Illuminate\Http\Response
     */

    public function send_mme ($id){
        $mme= Mme::findOrFail($id) ;
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $validate=NULL ; 
        if ($mostRecentlyMmeTmp!=NULL){

            $states=$mostRecentlyMmeTmp->states;
            $mostRecentlyState=MmeState::orderBy('created_at', 'asc')->first();
            foreach($states as $state){
                $date=$state->created_at ; 
                $date2=$mostRecentlyState->created_at;
               if ($date>=$date2){
                     $mostRecentlyState=$state ; 
                }
            }
            $validate=$mostRecentlyMmeTmp->mmeTemp_validate ; 
            $remarks=$mostRecentlyMmeTmp->mmeTemp_remarks ;
        }
        return response()->json([
            'mme_internalReference' => $mme->mme_internalReference,
            'mme_externalReference' => $mme->mme_externalReference,
            'mme_name' => $mme->mme_name,
            'mme_serialNumber' => $mme->mme_serialNumber,
            'mme_constructor'  => $mme->mme_constructor,
            'mme_remarks'  => $remarks,
            'mme_set'  => $mme->mme_set,
            'mme_validate' => $validate,
        ]);
    }


     /**
     * Function call by MmeIDForm.vue with the route : /mme/sets (get)
     * Get all the differents sets in the data base and send them to the vue 
     * @return \Illuminate\Http\Response
     */

    public function send_sets (){
        $sets=DB::select(DB::raw('SELECT DISTINCT mme_set FROM mmes'));
        return response()->json($sets) ;
    }


    /**
     * Function call by MmeIDForm.vue when the form is submitted for check data with the route : /mme/add (post)
     * Check the informations entered in the form and send the errors if it exists
     * @return \Illuminate\Http\Response
     */
    public function verif_mme(Request $request){

        // We need to do many verifications on the data entered by the user.
        // If the user make a mistake, we send to the vue an error to explain it and print it to the user.


        //-----CASE mme->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his mme, so he must enter all the attributes
        if ($request->mme_validate=='validated'){
            $this->validate(
                $request,
                [
                    'mme_internalReference' => 'required|min:3|max:16',
                    'mme_externalReference' => 'required|min:3|max:100',
                    'mme_name'  => 'required|min:3|max:100', 
                    'mme_serialNumber'  => 'required|min:3|max:50',
                    'mme_constructor'  => 'required|min:3|max:30',
                    'mme_remarks'  => 'required|min:3|max:400',
                    'mme_set'  => 'required|min:1|max:20',
                ],
                [
                    'mme_internalReference.required' => 'You must enter an internal reference ',
                    'mme_internalReference.min' => 'You must enter at least 3 characters ',
                    'mme_internalReference.max' => 'You must enter a maximum of 16 characters',

                    'mme_externalReference.required' => 'You must enter an external reference',
                    'mme_externalReference.min' => 'You must enter at least 3 characters ',
                    'mme_externalReference.max' => 'You must enter a maximum of 100 characters',

                    'mme_name.required' => 'You must enter a name',
                    'mme_name.min' => 'You must enter at least 3 characters ',
                    'mme_name.max' => 'You must enter a maximum of 100 characters',

                    'mme_serialNumber.required'  => 'You must enter a serial number', 
                    'mme_serialNumber.min'  => 'You must enter at least 3 characters ',
                    'mme_serialNumber.max'  =>  'You must enter a maximum of 50 characters',

                    'mme_constructor.required'  => 'You must enter a constructor', 
                    'mme_constructor.min'  => 'You must enter at least 3 characters ',
                    'mme_constructor.max'  =>  'You must enter a maximum of 30 characters',

                    'mme_remarks.required'  => 'You must enter a remark',
                    'mme_remarks.min'  =>  'You must enter at least 3 characters ',
                    'mme_remarks.max'  => 'You must enter a maximum of 400 characters',

                    'mme_set.required'  => 'You must enter a set',
                    'mme_set.min'  => 'You must enter at least 1 characters ',
                    'mme_set.max'  => 'You must enter a maximum of 20 characters',
                     
                ]
            );
        }else{
             //-----CASE mme->validate=drafted or mme->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" value he must enter only the internReference and externReference
            $this->validate(
                $request,
                [
                    'mme_internalReference' => 'required|min:3|max:16',
                    'mme_externalReference' => 'required|min:3|max:100',
                    'mme_name'  => 'max:100', 
                    'mme_serialNumber'  => 'max:50',
                    'mme_constructor'  => 'max:30',
                    'mme_remarks'  => 'max:400',
                    'mme_set'  => 'max:20',
                ],
                [
                    
                    'mme_internalReference.required' => 'You must enter an internal reference ',
                    'mme_internalReference.min' => 'You must enter at least 3 characters ',
                    'mme_internalReference.max' => 'You must enter a maximum of 16 characters',

                    'mme_externalReference.required' => 'You must enter an external reference',
                    'mme_externalReference.min' => 'You must enter at least 3 characters ',
                    'mme_externalReference.max' => 'You must enter a maximum of 100 characters',

                    'mme_name.max' => 'You must enter a maximum of 100 characters',
                    'mme_serialNumber.max'  =>  'You must enter a maximum of 50 characters',
                    'mme_constructor.max'  =>  'You must enter a maximum of 30 characters',
                    'mme_remarks.max'  => 'You must enter a maximum of 400 characters',
                    'mme_set.max'  => 'You must enter a maximum of 20 characters',
                ]
            );
        }
        
        if ($request->reason=="update"){
            //we checked if the internal reference entered is already used for another mme
            $mme_already_exist=Mme::where('mme_internalReference', '=', $request->mme_internalReference, 'and')->where('id', '<>', $request->mme_id)->first() ; 
            if ($mme_already_exist!=NULL){
                return response()->json([
                    'errors' => [
                        'mme_internalReference' => ["This internal reference is already use for another mme"]
                    ]
                ], 429);
            }

            //We search the most recently mme temp of the mme 
            $mme= Mme::findOrFail($request->mme_id) ;
            $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->orderBy('created_at', 'desc')->first();
            if ($mostRecentlyMmeTmp!=NULL){
                //we checked if a you have already validated the id card, ff it's the case we can't update the internalReference
                if ($mostRecentlyMmeTmp->mmeTemp_validate=="validated"){                
                    if($mme->mme_internalReference!=$request->mme_internalReference){
                        return response()->json([
                            'errors' => [
                                'mme_internalReference' => ["You can't modify the internal reference because you have already validated the id card "]
                            ]
                        ], 429);
                    }
                    //we checked if a life sheet is already created. If it's the case we can't update the external reference, the name, the constructor (...)
                    if ($mostRecentlyMmeTmp->mmeTemp_lifeSheetCreated==true){
                        if($mme->mme_externalReference!=$request->mme_externalReference){
                            return response()->json([
                                'errors' => [
                                    'mme_externalReference' => ["You can't modify the external reference because you have already validated the id card"]
                                ]
                            ], 429);
                        }
                        if($mme->mme_name!=$request->mme_name){
                            return response()->json([
                                'errors' => [
                                    'mme_name' => ["You can't modify the name because a life sheet has already been created"]
                                ]
                            ], 429);
                        }
                        if($mme->mme_serialNumber!=$request->mme_serialNumber){
                            return response()->json([
                                'errors' => [
                                    'mme_serialNumber' => ["You can't modify the serial number because a life sheet has already been created"]
                                ]
                            ], 429);
                        }
                        if($mme->mme_constructor!=$request->mme_constructor){
                            return response()->json([
                                'errors' => [
                                    'mme_constructor' => ["You can't modify the constructor because a life sheet has already been created"]
                                ]
                            ], 429);
                        }

                        if($mme->mme_set!=$request->mme_set){
                            return response()->json([
                                'errors' => [
                                    'mme_set' => ["You can't modify the set because a life sheet has already been created"]
                                ]
                            ], 429);
                        }
                    }
                } 
            }
        }
        else{
            if ($request->reason=="add"){
                //we checked if the internal reference entered is already used for another mme
                $mme_already_exist=Mme::where('mme_internalReference', '=', $request->mme_internalReference)->first() ; 
                
               if ($mme_already_exist!=null){
                    return response()->json([
                        'errors' => [
                            'mme_internalReference' => ["This internal reference is already use for another mme"]
                        ]
                    ], 429);
                }
            }
        }
    }

     /**
     * Function call by MmeIDForm.vue when the form is submitted for insert with the route : /mme/add (post)
     * Add a new enregistrement of mme and mme_temp in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new mme
     */
    public function add_mme(Request $request){

        //Creation of a new mme
        $mme=Mme::create([
            'mme_internalReference' => $request->mme_internalReference,
            'mme_externalReference' => $request->mme_externalReference, 
            'mme_name' => $request->mme_name,
            'mme_serialNumber' => $request->mme_serialNumber,
            'mme_constructor' => $request->mme_constructor,
            'mme_set' => $request->mme_set,
        ]) ; 

        $mme_id=$mme->id ; 
            
        
        //Creation of a new mme temp
        $new_mmeTemp=MmeTemp::create([
            'mme_id'=> $mme_id,
            'mmeTemp_version' => '1',
            'mmeTemp_date' => Carbon::now('Europe/Paris'),
            'mmeTemp_validate' => $request->mme_validate,
            'mmeTemp_remarks' => $request->mme_remarks,
        ]);
        
        //Creation of a new state
        $newState=MmeState::create([
            'state_remarks' => "State by default",
            'state_startDate' =>  Carbon::now('Europe/Paris'),
            'state_isOk' => true,
            'state_validate' => "drafted",
            'state_name' => "Waiting_for_referencing"
        ]) ; 
        
        $newState->mme_temps()->attach($new_mmeTemp);
        return response()->json($mme_id) ; 
    }



    /**
     * Function call by MmeIDForm.vue when the form is submitted for update with the route : /mme/update (post)
     * Update an enregistrement of mme and mme_temp in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the mme we want to update
     * */
    public function update_mme(Request $request, $id){
        $mme= Mme::findOrFail($id) ;
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id)->orderBy('created_at', 'desc')->first();
        
        //We checked if the most recently mme temp is validate and if a life sheet has been already created.
        //If the mme temp is validated and a life sheet has been already created, we need to update the number of version
        if ($mostRecentlyMmeTmp->mmeTemp_validate=="validated" && (boolean)$mostRecentlyMmeTmp->mmeTemp_lifeSheetCreated===true && $request->mme_remarks!=$mostRecentlyMmeTmp->mmeTemp_remarks){
            //We need to increase the number of mme temp linked to the mme
            $version_mme=$mme->mme_nbrVersion+1 ; 
            //Update of mme
            $mme->update([
                'mme_nbrVersion' =>$version_mme,
            ]);
            
            //We need to increase the version of the mme temp (because we create a new mme temp)
            $version =  $mostRecentlyMmeTmp->mmeTemp_version+1 ; 
            //Creation of a new mme temp
            $mostRecentlyMmeTmp->update([
                'mmeTemp_version' => $version,
                'mmeTemp_date' => Carbon::now('Europe/Paris'),
                'mmeTemp_validate' => $request->mme_validate,
                'mmeTemp_remarks' => $request->mme_remarks,
            ]);

            // In the other case, we can modify the informations without problems
        }else{

            //Update of mme
            $mme->update([
                'mme_internalReference' => $request->mme_internalReference,
                'mme_externalReference' => $request->mme_externalReference, 
                'mme_name' => $request->mme_name,
                'mme_serialNumber' => $request->mme_serialNumber,
                'mme_constructor' => $request->mme_constructor,
                'mme_set' => $request->mme_set,
            ]);

            //Update of mme temp
            $mostRecentlyMmeTmp->update([
                'mmeTemp_validate' => $request->mme_validate,
                'mmeTemp_remarks' => $request->mme_remarks,
            ]);
        }
    }

     /**
     * Function call by MmeMaintenanceCalendar.vue when the form is submitted with the route : /mme/verif/planning (post)
     * Send all the mmes validated in the data base with the verifications linked
     * @return \Illuminate\Http\Response
     * */
    public function send_mme_verif_for_planning(){
        $mmes=Mme::all() ;
        $container=array() ; 
        $containerVerif=array() ;
        foreach($mmes as $mme){
            $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme->id)->orderBy('created_at', 'desc')->first();
            $states=$mostRecentlyMmeTmp->states;
            $mostRecentlyState=MmeState::orderBy('created_at', 'asc')->first();
            foreach($states as $state){
                $date=$state->created_at ; 
                $date2=$mostRecentlyState->created_at;
               if ($date>=$date2){
                     $mostRecentlyState=$state ; 
                }
            }
            if ($mostRecentlyMmeTmp->mmeTemp_validate==="validated"){
                $verifs=Verification::where('mmeTemp_id', "=", $mostRecentlyMmeTmp->id)->get() ;
                foreach( $verifs as $verif){
                    $verif=([
                        "id" => $verif->id,
                        "verif_number" => (string)$verif->verif_number,
                        "verif_description" => $verif->verif_description,
                        "verif_periodicity" => (string)$verif->verif_periodicity,
                        "verif_symbolPeriodicity" => $verif->verif_symbolPeriodicity,
                        "verif_protocol" => $verif->verif_protocol,
                        "verif_startDate" => $verif->verif_startDate,
                        "verif_nextDate" => $verif->verif_nextDate,
                        "verif_reformDate" => $verif->verif_reformDate,
                        "verif_validate" => $verif->verif_validate,
                        
                    ]);
                    array_push($containerVerif,$verif);
                }
                
                $mme = ([
                    "id" => $mme->id,
                    "internalReference" => $mme->mme_internalReference,
                    "verifications" => $containerVerif,
                    "state_id" => $mostRecentlyState->id,
                ]) ; 

                array_push($container,$mme);


            }
        }
        return response()->json($container) ;
    }

    
    /**
     * Function call by MmeConsult.vue when the form is submitted for update with the route : /mme/verifValidation{id} (post)
     * Tell if the mme is ready to be validated
     * The id parameter is the id of the mme in which we want to validate
     * @return \Illuminate\Http\Response
     * */
    public function verif_validation($id){
        $container=array() ; 
        $container2=array() ; 
        
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyMmeTmp->mmeTemp_validate!="validated"){
            $obj=([
                'validation' => ["You can't validate an mme that doesn't have a validated ID card"],
            ]);
            array_push($container2,$obj);
        }

        $files=File::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->get() ; 
        if (count($files)<1){
            $obj2=([
                'validation' => ["You can't validate an mme that doesn't have at least one file"]
            ]);
            array_push($container2,$obj2);
            
        }else{
            foreach($files as $file){
                if ($file->file_validate != "validated"){
                    $obj3=([
                        'validation' => ["You can't validate an mme that have at least one file in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj3);
                }
            }
        }
        
        $usages=MmeUsage::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->get() ; 
        if (count($usages)<1){
            $obj4=([
                'validation' => ["You can't validate an mme that doesn't have at least one usage"]
            ]);
            array_push($container2,$obj4);
        }else{
            foreach($usages as $usage){
                if ($usage->usg_validate != "validated"){
                    
                    $obj5=([
                        'validation' => ["You can't validate an mme that have at least one usage in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj5);
                }
            }
        }
        $verifs=Verification::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->get() ; 
        if (count($verifs)<1){
            $obj9=([
                'validation' => ["You can't validate an mme that doesn't have at least one verification "]
            ]);
            array_push($container2,$obj9);
        }else{
            foreach($verifs as $verif){
                if ($verif->verif_validate != "validated"){
                    $obj10=([
                        'validation' => ["You can't validate an mme that have at least one verification in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj10);
                }
            }
        }

        if (count($container2)>0){
            return response()->json([
                    'errors' => $container2
            ], 429);
        }
    }

    /**
     * Function call by MmeConsult.vue when the form is submitted for update with the route : /mme/validation (post)
     * Tell if the mme is ready to be validated
     * The id parameter is the id of the mme in which we want to validate
     * @return \Illuminate\Http\Response
     * */
    
    public function validation(Request $request, $id){
        $mme=Mme::findOrFail($id) ; 
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id)->orderBy('created_at', 'desc')->first();
        
        if ($request->reason=="technical"){
            $mostRecentlyMmeTmp->update([
                'technicalVerifier_id' => $request->enteredBy_id,
            ]);
        }

        if ($request->reason=="quality"){
            $mostRecentlyMmeTmp->update([
                'qualityVerifier_id'=> $request->enteredBy_id,
            ]);
        }

        if ($mostRecentlyMmeTmp->qualityVerifier_id!=NULL && $mostRecentlyMmeTmp->technicalVerifier_id!=NULL){
            $mostRecentlyMmeTmp->update([
                 'mmeTemp_lifeSheetCreated' => true,
            ]);

            
            //Creation of a new state
            $newState=MmeState::create([
                'state_remarks' => "This mme has been validated",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_isOk' => true,
                'state_validate' => "drafted",
                'state_name' => "Waiting_to_be_in_use"
            ]) ; 

            $newState->mme_temps()->attach($mostRecentlyMmeTmp);
        }
    }


    
     /**
     * Function call by UpdateState when we delete an mme in the reform state : /mme/delete/{id} (post)
     * Delete a mme and its attributes
     * The id parameter is the id of the mme in which we want to reform/delete
     * */

    public function delete_mme($id){
        $mme=Mme::findOrFail($id) ; 
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id)->orderBy('created_at', 'desc')->first();

        $states=$mostRecentlyMmeTmp->states;
        $mostRecentlyState=MmeState::orderBy('created_at', 'asc')->first();
        foreach($states as $state){
            $date=$state->created_at ; 
            $date2=$mostRecentlyState->created_at;
            if ($date>=$date2){
                    $mostRecentlyState=$state ; 
            }
        }

        if ($mostRecentlyState->state_name=="Reform"){
            $files=File::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->get() ; 
            foreach ($files as $file){
                $file->delete() ;
            }

            $usages=MmeUsage::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->get() ; 
            foreach ($usages as $usage){
                $usage->delete() ;
            }

        }else{
            return response()->json([
                'errors' => [
                    'delete' => ["You can't delete a mme that isn't in reform state"]
                ]
            ], 429);

        }
    }

    /**
     * Function call by MmeIDForm.vue when the form is submitted for insert with the route : /state/mme/${id}   (post)
     * Add a new enregistrement of mme and mme_temp in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new mme
     */
    public function add_mme_from_state(Request $request, $id){

        
        //Creation of a new mme
        $mme=Mme::create([
            'mme_internalReference' => $request->mme_internalReference,
            'mme_externalReference' => $request->mme_externalReference, 
            'mme_name' => $request->mme_name,
            'mme_serialNumber' => $request->mme_serialNumber,
            'mme_constructor' => $request->mme_constructor,
            'mme_set' => $request->mme_set,
            'state_id' => $id,
        ]) ; 

        $mme_id=$mme->id ; 

        
        //Creation of a new mme temp
        $new_mmeTemp=MmeTemp::create([
            'mme_id'=> $mme_id,
            'mmeTemp_version' => '1',
            'mmeTemp_date' => Carbon::now('Europe/Paris'),
            'mmeTemp_validate' => $request->mme_validate,
            'mmeTemp_remarks' => $request->mme_remarks,
        ]);

        //Creation of a new state
        $newState=MmeState::create([
            'state_remarks' => "State by default",
            'state_startDate' =>  Carbon::now('Europe/Paris'),
            'state_isOk' => true,
            'state_validate' => "drafted",
            'state_name' => "Waiting_for_referencing"
        ]) ; 
        
        $newState->mme_temps()->attach($new_mmeTemp);
        return response()->json($mme_id) ; 
    }

    /**
     * Function call by UpdateState.vue when the form is submitted for insert with the route : /send/state/mme/${state_id} (post)
     * Send the mme created during the state in which the id is passed in parameter
     * The id paramter correspond to the id of the state in which we want the informations of the mme created during
     * @return \Illuminate\Http\Response : informations of the mme created during the state
     */
    //COMMENTAIRE A CHANGER
    public function send_mme_from_state($state_id){
        $mme=Mme::where('state_id', '=', $state_id)->first() ; 
        $validate=NULL ; 
        $massUnit=NULL;
        $type = NULL ; 
        $mobility=NULL;
        $lifeSheetCreated=$mostRecentlyMmeTmp->mmeTemp_lifeSheetCreated ; 
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme->id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyMmeTmp!=NULL){
            $validate=$mostRecentlyMmeTmp->mmeTemp_validate ; 
            $remarks=$mostRecentlyMmeTmp->mmeTemp_remarks ;
        }
        $obj=([
            'mme_internalReference' => $mme->mme_internalReference,
            'mme_externalReference' => $mme->mme_externalReference,
            'mme_name' => $mme->$mme_name,
            'mme_serialNumber' => $mme->mme_serialNumber,
            'mme_constructor'  => $mme->mme_constructor,
            'mme_remarks'  => $remarks,
            'mme_set'  => $mme->mme_set,
            'mme_validate' => $validate,
            'mme_lifeSheetCreated' => $lifeSheetCreated,
        ]);
        return response()->json($obj) ;

        
    }

    /**
     * Function call by MmeMaintenanceCalendar.vue when the form is submitted with the route : /mme/verif/revisionDatePassed (post)
     * Send all the mmes validated in the data base with the verifications in which the revision date is passed
     * @return \Illuminate\Http\Response
     * */
    public function send_mme_verif_revisionDatePassed(){
        $mmes=Mme::all() ;
        $container=array() ; 
        foreach($mmes as $mme){
            $containerVerif=array() ;
            $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme->id)->orderBy('created_at', 'desc')->first();
            if ($mostRecentlyMmeTmp->mmeTemp_validate==="validated"){
                $verifs=Verification::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->where('verif_validate', '=', "validated")->get() ;  
                $today=Carbon::now() ;
                foreach( $verifs as $verif){
                    if (($verif->verif_reformDate=='' || $verif->verif_reformDate===NULL) && $verif->verif_nextDate<$today ){
                        $verif=([
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
                            'verif_validate' => $verif->verif_validate,
                        ]);
                        array_push($containerVerif,$verif);
                    }
                }

                $states=$mostRecentlyMmeTmp->states;
                $mostRecentlyState=MmeState::orderBy('created_at', 'asc')->first();
                foreach($states as $state){
                    $date=$state->created_at ; 
                    $date2=$mostRecentlyState->created_at;
                    if ($date>=$date2){
                        $mostRecentlyState=$state ; 
                    }
                }

                if (count($containerVerif)>0){
                    $mme = ([
                        "id" => $mme->id,
                        "internalReference" => $mme->mme_internalReference,
                        "verifications" => $containerVerif,
                        "state_id" => $mostRecentlyState->id,
                    ]) ; 
                    array_push($container,$mme);
                }
            }
        }
        return response()->json($container) ;
    }

      /**
     * Function call by MmeMaintenanceCalendar.vue when the form is submitted with the route : /mme/verif/revisionLimitPassed (post)
     * Send all the mmes validated in the data base with the verification in which the revision limit is passed
     * @return \Illuminate\Http\Response
     * */
    public function send_mme_verif_revisionLimitPassed(){
        $mmes=Mme::all() ;
        $container=array() ; 
        foreach($mmes as $mme){
            $containerVerif=array() ;
            $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme->id)->orderBy('created_at', 'desc')->first();
            if ($mostRecentlyMmeTmp->mmeTemp_validate==="validated"){
                $verifs=Verification::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->where('verif_validate', '=', "validated")->get() ;    
                $today=Carbon::now('Europe/London') ;
                foreach( $verifs as $verif){
                    $dates=explode(' ', $verif->verif_nextDate) ; 
                    $ymd=explode('-', $dates[0]);
                    $year=$ymd[0] ; 
                    $month=$ymd[1] ;
                    $day=$ymd[2] ;

                    $time=explode(':', $dates[1]); 
                    $hour=$time[0] ;
                    $min=$time[1] ; 
                    $sec=$time[2] ;
                
                    $nextDate=Carbon::create($year, $month, $day, $hour, $min, $sec);
                    $OneWeekLater=$nextDate->addDays(7) ; 
                    if (($verif->verif_reformDate=='' || $verif->verif_reformDate===NULL) && $OneWeekLater<$today ){
                        $verif=([
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
                            'verif_validate' => $verif->verif_validate,
                            
                        ]);
                        array_push($containerVerif,$verif);
                    }
                }
                $states=$mostRecentlyMmeTmp->states;
                $mostRecentlyState=MmeState::orderBy('created_at', 'asc')->first();
                foreach($states as $state){
                    $date=$state->created_at ; 
                    $date2=$mostRecentlyState->created_at;
                    if ($date>=$date2){
                        $mostRecentlyState=$state ; 
                    }
                }

                if (count($containerVerif)>0){
                    $eq = ([
                        "id" => $mme->id,
                        "internalReference" => $mme->mme_internalReference,
                        "verifications" => $containerVerif,
                        "state_id" => $mostRecentlyState->id,
                    ]) ; 
    
                    array_push($container,$mme);
                }
            }
        }
        return response()->json($container) ;
    }


}





