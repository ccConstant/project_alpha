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
use App\Models\MmeTemp;
use App\Models\MmeState;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB ; 

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
        $sets=DB::select(DB::raw('SELECT DISTINCT mme_set FROM mme'));
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
            if ($mme_already_exist!=null){
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
                    if ($mostRecentlymmeTmp->mmeTemp_lifeSheetCreated==true){
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
                        if($equipment->eq_constructor!=$request->eq_constructor){
                            return response()->json([
                                'errors' => [
                                    'eq_constructor' => ["You can't modify the constructor because a life sheet has already been created"]
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

                        if($mme->mme_mobility!=$request->mme_mobility){
                            return response()->json([
                                'errors' => [
                                    'mme_mobility' => ["You can't modify the mobility because a life sheet has already been created"]
                                ]
                            ], 429);
                        }
                    }
                }
                
            }
        }else{
            if ($request->reason=="add"){
                //we checked if the internal reference entered is already used for another mme
                $mme_already_exist= Mme::where('mme_internalReference', '=', $request->mme_internalReference)->first() ; 
                
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

        //Creation of a new equipment
        $mme=Mme::create([
            'mme_internalReference' => $request->mme_internalReference,
            'mme_externalReference' => $request->mme_externalReference, 
            'mme_name' => $request->mme_name,
            'mme_serialNumber' => $request->mme_serialNumber,
            'mme_constructor' => $request->mme_constructor,
            'mme_set' => $request->mme_set,
        ]) ; 

        $mme_id=$mme->id ; 
            
        
        //Creation of a new equipment temp
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
            $version_eq=$equipment->eq_nbrVersion+1 ; 
            //Update of equipment
            $equipment->update([
                'eq_nbrVersion' =>$version_eq,
            ]);
            
            //We need to increase the version of the equipment temp (because we create a new equipment temp)
            $version =  $mostRecentlyEqTmp->eqTemp_version+1 ; 
            //Creation of a new equipment temp
            $mostRecentlyEqTmp->update([
                'eqTemp_version' => $version,
                'eqTemp_date' => Carbon::now('Europe/Paris'),
                'eqTemp_validate' => $request->eq_validate,
                'enumMassUnit_id' => $massUnit_id,
                'eqTemp_mass' => $request->eq_mass,
                'eqTemp_remarks' => $request->eq_remarks,
                'eq_mobility' => $request->eq_mobility,
                'enumType_id' => $type_id,
                 // NEED TO UPDATE
               /* 'qualityVerifier_id' => $mostRecentlyEqTmp->qualityVerifier_id,
                'technicalVerifier_id' => $mostRecentlyEqTmp->technicalVerifier_id,
                'createdBy_id' => $mostRecentlyEqTmp->createdBy_id,
                'specialProcess_id' => $mostRecentlyEqTmp->specialProcess_id,*/
            ]);

            // In the other case, we can modify the informations without problems
        }else{

            //Update of equipment
            $equipment->update([
                'eq_internalReference' => $request->eq_internalReference,
                'eq_externalReference' => $request->eq_externalReference, 
                'eq_name' => $request->eq_name,
                'eq_serialNumber' => $request->eq_serialNumber,
                'eq_constructor' => $request->eq_constructor,
                'eq_set' => $request->eq_set,
            ]);

            //Update of equipment temp
            $mostRecentlyEqTmp->update([
                'eqTemp_validate' => $request->eq_validate,
                'enumMassUnit_id' => $massUnit_id,
                'eqTemp_mass' => $request->eq_mass,
                'eqTemp_remarks' => $request->eq_remarks,
                'eq_mobility' => $request->eq_mobility,
                'enumType_id' => $type_id,
                // NEED TO UPDATE
               /* 'qualityVerifier_id' => $mostRecentlyEqTmp->qualityVerifier_id,
                'technicalVerifier_id' => $mostRecentlyEqTmp->technicalVerifier_id,
                'createdBy_id' => $mostRecentlyEqTmp->createdBy_id,
                'specialProcess_id' => $mostRecentlyEqTmp->specialProcess_id,*/
            ]);
        }
    }

     /**
     * Function call by EquipmentMaintenanceCalendar.vue when the form is submitted with the route : /equipment/prvMtnOp/planning (post)
     * Send all the equipments validated in the data base with the preventive maintenance operations linked
     * @return \Illuminate\Http\Response
     * */
    public function send_eq_prvMtnOp_for_planning(){
        $equipments=Equipment::all() ;
        $container=array() ; 
        $containerOp=array() ;
        foreach($equipments as $equipment){
            $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $equipment->id)->orderBy('created_at', 'desc')->first();
            if ($mostRecentlyEqTmp->eqTemp_validate==="validated"){
                $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', "=", $mostRecentlyEqTmp->id)->get() ;
                foreach( $prvMtnOps as $prvMtnOp){
                    $opMtn=([
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
                    array_push($containerOp,$opMtn);
                }
                
                $eq = ([
                    "internalReference" => $equipment->eq_internalReference,
                    "preventive_maintenance_operations" => $containerOp,
                ]) ; 

                array_push($container,$eq);


            }
        }
        return response()->json($container) ;
    }


    /**
     * Function call by EquipmentConsult.vue when the form is submitted for update with the route : /equipment/verifValidation{id} (post)
     * Tell if the equipment is ready to be validated
     * The id parameter is the id of the equipment in which we want to validate
     * @return \Illuminate\Http\Response
     * */

    public function verif_validation($id){
        $container=array() ; 
        $container2=array() ; 
        
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyEqTmp->eqTemp_validate!="validated"){
            $obj=([
                'validation' => ["You can't validate an equipment that doesn't have a validated ID card"],
            ]);
            array_push($container2,$obj);
        }

        $files=File::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ; 
        if (count($files)<1){
            $obj2=([
                'validation' => ["You can't validate an equipment that doesn't have at least one file"]
            ]);
            array_push($container2,$obj2);
            
        }else{
            foreach($files as $file){
                if ($file->file_validate != "validated"){
                    $obj3=([
                        'validation' => ["You can't validate an equipment that have at least one file in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj3);
                }
            }
        }
        
        $usages=Usage::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ; 
        if (count($usages)<1){
            $obj4=([
                'validation' => ["You can't validate an equipment that doesn't have at least one usage"]
            ]);
            array_push($container2,$obj4);
        }else{
            foreach($usages as $usage){
                if ($usage->usg_validate != "validated"){
                    
                    $obj5=([
                        'validation' => ["You can't validate an equipment that have at least one usage in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj5);
                }
            }
        }

        
        if ($mostRecentlyEqTmp->special_process==NULL){
            $obj6=([
                'validation' => ["You can't validate an equipment that doesn't have one special process"]
            ]);
            array_push($container2,$obj6);

        }else{
            $spProc=SpecialProcess::findOrFail($mostRecentlyEqTmp->special_process->id) ; 
            if ($spProc->spProc_validate!="validated"){
                $obj7=([
                    'validation' => ["You can't validate an equipment with a special process in drafted or in to be validated"]
                ]);
                array_push($container2,$obj7);
            }
        }

        
        $risks=Risk::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ; 
        if (count($risks)<1){
            $obj8=([
                'validation' => ["You can't validate an equipment that doesn't have at least one risk"]
            ]);
            array_push($container2,$obj8);
        }else{
            foreach($risks as $risk){
                if ($risk->risk_validate != "validated"){
                    $obj9=([
                        'validation' => ["You can't validate an equipment that have at least one risk in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj9);
                }
            }
        }

        
        $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ; 
        if (count($prvMtnOps)<1){
            $obj9=([
                'validation' => ["You can't validate an equipment that doesn't have at least one preventive maintenance operations "]
            ]);
            array_push($container2,$obj9);
        }else{
            foreach($prvMtnOps as $prvMtnOp){
                if ($prvMtnOp->prvMtnOp_validate != "validated"){
                    $obj10=([
                        'validation' => ["You can't validate an equipment that have at least one prvMtnOp in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj10);
                }
            }
        }
        
        if (count($mostRecentlyEqTmp->states)<1){
            $obj11=([
                'validation' => ["You can't validate an equipment that doesn't have at least one state"]
            ]);
            array_push($container2,$obj11);
        }else{
            foreach($mostRecentlyEqTmp->states as $state){
                if ($state->state_validate != "validated"){
                    $obj12=([
                        'validation' => ["You can't validate an equipment that have at least one state in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj12);
                }
            }
        }
         
        return response()->json([
                'errors' => $container2
        ], 429);

    }

    /**
     * Function call by EquipmentConsult.vue when the form is submitted for update with the route : /equipment/validation (post)
     * Tell if the equipment is ready to be validated
     * The id parameter is the id of the equipment in which we want to validate
     * @return \Illuminate\Http\Response
     * */

    public function validation(Request $request, $id){
        $equipment=Equipment::findOrFail($id) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        if ($request->reason=="technical"){
            //RELIER LA PERSONNE 
        }

        if ($request->reason=="quality"){
            //RELIER LA PERSONNE 
        }

        if ($mostRecentlyEqTmp->qualityVerifier_id!=NULL && $mostRecentlyEqTmp->technicalVerifier!=NULL){
            $mostRecentlyEqTmp->eqTemp_lifeSheetCreated=true ;

            $state_id=$state->id ; 
            
            //Creation of a new state
            /*$newState=State::create([
                'state_remarks' => "This equipment has been validated",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_isOk' => true,
                'state_validate' => "drafted",
                'state_name' => "Waiting_to_be_in_use"
            ]) ; */

            //relier 
            
        }
    }


     /**
     * Function call by ?? when we delete an equipment in the reform state : ?? (post)
     * Delete an equipment and its attributes
     * The id parameter is the id of the equipment in which we want to reform/delete
     * */

    public function delete_equipment($id){
        $equipment=Equipment::findOrFail($id) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();

        $states=$mostRecentlyEqTmp->states;
        $mostRecentlyState=MmeState::orderBy('created_at', 'asc')->first();
        foreach($states as $state){
            $date=$state->created_at ; 
            $date2=$mostRecentlyState->created_at;
            if ($date>=$date2){
                    $mostRecentlyState=$state ; 
            }
        }

        if ($mostRecentlyState->state_name=="Reform"){
            $powers=Power::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ; 
            foreach ($powers as $power){
                $power->delete() ;
            }

            $files=File::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ; 
            foreach ($files as $file){
                $file->delete() ;
            }

            $dimensions=Dimension::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ; 
            foreach ($dimensions as $dimension){
                $dimension->delete() ;
            }

            $risks=Risk::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ; 
            foreach ($risks as $risk){
                $risk->delete() ;
            }

            $usages=Usage::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ; 
            foreach ($usages as $usage){
                $usage->delete() ;
            }

            if ($mostRecentlyEqTmp->special_process!=NULL){
                $specialProcess=SpecialProcess::findOrFail($mostRecentlyEqTmp->special_process->id) ; 
                $mostRecentlyEqTmp->update([
                    'specialProcess_id' => NULL,
                ]) ;
                $specialProcess->delete() ;
            }
        }else{
            return response()->json([
                'errors' => [
                    'delete' => ["You can't delete an equipment that isn't in reform state"]
                ]
            ], 429);

        }
    }

    /**
     * Function call by EquipmentIDForm.vue when the form is submitted for insert with the route : /state/equipment/${id}   (post)
     * Add a new enregistrement of equipment and equipment_temp in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new equipment
     */
    public function add_equipment_from_state(Request $request, $id){

        
        //An equipment is linked to its mass unit. So we need to find the id of the massUnit choosen by the user and write it in the attribute of the equipment.
        //But if no one mass unit is choosen by the user we define this id to NULL
        // And if the massUnit choosen is find in the data base the NULL value will be replace by the id value
        $massUnit_id=NULL ;
        if ($request->eq_massUnit!=''){
            $massUnit= EnumEquipmentMassUnit::where('value', '=', $request->eq_massUnit)->first() ;
            $massUnit_id=$massUnit->id ; 
        }

        //An equipment is linked to its type. So we need to find the id of the type choosen by the user and write it in the attribute of the equipment.
        //But if no one type is choosen by the user we define this id to NULL
        // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $type_id=NULL ; 
        if ($request->eq_type!=''){
            $type= EnumEquipmentType::where('value', '=', $request->eq_type)->first() ;
            $type_id=$type->id ; 
        }
        
        //Creation of a new equipment
        $equipment=Equipment::create([
            'eq_internalReference' => $request->eq_internalReference,
            'eq_externalReference' => $request->eq_externalReference, 
            'eq_name' => $request->eq_name,
            'eq_serialNumber' => $request->eq_serialNumber,
            'eq_constructor' => $request->eq_constructor,
            'eq_set' => $request->eq_set,
            'state_id' => $id,
        ]) ; 

        $equipment_id=$equipment->id ; 

        
        //Creation of a new equipment temp
        $new_eqTemp=EquipmentTemp::create([
            'equipment_id'=> $equipment_id,
            'eqTemp_version' => '1',
            'eqTemp_date' => Carbon::now('Europe/Paris'),
            'eqTemp_validate' => $request->eq_validate,
            'enumMassUnit_id' => $massUnit_id,
            'eqTemp_mass' => $request->eq_mass,
            'eqTemp_remarks' => $request->eq_remarks,
            'eqTemp_mobility' => $request->eq_mobility,
            'enumType_id' => $type_id,
        ]);

        //Creation of a new state
        $newState=MmeState::create([
            'state_remarks' => "State by default",
            'state_startDate' =>  Carbon::now('Europe/Paris'),
            'state_isOk' => true,
            'state_validate' => "drafted",
            'state_name' => "Waiting_for_referencing"
        ]) ; 
        
        $newState->equipment_temps()->attach($new_eqTemp);
        return response()->json($equipment_id) ; 
    }

    /**
     * Function call by UpdateState.vue when the form is submitted for insert with the route : /send/state/equipment/${state_id} (post)
     * Add a new enregistrement of equipment and equipment_temp in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new equipment
     */
    public function send_equipment_from_state($state_id){
        $equipment=Equipment::where('state_id', '=', $state_id)->first() ; 
        $validate=NULL ; 
        $massUnit=NULL;
        $type = NULL ; 
        $mobility=NULL;
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $equipment->id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyEqTmp!=NULL){
            $validate=$mostRecentlyEqTmp->eqTemp_validate ; 
            $mass=$mostRecentlyEqTmp->eqTemp_mass ;
            $remarks=$mostRecentlyEqTmp->eqTemp_remarks ;
            $mobility=$mostRecentlyEqTmp->eqTemp_mobility;

            if ($mostRecentlyEqTmp->enumMassUnit_id!=NULL){
                $massUnit = $mostRecentlyEqTmp->enumEquipmentMassUnit->value ;
            }

            if ($mostRecentlyEqTmp->enumType_id!=NULL){
                $type = $mostRecentlyEqTmp->enumEquipmentType->value ;
            }
        }
        $obj=([
            'eq_internalReference' => $equipment->eq_internalReference,
            'eq_externalReference' => $equipment->eq_externalReference,
            'eq_name' => $equipment->eq_name,
            'eq_type'=> $type,
            'eq_serialNumber' => $equipment->eq_serialNumber,
            'eq_constructor'  => $equipment->eq_constructor,
            'eq_mass'  => (string)$mass,
            'eq_remarks'  => $remarks,
            'eq_set'  => $equipment->eq_set,
            'eq_massUnit'=> $massUnit,
            'eq_mobility'=> (boolean)$mobility,
            'eq_validate' => $validate,
        ]);
        return response()->json($obj) ;

        
    }

}





