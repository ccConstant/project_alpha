<?php

/*
* Filename : PowerController.php 
* Creation date : 17 May 2022
* Update date : 17 May 2022
* This file is used to link the view files and the database that concern the power table. 
* For example : add a power for an equipment in the data base, update a power, delete it...
*/ 


namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\SW01\EquipmentTemp ; 
use App\Models\SW01\EnumPowerType ; 
use App\Models\SW01\Power ; 
use App\Models\SW01\Equipment ;  
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\SW01\State ; 
use App\Models\User ;



class PowerController extends Controller
{


     /**
     * Function call by EquipmentPowForm.vue with the route : /power/names (get)
    * Get the fields of the power names to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */
    public function send_names (){
        $names=DB::select(DB::raw('SELECT DISTINCT pow_name FROM powers'));
        return response()->json($names) ; 
    }

    /**
     * Function call by EquipmentPowForm.vue when the form is submitted for check data with the route : /equipment/add/power/${id}  (post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_power(Request $request){

        $user=User::findOrFail($request->user_id);
        if (!$user->user_validateDescriptiveLifeSheetDataRight && $request->pow_validate=="validated"){
            return response()->json([
                'errors' => [
                    'pow_type' => ["You don't have the user right to save a power as validated"]
                ]
            ], 429);
        }

        if ($request->reason=="update"){
            $pow=Power::findOrFail($request->pow_id);
            if (!$user->user_updateDataInDraftRight && ($pow->pow_validate=="drafted" || $pow->pow_validate=="to_be_validated")){
                return response()->json([
                    'errors' => [
                        'pow_type' => ["You don't have the user right to update a power save as drafted or in to be validated"]
                    ]
                ], 429);
            }

            if (!$user->user_updateDataValidatedButNotSignedRight && $pow->pow_validate=="validated"){
                return response()->json([
                    'errors' => [
                        'pow_type' => ["You don't have the user right to update a power save as validated"]
                    ]
                ], 429);
            }
            if (!$user->user_updateDescriptiveLifeSheetDataSignedRight && $request->lifesheet_created==true){
                return response()->json([
                    'errors' => [
                        'pow_type' => ["You don't have the user right to update a power signed"]
                    ]
                ], 429);
            }
        }

        //-----CASE pow->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his power, so he must enter all the attributes
        if ($request->pow_validate=='validated'){
            $this->validate(
                $request,
                [
                    'pow_name' => 'required|min:3|max:25',
                    'pow_value' => 'required|min:1|max:25',
                    'pow_unit' => 'required|min:1|max:25',
                    'pow_consumptionValue' => 'required|min:1|max:25',
                    'pow_consumptionUnit' => 'required|min:1|max:25',

                ],
                [
                    'pow_name.required' => 'You must enter a name for the power ',
                    'pow_name.min' => 'You must enter at least three characters ',
                    'pow_name.max' => 'You must enter a maximum of 25 characters',
                    'pow_value.required' => 'You must enter a value for the power ',
                    'pow_value.min' => 'You must enter at least one character ',
                    'pow_value.max' => 'You must enter a maximum of 25 characters',
                    'pow_unit.required' => 'You must enter a unit for the power ',
                    'pow_unit.min' => 'You must enter at least one character ',
                    'pow_unit.max' => 'You must enter a maximum of 25 characters',
                    'pow_consumptionValue.required' => 'You must enter a value for the consumption of the power ',
                    'pow_consumptionValue.min' => 'You must enter at least 1 characters ',
                    'pow_consumptionValue.max' => 'You must enter a maximum of 25 characters',
                    'pow_consumptionUnit.required' => 'You must enter a unit for the consumption of the power ',
                    'pow_consumptionUnit.min' => 'You must enter at least one character ',
                    'pow_consumptionUnit.max' => 'You must enter a maximum of 25 characters',
                
                ]
            );

            //verification about pow_type, if no one value is selected we need to alert the user
            if ($request->pow_type=='' || $request->pow_type==NULL ){
                return response()->json([
                    'errors' => [
                        'pow_type' => ["You must choose a type for your power"]
                    ]
                ], 429);
            }

        }else{
             //-----CASE pow->validate=drafted or pow->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations 
            $this->validate(
                $request,
                [
                    'pow_name' => 'required|min:3|max:25',
                    'pow_value' => 'max:25',
                    'pow_unit' => 'max:25',
                    'pow_consumptionValue' => 'max:25',
                    'pow_consumptionUnit' => 'max:25',
                ],
                [
                    'pow_name.required' => 'You must enter a name for the power ',
                    'pow_name.min' => 'You must enter at least three characters ',
                    'pow_name.max' => 'You must enter a maximum of 25 characters',
                    'pow_value.max' => 'You must enter a maximum of 25 characters',
                    'pow_unit.max' => 'You must enter a maximum of 25 characters',
                    'pow_consumptionValue.max' => 'You must enter a maximum of 25 characters',
                    'pow_consumptionUnit.max' => 'You must enter a maximum of 25 characters',
                ]
            );
        }

    }

    /**
     * Function call by EquipmentPowForm.vue when the form is submitted for insert with the route : /equipment/add/pow/${id} (post)
     * Add a new enregistrement of power in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : the id of the new power
     */
    public function add_power(Request $request){

       //A power is linked to its type. So we need to find the id of the type choosen by the user and write it in the attribute of the power.
        //But if no one type is choosen by the user we define this id to NULL
        // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $type_id=NULL ; 
       if ($request->pow_type!='' && $request->pow_type!=NULL){
            $type= EnumPowerType::where('value', '=', $request->pow_type)->first() ;
            $type_id=$type->id ; 
        }

        $equipment=Equipment::findOrfail($request->eq_id) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        
        //Creation of a new power
        $power=Power::create([
            'pow_name' => $request->pow_name,
            'pow_value' => $request->pow_value,
            'pow_unit' => $request->pow_unit,
            'pow_consumptionValue' => $request->pow_consumptionValue,
            'pow_consumptionUnit' => $request->pow_consumptionUnit,
            'pow_validate' => $request->pow_validate,
            'enumPowerType_id' => $type_id,
            'equipmentTemp_id' => $mostRecentlyEqTmp->id,
        ]) ; 

        $power_id=$power->id;
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
            //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for add power
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
                    'state_remarks' => "Equipment Update (add power) : new version of life sheet created",
                    'state_startDate' =>  Carbon::now('Europe/Paris'),
                    'state_validate' => "validated",
                    'state_name' => "Waiting_for_referencing"
                ]) ;

                $newState->equipment_temps()->attach($mostRecentlyEqTmp);
            }
            return response()->json($power_id) ; 
        }
    }


    /**
     * Function call by EquipmentPowForm.vue when the form is submitted for update with the route : /equipment/update/pow/{id} (post)
     * Update an enregistrement of power in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the power we want to update
     * */
    public function update_power(Request $request, $id){

        //A power is linked to its type. So we need to find the id of the type choosen by the user and write it in the attribute of the power.
        //But if no one type is choosen by the user we define this id to NULL
        // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $type_id=NULL ; 
        if ($request->pow_type!='' && $request->pow_type!=NULL){
            $type= EnumPowerType::where('value', '=', $request->pow_type)->first() ;
            $type_id=$type->id ; 
        }

        $equipment=Equipment::findOrfail($request->eq_id) ; 
        //We search the most recently equipment temp of the equipment 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->latest()->first();
        if ($mostRecentlyEqTmp!=NULL){

            if ($mostRecentlyEqTmp->qualityVerifier_id!=NULL){
                $mostRecentlyEqTmp->update([
                    'qualityVerifier_id' => NULL,
                ]);
            }
            if ($mostRecentlyEqTmp->technicalVerifier_id!=NULL){
                $mostRecentlyEqTmp->update([
                    'technicalVerifier_id' => NULL,
                ]);
            }
            //We checked if the most recently equipment temp is validate and if a life sheet has been already created.
           //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for update power
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
                    'state_remarks' => "Equipment Update (update power) : new version of life sheet created",
                    'state_startDate' =>  Carbon::now('Europe/Paris'),
                    'state_validate' => "validated",
                    'state_name' => "Waiting_for_referencing"
                ]) ;

                $newState->equipment_temps()->attach($mostRecentlyEqTmp);
            }
            // In the other case, we can modify the informations without problems
            $power=Power::findOrFail($id) ; 
            $power->update([
                'pow_name' => $request->pow_name,
                'pow_value' => $request->pow_value,
                'pow_unit' => $request->pow_unit,
                'pow_consumptionValue' => $request->pow_consumptionValue,
                'pow_consumptionUnit' => $request->pow_consumptionUnit,
                'pow_validate' => $request->pow_validate,
                'enumPowerType_id' => $type_id,
            ]) ; 
        }
    }

    /**
     * Function call by ReferenceAPow.vue with the route : /power/send/{$id} (get)
     * Get the powers of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the powers
     * @return \Illuminate\Http\Response
     */

    public function send_powers($id) {
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $container=array() ; 
        $powers= $powers=Power::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ;
        foreach ($powers as $power) {
            $type = NULL ; 
            if ($power->enumPowerType_id!=NULL){
                $type = $power->enumPowerType->value ;
            }
            $obj=([
                'id' => $power->id,
                'pow_name' => $power->pow_name,
                'pow_value' => (string)$power->pow_value,
                'pow_unit' => $power->pow_unit,
                'pow_consumptionValue' => (string)$power->pow_consumptionValue,
                'pow_consumptionUnit' => $power->pow_consumptionUnit,
                'pow_validate' => $power->pow_validate,
                'pow_type' => $type,
            ]);
            array_push($container,$obj);
        }
        return response()->json($container) ;
    }


     /**
     * Function call by EquipmentPowForm.vue when we want to delete a power with the route : /equipment/delete/pow{id} (post)
     * Delete a power thanks to the id given in parameter
     * The id parameter correspond to the id of the power we want to delete
     * */
    public function delete_power(Request $request, $id){
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        //We search the most recently equipment temp of the equipment 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->latest()->first();
       
        $user=User::findOrFail($request->user_id);
        $power=Power::findOrFail($id);
        if (($power->pow_validate=="drafted" || $power->pow_validate=="to_be_validated") && !$user->user_deleteDataNotValidatedLinkedToEqOrMmeRight){
            return response()->json([
                'errors' => [
                    'pow_type' => ["You don't have the user right to delete a power save as drafted or in to be validated"]
                ]
            ], 429);
        }
        if ($power->pow_validate=="validated" && !$user->user_deleteDataValidatedLinkedToEqOrMmeRight){
            return response()->json([
                'errors' => [
                    'pow_type' => ["You don't have the user right to delete a power save as validated"]
                ]
            ], 429);
        }
        if ($request->lifesheet_created && !$user->user_deleteDataValidatedLinkedToEqOrMmeRight){
            return response()->json([
                'errors' => [
                    'pow_type' => ["You don't have the user right to delete a power signed"]
                ]
            ], 429);
        }
       
       
       
       
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
                'state_remarks' => "Equipment Update (delete power) : new version of life sheet created",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_validate' => "validated",
                'state_name' => "Waiting_for_referencing"
            ]) ;

            $newState->equipment_temps()->attach($mostRecentlyEqTmp);
        }
        $power->delete() ; 
    }

    /**
     * Function call by LifeSheetPDF.vue with the route : /power/send/ByType/{$id}(get)
     * Get the powers by type of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the powers
     * @return \Illuminate\Http\Response
     */

    public function send_powers_by_type($id) {
        $enums_powerType=EnumPowerType::all() ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->latest()->first();
        $powers = Power::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get();
        $containerGlobal=array() ; 
        foreach ($enums_powerType as $enum_powerType){
            $container=array() ; 
            foreach ($powers as $power) {
                if ($enum_powerType->id==$power->enumPowerType_id){
                    $type = NULL ; 
                    if ($power->enumPowerType_id!=NULL){
                        $type = $power->enumPowerType->value ;
                    }
                    $obj=([
                        'id' => $power->id,
                        'pow_name' => $power->pow_name,
                        'pow_value' => (string)$power->pow_value,
                        'pow_unit' => $power->pow_unit,
                        'pow_consumptionValue' => (string)$power->pow_consumptionValue,
                        'pow_consumptionUnit' => $power->pow_consumptionUnit,
                        'pow_validate' => $power->pow_validate,
                        'pow_type' => $type,
                    ]);
                    array_push($container,$obj);
                }
            }
            $obj2=([
                "type" => $enum_powerType->value,
                "powers" => $container,
            ]);
            array_push($containerGlobal,$obj2);
        }
       
        return response()->json($containerGlobal) ;
    }


}