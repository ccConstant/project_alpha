<?php

/*
* Filename : EquipmentController.php
* Creation date : 27 Apr 2022
* Update date : 27 Jun 2023
* This file is used to link the view files and the database that concern the equipment table.
* For example : add the identity card of an equipment in the database, update the identity card, delete the identity card...
*/

namespace App\Http\Controllers\SW01;

use App\Models\SW01\CurativeMaintenanceOperation;
use App\Models\SW01\PreventiveMaintenanceOperationRealized;
use Illuminate\Http\Request ;
use Illuminate\Support\Facades\DB ;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\PreventiveMaintenanceOperation;
use App\Models\SW01\State;
use App\Models\File;
use App\Models\SW01\Mme;
use App\Models\SW01\MmeTemp;
use App\Models\SW01\Power;
use App\Models\SW01\Dimension;
use App\Models\SW01\Risk;
use App\Models\SW01\SpecialProcess;
use App\Models\SW01\Usage;
use App\Models\User;
use App\Models\SW01\EnumEquipmentMassUnit ;
use App\Models\SW01\EnumEquipmentType ;
use App\Http\Controllers\SW01\StateController ;
use App\Http\Controllers\SW01\SpecialProcessController ;
use App\Http\Controllers\SW01\MmeController ;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

class EquipmentController extends Controller{


    /**
     * Function call by ListOfEquipmentV1.vue with the route : /equipment/equipments (get)
     * Get all the internalReference and the id of equipment in the data base for print them in the vue
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function send_internalReferences_ids (){
        $equipments= Equipment::orderBy('eq_internalReference', 'asc')->get();
        $container=array() ;
        foreach($equipments as $equipment){
            $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $equipment->id)->orderBy('created_at', 'desc')->first();
            $states=$mostRecentlyEqTmp->states;
            $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
            foreach($states as $state){
                $date=$state->created_at ;
                $date2=$mostRecentlyState->created_at;
               if ($date>=$date2){
                     $mostRecentlyState=$state ;
                }
            }
            $isAlreadyQualityValidated=false ;
            if ($mostRecentlyEqTmp->qualityVerifier_id!=NULL){
                $isAlreadyQualityValidated=true ;
            }
            $isAlreadyTechnicalValidated=false ;
            if ($mostRecentlyEqTmp->technicalVerifier_id!=NULL){
                $isAlreadyTechnicalValidated=true ;
            }
            $needToBeRealized = false;
            $needToBeApprove = false;
            $pre = PreventiveMaintenanceOperationRealized::all()->where('state_id', '=', $mostRecentlyState->id);
            foreach ($pre as $p) {
                if ($p->realizedBy_id  === NULL) {
                    $needToBeRealized = true;
                }
                if ($p->approvedBy_id  === NULL) {
                    $needToBeApprove = true;
                }
                break;
            }
            if ($needToBeApprove === false) {
                $cur = CurativeMaintenanceOperation::all()->where('state_id', '=', $mostRecentlyState->id);
                foreach ($cur as $c) {
                    if ($c->approvedBy_id  === NULL) {
                        $needToBeApprove = true;
                    }
                    break;
                }
            }

            $obj=([
                'id' => $equipment->id,
                'eq_internalReference' => $equipment->eq_internalReference,
                'eq_externalReference' => $equipment->eq_externalReference,
                'eq_name' => $equipment->eq_name,
                'eq_state' =>  $mostRecentlyState->state_name,
                'state_id' => $mostRecentlyState->id,
                'eqTemp_lifeSheetCreated' => $mostRecentlyEqTmp->eqTemp_lifeSheetCreated,
                'alreadyValidatedQuality' =>$isAlreadyQualityValidated,
                'alreadyValidatedTechnical' =>$isAlreadyTechnicalValidated,
                'eq_version' => $mostRecentlyEqTmp->eqTemp_version,
                'needToBeRealized' => $needToBeRealized,
                'needToBeApprove' => $needToBeApprove,
                'validated' => $mostRecentlyEqTmp->eqTemp_validate,
                'signed' => $mostRecentlyEqTmp->qualityVerifier_id != null && $mostRecentlyEqTmp->technicalVerifier_id != null,
                'signatureDate' => $mostRecentlyEqTmp->eqTemp_signatureDate,
            ]);
            array_push($container,$obj);
        }
        return response()->json($container);
    }


     /**
     * Function call by ImportationModal.vue with the route : /equipments/same_set/{$set} (get)
     * Get the Equipments with the same set as the one in parameters
     * The set in parameter correspond of the set of equipment we actually create : this set allow us to import many characteritics from another equipment if the set is the same
     * @return \Illuminate\Http\Response
     */

    public function send_equipments_same_set($set){
        $equipments_same_set=Equipment::where('eq_set', $set)->get();
        return response()->json($equipments_same_set) ;
    }

    /**
     * Function call by EquipmentConsult.vue with the route : /equipment/{id} (get)
     * Get equipment corresponding to the id in the data base for print it in the vue
     * The id parameter corresponds to the id of the equipment from which we want the informations (internalReference, externalReference...)
     * @return \Illuminate\Http\JsonResponse
     */

    public function send_equipment ($id){
        $equipment= Equipment::findOrFail($id) ;
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $validate=NULL ;
        $massUnit=NULL;
        $type = NULL ;
        $mobility=NULL ;
        if ($mostRecentlyEqTmp!=NULL){
            $states=$mostRecentlyEqTmp->states;
            $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
            foreach($states as $state){
                $date=$state->created_at ;
                $date2=$mostRecentlyState->created_at;
               if ($date>=$date2){
                     $mostRecentlyState=$state ;
                }
            }
            $validate=$mostRecentlyEqTmp->eqTemp_validate ;
            $mass=$mostRecentlyEqTmp->eqTemp_mass ;
            $remarks=$mostRecentlyEqTmp->eqTemp_remarks ;
            $mobility=$mostRecentlyEqTmp->eqTemp_mobility;
            $lifeSheetCreated=$mostRecentlyEqTmp->eqTemp_lifeSheetCreated ;

            if ($mostRecentlyEqTmp->enumMassUnit_id!=NULL){
                $massUnit = $mostRecentlyEqTmp->enumEquipmentMassUnit->value ;
            }
            if ($mostRecentlyEqTmp->enumType_id!=NULL){
                $type = $mostRecentlyEqTmp->enumEquipmentType->value ;
            }
            $technicalVerifier_firstName=NULL;
            $technicalVerifier_lastName=NULL;
            $qualityVerifier_firstName=NULL;
            $qualityVerifier_lastName=NULL;
            if ($mostRecentlyEqTmp->technicalVerifier_id!=NULL){
                $technicalVerifier=User::findOrFail($mostRecentlyEqTmp->technicalVerifier_id) ;
                $technicalVerifier_firstName=$technicalVerifier->user_firstName;
                $technicalVerifier_lastName=$technicalVerifier->user_lastName;
            }
            if ($mostRecentlyEqTmp->qualityVerifier_id!=NULL){
                $qualityVerifier=User::findOrFail($mostRecentlyEqTmp->qualityVerifier_id) ;
                $qualityVerifier_firstName=$qualityVerifier->user_firstName ;
                $qualityVerifier_lastName=$qualityVerifier->user_lastName ;
            }
            $version=0 ;
            $number=(Integer)$mostRecentlyEqTmp->eqTemp_version ;
            if ($number<10){
                $version="0".(String)$mostRecentlyEqTmp->eqTemp_version;
            }
        }
        return response()->json([
            'eq_internalReference' => $equipment->eq_internalReference,
            'eq_externalReference' => $equipment->eq_externalReference,
            'eq_name' => $equipment->eq_name,
            'eq_type'=> $type,
            'eq_version' => $version,
            'eq_serialNumber' => $equipment->eq_serialNumber,
            'eq_constructor'  => $equipment->eq_constructor,
            'eq_mass'  => (string)$mass,
            'eq_remarks'  => $remarks,
            'eq_set'  => $equipment->eq_set,
            'eq_massUnit'=> $massUnit,
            'eq_mobility'=> (boolean)$mobility,
            'eq_validate' => $validate,
            'eq_lifeSheetCreated' => $lifeSheetCreated,
            'eq_technicalVerifier_firstName' => $technicalVerifier_firstName,
            'eq_technicalVerifier_lastName' => $technicalVerifier_lastName,
            'eq_qualityVerifier_firstName' => $qualityVerifier_firstName,
            'eq_qualityVerifier_lastName' => $qualityVerifier_lastName,
            'eq_location'=>$mostRecentlyEqTmp->eqTemp_location,
            'eq_signatureDate'=>$mostRecentlyEqTmp->eqTemp_signatureDate,
        ]);
    }


     /**
     * Function call by EquipmentIDForm.vue with the route : /equipment/sets (get)
     * Get all the differents sets in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */

    public function send_sets (){
        $sets=DB::select(DB::raw('SELECT DISTINCT eq_set FROM equipment'));
        return response()->json($sets) ;
    }


    /**
     * Function call by EquipmentIDForm.vue when the form is submitted for check data with the route : /equipment/verif (post)
     * Check the informations entered in the form and send the errors if it exists
     * @return \Illuminate\Http\Response
     */
    public function verif_equipment(Request $request){

        // We need to do many verifications on the data entered by the user.
        // If the user make a mistake, we send to the vue an error to explain it and print it to the user.

        $user=User::findOrFail($request->createdBy_id);
        if (!$user->user_validateDescriptiveLifeSheetDataRight && $request->eq_validate=="validated"){
            return response()->json([
                'errors' => [
                    'eq_internalReference' => ["You don't have the user right to save an equipment ID as validated"]
                ]
            ], 429);
        }

        if ($request->reason=="update"){
            $equipment= EquipmentTemp::all()->where('equipment_id', '=', $request->eq_id)->last();
            if (!$user->user_updateDataInDraftRight && ($equipment->eqTemp_validate=="drafted" || $equipment->eqTemp_validate=="to_be_validated")){
                return response()->json([
                    'errors' => [
                        'eq_internalReference' => ["You don't have the user right to update an equipment ID save as drafted or in to be validated"]
                    ]
                ], 429);
            }

            if (!$user->user_updateDataValidatedButNotSignedRight && $equipment->eqTemp_validate=="validated"){
                return response()->json([
                    'errors' => [
                        'eq_internalReference' => ["You don't have the user right to update an equipment ID save as validated"]
                    ]
                ], 429);
            }
            if (!$user->user_updateDescriptiveLifeSheetDataSignedRight && $request->lifesheet_created==true){
                return response()->json([
                    'errors' => [
                        'eq_internalReference' => ["You don't have the user right to update an equipment ID signed"]
                    ]
                ], 429);
            }


        }

        //-----CASE eq->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his equipment, so he must enter all the attributes
        if ($request->eq_validate=='validated'){
            $this->validate(
                $request,
                [
                    'eq_internalReference' => 'required|min:3|max:16',
                    'eq_externalReference' => 'required|min:3|max:100',
                    'eq_name'  => 'required|min:3|max:100',
                    'eq_serialNumber'  => 'required|min:3|max:50',
                    'eq_constructor'  => 'required|min:3|max:30',
                    'eq_mass'  => 'required|max:8',
                    'eq_remarks'  => 'required|min:3|max:400',
                    'eq_set'  => 'required|min:1|max:50',
                    'eq_location' => 'required|max:255',
                ],
                [
                    'eq_internalReference.required' => 'You must enter an internal reference ',
                    'eq_internalReference.min' => 'You must enter at least 3 characters ',
                    'eq_internalReference.max' => 'You must enter a maximum of 16 characters',

                    'eq_externalReference.required' => 'You must enter an external reference',
                    'eq_externalReference.min' => 'You must enter at least 3 characters ',
                    'eq_externalReference.max' => 'You must enter a maximum of 100 characters',

                    'eq_name.required' => 'You must enter a name',
                    'eq_name.min' => 'You must enter at least 3 characters ',
                    'eq_name.max' => 'You must enter a maximum of 100 characters',

                    'eq_serialNumber.required'  => 'You must enter a serial number',
                    'eq_serialNumber.min'  => 'You must enter at least 3 characters ',
                    'eq_serialNumber.max'  =>  'You must enter a maximum of 50 characters',

                    'eq_constructor.required'  => 'You must enter a constructor',
                    'eq_constructor.min'  => 'You must enter at least 3 characters ',
                    'eq_constructor.max'  =>  'You must enter a maximum of 30 characters',

                    'eq_mass.required'  => 'You must enter a mass',
                    'eq_mass.max'  => 'You must enter a maximum of 8 characters',

                    'eq_remarks.required'  => 'You must enter a remark',
                    'eq_remarks.min'  =>  'You must enter at least 3 characters ',
                    'eq_remarks.max'  => 'You must enter a maximum of 400 characters',

                    'eq_set.required'  => 'You must enter a set',
                    'eq_set.min'  => 'You must enter at least 1 characters ',
                    'eq_set.max'  => 'You must enter a maximum of 50 characters',

                    'eq_location.required' => 'You must enter a location',
                    'eq_location.max' => 'You must enter a maximum of 255 characters ',

                ]
            );

            //verification about eq_type, if no one value is selected we need to alert the user
            if ($request->eq_type=='' || $request->eq_type==NULL ){
                return response()->json([
                    'errors' => [
                        'eq_type' => ["You must choose a type"]
                    ]
                ], 429);
            }

            //verification about eq_massUnit, if no one value is selected we need to alert the user
            if ($request->eq_massUnit=='' || $request->eq_massUnit==NULL){
                return response()->json([
                    'errors' => [
                        'eq_massUnit' => ["You must choose a unit of mass"]
                    ]
                ], 429);
            }
        }else{
             //-----CASE eq->validate=drafted or eq->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" value he must enter only the internReference and externReference
            $this->validate(
                $request,
                [
                    'eq_internalReference' => 'required|min:3|max:16',
                    'eq_externalReference' => 'required|min:3|max:100',
                    'eq_name'  => 'max:100',
                    'eq_serialNumber'  => 'max:50',
                    'eq_constructor'  => 'max:30',
                    'eq_mass'  => 'max:8',
                    'eq_remarks'  => 'max:400',
                    'eq_set'  => 'max:50',
                    'eq_location' => 'max:255',
                ],
                [

                    'eq_internalReference.required' => 'You must enter an internal reference ',
                    'eq_internalReference.min' => 'You must enter at least 3 characters ',
                    'eq_internalReference.max' => 'You must enter a maximum of 16 characters',

                    'eq_externalReference.required' => 'You must enter an external reference',
                    'eq_externalReference.min' => 'You must enter at least 3 characters ',
                    'eq_externalReference.max' => 'You must enter a maximum of 100 characters',

                    'eq_name.max' => 'You must enter a maximum of 100 characters',
                    'eq_serialNumber.max'  =>  'You must enter a maximum of 50 characters',
                    'eq_constructor.max'  =>  'You must enter a maximum of 30 characters',
                    'eq_mass.max'  => 'You must enter a maximum of 8 characters',
                    'eq_remarks.max'  => 'You must enter a maximum of 400 characters',
                    'eq_set.max'  => 'You must enter a maximum of 50 characters',
                    'eq_location.max' => 'You must enter a maximum of 255 characters ',
                ]
            );
        }

        if ($request->reason=="update"){
            //we checked if the internal reference entered is already used for another equipment
            $equipment_already_exist=Equipment::where('eq_internalReference', '=', $request->eq_internalReference, 'and')->where('id', '<>', $request->eq_id)->first() ;
            if ($equipment_already_exist!=null){
                return response()->json([
                    'errors' => [
                        'eq_internalReference' => ["This internal reference is already use for another equipment"]
                    ]
                ], 429);
            }

            //We search the most recently equipment temp of the equipment
            $equipment= Equipment::findOrFail($request->eq_id) ;
            $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
            if ($mostRecentlyEqTmp!=NULL){
                //we checked if a you have already validated the id card, ff it's the case we can't update the internalReference
                if ($mostRecentlyEqTmp->eqTemp_validate=="validated"){
                    if($equipment->eq_internalReference!=$request->eq_internalReference){
                        return response()->json([
                            'errors' => [
                                'eq_internalReference' => ["You can't modify the internal reference because you have already validated the id card "]
                            ]
                        ], 429);
                    }
                    //we checked if a life sheet is already created. If it's the case we can't update the external reference, the name, the constructor (...)
                    if ($mostRecentlyEqTmp->eqTemp_lifeSheetCreated==true){
                        if($equipment->eq_externalReference!=$request->eq_externalReference){
                            return response()->json([
                                'errors' => [
                                    'eq_externalReference' => ["You can't modify the external reference because you have already validated the id card"]
                                ]
                            ], 429);
                        }
                        if($equipment->eq_name!=$request->eq_name){
                            return response()->json([
                                'errors' => [
                                    'eq_name' => ["You can't modify the name because a life sheet has already been created"]
                                ]
                            ], 429);
                        }
                        if($equipment->eq_serialNumber!=$request->eq_serialNumber){
                            return response()->json([
                                'errors' => [
                                    'eq_serialNumber' => ["You can't modify the serial number because a life sheet has already been created"]
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

                        if($equipment->eq_set!=$request->eq_set){
                            return response()->json([
                                'errors' => [
                                    'eq_set' => ["You can't modify the set because a life sheet has already been created"]
                                ]
                            ], 429);
                        }
                    }
                }

            }
        }else{
            if ($request->reason=="add"){
                //we checked if the internal reference entered is already used for another equipment
                $equipment_already_exist= Equipment::where('eq_internalReference', '=', $request->eq_internalReference)->first() ;

                if ($equipment_already_exist!=null){
                    return response()->json([
                        'errors' => [
                            'eq_internalReference' => ["This internal reference is already use for another equipment"]
                        ]
                    ], 429);
                }
            }
        }
    }

     /**
     * Function call by EquipmentIDForm.vue when the form is submitted for insert with the route : /equipment/add (post)
     * Add a new enregistrement of equipment and equipment_temp in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new equipment
     */
    public function add_equipment(Request $request){



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
            'createdBy_id' => $request->createdBy_id,
            'eqTemp_location' => $request->eq_location,
        ]);

        //Creation of a new state
        $newState=State::create([
            'state_remarks' => "State by default",
            'state_startDate' =>  Carbon::now('Europe/Paris'),
            'state_validate' => "validated",
            'state_name' => "Waiting_for_referencing"
        ]) ;

        $newState->equipment_temps()->attach($new_eqTemp);
        return response()->json($equipment_id) ;
    }



    /**
     * Function call by EquipmentIDForm.vue when the form is submitted for update with the route : /equipment/update (post)
     * Update an enregistrement of equipment and equipment_temp in the data base with the informations entered in the form
     * The id parameter correspond to the id of the equipment we want to update
     * */
    public function update_equipment(Request $request, $id){
        $equipment= Equipment::findOrFail($id) ;
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();

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
        //If the equipment temp is validated and a life sheet has been already created, we need to create another equipment temp (that's mean another life sheet version)
        if ($mostRecentlyEqTmp->eqTemp_validate=="validated" && (boolean)$mostRecentlyEqTmp->eqTemp_lifeSheetCreated===true && ($type_id != $mostRecentlyEqTmp->enumType_id || $request->eq_mass != $mostRecentlyEqTmp->eqTemp_mass || $massUnit_id != $mostRecentlyEqTmp->enumMassUnit_id || $request->eq_remarks!=$mostRecentlyEqTmp->eqTemp_remarks || $request->eq_mobility!=$mostRecentlyEqTmp->eqTemp_mobility)){
            //We need to increase the number of equipment temp linked to the equipment
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
                'qualityVerifier_id' => NULL,
                'eqTemp_lifeSheetCreated' => false,
                'technicalVerifier_id' => NULL,
                'eqTemp_location' => $request->eq_location,
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
                'state_remarks' => "Equipment Update (update eq) : new version of life sheet created",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_validate' => "validated",
                'state_name' => "Waiting_for_referencing"
            ]) ;

            $newState->equipment_temps()->attach($mostRecentlyEqTmp);

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
                'eqTemp_mobility' => $request->eq_mobility,
                'enumType_id' => $type_id,
                'eqTemp_location' => $request->eq_location,
            ]);
        }
    }

    /**
     * Function call by AnnualEquipmentPlanningV1.vue with the route : /equipment/prvMtnOp/planning (post)
     * Send all the equipments validated in the data base with the preventive maintenance operations linked
     * @return \Illuminate\Http\Response
     * */
    public function send_eq_prvMtnOp_for_planning(){
        $equipments=Equipment::all() ;
        $container=array() ;
        foreach($equipments as $equipment){
            $containerOp=array() ;
            $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $equipment->id)->orderBy('created_at', 'desc')->first();
            if ($mostRecentlyEqTmp->eqTemp_validate==="validated"){
                $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->where('prvMtnOp_validate', '=', "validated")->where('prvMtnOp_reformDate','=',NULL)->get() ;
                $comment = null;
                foreach( $prvMtnOps as $prvMtnOp){
                    $comment = $prvMtnOp->prvMtnOp_comment;
                    $AllnextDate=array() ;
                    if ($prvMtnOp->prvMtnOp_preventiveOperation){
                        $dates=explode(' ', $prvMtnOp->prvMtnOp_nextDate) ;
                        $ymd=explode('-', $dates[0]);
                        $year=$ymd[0] ;
                        $month=$ymd[1] ;
                        $day=$ymd[2] ;

                        $time=explode(':', $dates[1]);
                        $hour=$time[0] ;
                        $min=$time[1] ;
                        $sec=$time[2] ;

                        $nextDate=Carbon::create($year, $month, $day, $hour, $min, $sec);
                        $endDate=Carbon::now('Europe/Paris');
                        $endDate->addMonths(24);
                        $dateForPush=Carbon::create($nextDate->year, $nextDate->month, $nextDate->day, $nextDate->hour, $nextDate->minute, $nextDate->second) ;
                        $monthForPush=$dateForPush->month ;
                        if (strlen($monthForPush)==1){
                            $monthForPush="0".$monthForPush ;
                        }
                        array_push($AllnextDate,$monthForPush."-".$dateForPush->year) ;
                        $day = $dateForPush->day;
                        while($nextDate<$endDate){
                            if ($prvMtnOp->prvMtnOp_symbolPeriodicity=='Y'){
                                $nextDate->addYears($prvMtnOp->prvMtnOp_periodicity) ;
                            }
                            if ($prvMtnOp->prvMtnOp_symbolPeriodicity=='M'){
                                $nextDate->addMonths($prvMtnOp->prvMtnOp_periodicity) ;
                            }

                            if ($prvMtnOp->prvMtnOp_symbolPeriodicity=='D'){
                                $nextDate->addDays($prvMtnOp->prvMtnOp_periodicity) ;
                            }
                            if ($prvMtnOp->prvMtnOp_symbolPeriodicity=='H'){
                                $nextDate->addHours($prvMtnOp->prvMtnOp_periodicity) ;
                            }
                            if ($nextDate<$endDate) {
                                $dateForPush2=Carbon::create($nextDate->year, $nextDate->month, $nextDate->day, $nextDate->hour, $nextDate->minute, $nextDate->second) ;
                                $monthForPush2=$dateForPush2->month ;
                                if (strlen($monthForPush2)==1){
                                    $monthForPush2="0".$monthForPush2 ;
                                }
                                array_push($AllnextDate,$monthForPush2."-".$dateForPush2->year) ;
                                $day = $dateForPush2->day;
                            }
                        }
                        $opMtn=([
                            "id" => $prvMtnOp->id,
                            "prvMtnOp_number" => (string)$prvMtnOp->prvMtnOp_number,
                            "prvMtnOp_description" => $prvMtnOp->prvMtnOp_description,
                            "prvMtnOp_periodicity" => (string)$prvMtnOp->prvMtnOp_periodicity,
                            "prvMtnOp_symbolPeriodicity" => $prvMtnOp->prvMtnOp_symbolPeriodicity,
                            "prvMtnOp_nextDate" => $AllnextDate,
                            "prvMtnOp_protocol" => $prvMtnOp->prvMtnOp_protocol,
                            "prvMtnOp_day" => $day,

                        ]);
                        array_push($containerOp,$opMtn);

                    }else{

                        $opMtn=([
                            "id" => $prvMtnOp->id,
                            "prvMtnOp_number" => (string)$prvMtnOp->prvMtnOp_number,
                            "prvMtnOp_description" => $prvMtnOp->prvMtnOp_description,
                            "prvMtnOp_periodicity" => "N/A",
                            "prvMtnOp_symbolPeriodicity" => "",
                            "prvMtnOp_nextDate" => $AllnextDate,

                        ]);
                        array_push($containerOp,$opMtn);
                    }
                }

                $states=$mostRecentlyEqTmp->states;
                $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
                foreach($states as $state){
                    $date=$state->created_at ;
                    $date2=$mostRecentlyState->created_at;
                    if ($date>=$date2){
                        $mostRecentlyState=$state ;
                    }
                }


                $eq = ([
                    "id" => $equipment->id,
                    "internalReference" => $equipment->eq_internalReference,
                    "name" => $equipment->eq_name,
                    "preventive_maintenance_operations" => $containerOp,
                    "state_id" => $mostRecentlyState->id,
                    "prvMtnOp_lastComment" => $comment !== null ? $comment : "N/A",
                ]) ;

                array_push($container,$eq);


            }
        }
        return response()->json($container) ;
    }


    /**
     * Function call by MonthlyEquipmentPlanning.vue with the route : /equipment/prvMtnOp/planning (post)??
     * Send all the equipments validated in the data base with the preventive maintenance operations linked
     * @return \Illuminate\Http\Response
     * */
    public function send_eq_prvMtnOp_for_monthly_planning(){
        $equipments=Equipment::all() ;
        $containerOp=array() ;
        foreach($equipments as $equipment){
            $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $equipment->id)->orderBy('created_at', 'desc')->first();
            if ($mostRecentlyEqTmp->eqTemp_validate==="validated"){
                $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->where('prvMtnOp_validate', '=', "validated")->where('prvMtnOp_reformDate','=',NULL)->where('prvMtnOp_preventiveOperation','=',1)->get() ;
                foreach( $prvMtnOps as $prvMtnOp){
                    $now=Carbon::now('Europe/Paris');
                    $oneMonthLater=Carbon::now('Europe/Paris');
                    $oneMonthLater->addMonths(1);
                    $dates=explode(' ', $prvMtnOp->prvMtnOp_nextDate) ;
                    $ymd=explode('-', $dates[0]);
                    $year_nextDate=$ymd[0] ;
                    $month_nextDate=$ymd[1] ;
                    $day_nextDate=$ymd[2] ;
                    $nextDate=$day_nextDate." ".$month_nextDate." ".$year_nextDate;
                    $nextDateCarbon=Carbon::create($year_nextDate, $month_nextDate, $day_nextDate, 0, 0, 0) ;
                    $states=$mostRecentlyEqTmp->states;
                    $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
                    foreach($states as $state){
                        $date=$state->created_at ;
                        $date2=$mostRecentlyState->created_at;
                        if ($date>=$date2){
                            $mostRecentlyState=$state ;
                        }
                    }
                    if ($prvMtnOp->prvMtnOp_validate=="validated" && $nextDateCarbon<=$oneMonthLater){
                        if ($nextDateCarbon>=$now){
                            $opMtn=([
                                "id" => $prvMtnOp->id,
                                "Number" => (string)$prvMtnOp->prvMtnOp_number,
                                "Description" => $prvMtnOp->prvMtnOp_description,
                                "prvMtnOp_periodicity" => (string)$prvMtnOp->prvMtnOp_periodicity,
                                "prvMtnOp_symbolPeriodicity" => $prvMtnOp->prvMtnOp_symbolPeriodicity,
                                "nextDate" => $nextDate,
                                "Protocol" => $prvMtnOp->prvMtnOp_protocol,
                                "Internal_Ref" => $equipment->eq_internalReference,
                                "Name" => $equipment->eq_name,
                                "eq_id" => $equipment->id,
                                "state_id" => $mostRecentlyState->id,
                                "passed" => false,
                            ]);
                            array_push($containerOp,$opMtn);
                        }else{
                            $opMtn=([
                                "id" => $prvMtnOp->id,
                                "Number" => (string)$prvMtnOp->prvMtnOp_number,
                                "Description" => $prvMtnOp->prvMtnOp_description,
                                "prvMtnOp_periodicity" => (string)$prvMtnOp->prvMtnOp_periodicity,
                                "prvMtnOp_symbolPeriodicity" => $prvMtnOp->prvMtnOp_symbolPeriodicity,
                                "nextDate" => $nextDate,
                                "Protocol" => $prvMtnOp->prvMtnOp_protocol,
                                "Internal_Ref" => $equipment->eq_internalReference,
                                "Name" => $equipment->eq_name,
                                "eq_id" => $equipment->id,
                                "state_id" => $mostRecentlyState->id,
                                "passed" => true,
                            ]);
                            array_push($containerOp,$opMtn);
                        }
                    }
                }
            }
        }
        return response()->json($containerOp) ;
    }

    /**
     * Function call by AnnualEquipmentCalendar.vue with the route : /equipment/planning/periode (get)
     * @return \Illuminate\Http\Response
     * */
    public function send_periode_for_planning(){
        $Allperiode=array();
        $startDate=Carbon::now('Europe/Paris');
        $month=$startDate->month ;
        if ($month==1){
            $month="Jan" ;
        }
        if ($month==2){
            $month="Feb" ;
        }
        if ($month==3){
            $month="Mar" ;
        }
        if ($month==4){
            $month="Apr" ;
        }
        if ($month==5){
            $month="May" ;
        }
        if ($month==6){
            $month="Jun" ;
        }
        if ($month==7){
            $month="Jul" ;
        }
        if ($month==8){
            $month="Aug" ;
        }
        if ($month==9){
            $month="Sep" ;
        }
        if ($month==10){
            $month="Oct" ;
        }
        if ($month==11){
            $month="Nov" ;
        }
        if ($month==12){
            $month="Dec" ;
        }
        $monthForId=$startDate->month ;
        $monthForId2=$startDate->month ;
        if (strlen($monthForId)==1){
            $monthForId2="0".$monthForId ;
        }

        $periode=([
            'month' => $month,
            'year' => $startDate->year,
            'id' => $monthForId2."-".$startDate->year,
        ]);


        array_push($Allperiode,$periode);
        for ($i=1; $i<24; $i++){
            $startDate->addMonth(1) ;
            $year=$startDate->year;
            $month=$startDate->month;
            $monthInLetters="";
            if ($month==1){
                $monthInLetters="Jan" ;
            }
            if ($month==2){
                $monthInLetters="Feb" ;
            }
            if ($month==3){
                $monthInLetters="Mar" ;
            }
            if ($month==4){
                $monthInLetters="Apr" ;
            }
            if ($month==5){
                $monthInLetters="May" ;
            }
            if ($month==6){
                $monthInLetters="Jun" ;
            }
            if ($month==7){
                $monthInLetters="Jul" ;
            }
            if ($month==8){
                $monthInLetters="Aug" ;
            }
            if ($month==9){
                $monthInLetters="Sep" ;
            }
            if ($month==10){
                $monthInLetters="Oct" ;
            }
            if ($month==11){
                $monthInLetters="Nov" ;
            }
            if ($month==12){
                $monthInLetters="Dec" ;
            }

            $monthForId3=$startDate->month ;
            $monthForId4=$startDate->month ;
            if (strlen($monthForId3)==1){
                $monthForId4="0".$monthForId3 ;
            }

            $periode2=([
                'id' => $monthForId4."-".$startDate->year,
                "month" => $monthInLetters,
                "year" => $year,
            ]);
            array_push($Allperiode,$periode2);
        }
        return $Allperiode ;

    }




    /**
     * Function call by EquipmentConsult.vue when the form is submitted for update with the route : /equipment/verifValidation{id} (post)
     * Tell if the equipment is ready to be validated
     * The id parameter is the id of the equipment in which we want to validate
     * @return \Illuminate\Http\Response
     * */

    public function verif_validation(Request $request, $id){
        $container=array() ;
        $container2=array() ;

        $user=User::findOrFail($request->user_id) ;
        if ($request->reason=="technical" && !$user->user_makeTechnicalValidationRight){
            return response()->json([
                'errors' => [
                    'validation' => ["You don't have the right to realize a technical validation"]
                ]
            ], 429);
        }
        if ($request->reason=="quality" && !$user->user_makeQualityValidationRight){
            return response()->json([
                'errors' => [
                    'validation' => ["You don't have the right to realize a quality validation"]
                ]
            ], 429);
        }

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyEqTmp->eqTemp_validate!="validated"){
            $obj=([
                'validation' => ["You can't validate an equipment that doesn't have a validated ID card"],
            ]);
            array_push($container2,$obj);
        }

        if ($mostRecentlyEqTmp->createdBy_id==NULL){
            $obj2=([
                'validation' => ["You can't validate an equipment that doesn't have a creator, please reference one"],
            ]);
            array_push($container2,$obj2);
        }

        $files=File::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ;
        if (count($files)<1){
            $obj3=([
                'validation' => ["You can't validate an equipment that doesn't have at least one file"]
            ]);
            array_push($container2,$obj3);

        }else{
            foreach($files as $file){
                if ($file->file_validate != "validated"){
                    $obj4=([
                        'validation' => ["You can't validate an equipment that have at least one file in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj4);
                }
            }
        }

        $usages=Usage::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ;
        if (count($usages)<1){
            $obj5=([
                'validation' => ["You can't validate an equipment that doesn't have at least one usage"]
            ]);
            array_push($container2,$obj5);
        }else{
            foreach($usages as $usage){
                if ($usage->usg_validate != "validated"){

                    $obj6=([
                        'validation' => ["You can't validate an equipment that have at least one usage in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj6);
                }
            }
        }


        if ($mostRecentlyEqTmp->special_process==NULL){
            $obj7=([
                'validation' => ["You can't validate an equipment that doesn't have one special process"]
            ]);
            array_push($container2,$obj7);

        }else{
            $spProc=SpecialProcess::findOrFail($mostRecentlyEqTmp->special_process->id) ;
            if ($spProc->spProc_validate!="validated"){
                $obj8=([
                    'validation' => ["You can't validate an equipment with a special process in drafted or in to be validated"]
                ]);
                array_push($container2,$obj8);
            }
        }


        $risks=Risk::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ;
        if (count($risks)<1){
            $obj9=([
                'validation' => ["You can't validate an equipment that doesn't have at least one risk"]
            ]);
            array_push($container2,$obj9);
        }else{
            foreach($risks as $risk){
                if ($risk->risk_validate != "validated"){
                    $obj10=([
                        'validation' => ["You can't validate an equipment that have at least one risk in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj10);
                }
            }
        }


        $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ;
        if (count($prvMtnOps)<1){
            $obj11=([
                'validation' => ["You can't validate an equipment that doesn't have at least one preventive maintenance operation "]
            ]);
            array_push($container2,$obj11);
        }else{
            foreach($prvMtnOps as $prvMtnOp){
                if ($prvMtnOp->prvMtnOp_validate != "validated"){
                    $obj12=([
                        'validation' => ["You can't validate an equipment that have at least one prvMtnOp in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj12);
                }
            }
        }

        if (count($mostRecentlyEqTmp->states)<1){
            $obj13=([
                'validation' => ["You can't validate an equipment that doesn't have at least one state"]
            ]);
            array_push($container2,$obj13);
        }else{
            foreach($mostRecentlyEqTmp->states as $state){
                if ($state->state_validate != "validated"){
                    $obj14=([
                        'validation' => ["You can't validate an equipment that have at least one state in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj14);
                }
            }
        }

        $dims=Dimension::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ;
        if (count($dims)<1){
            $obj15=([
                'validation' => ["You can't validate an equipment that doesn't have at least one dimension"]
            ]);
            array_push($container2,$obj15);

        }else{
            foreach($dims as $dim){
                if ($dim->dim_validate != "validated"){
                    $obj16=([
                        'validation' => ["You can't validate an equipment that have at least one dimension in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj16);
                }
            }
        }

        $pows=Power::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ;
        if (count($pows)<1){
            $obj17=([
                'validation' => ["You can't validate an equipment that doesn't have at least one power"]
            ]);
            array_push($container2,$obj17);

        }else{
            foreach($pows as $pow){
                if ($pow->pow_validate != "validated"){
                    $obj18=([
                        'validation' => ["You can't validate an equipment that have at least one power in draft or in to be validated, you have to validated it"]
                    ]);
                    array_push($container2,$obj18);
                }
            }
        }

        $states=$mostRecentlyEqTmp->states;
        $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
        foreach($states as $state){
            $date=$state->created_at ;
            $date2=$mostRecentlyState->created_at;
            if ($date>=$date2){
                    $mostRecentlyState=$state ;
            }
        }

        if ($request->reason=="quality"){
            if ($mostRecentlyState->state_name!="In_use" && $mostRecentlyState->state_name!="Waiting_for_installation"){
                $obj19=([
                    'validation' => ["You can realize quality validation only in use and waiting for installation state"]
                ]);
                array_push($container2,$obj19);
            }

            $mmes=Mme::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get() ;
            foreach($mmes as $mme){
                $mme_temp=MmeTemp::where('mme_id', '=', $mme->id)->orderBy('created_at', 'desc')->first();
                if ($mme_temp->qualityVerifier_id==NULL || $mme_temp->technicalVerifier_id==NULL){
                    $obj20=([
                        'validation' => ["You can't realize quality validation when there is at least one MME not validated by a technical verifier or a quality verifier"]
                    ]);
                    array_push($container2,$obj20);
                }
            }

        }

        if ($request->reason=="technical" && $mostRecentlyState->state_name!="Waiting_for_referencing" && $mostRecentlyState->state_name!="Waiting_for_installation"){
            $obj21=([
                'validation' => ["You can realize technical validation only in waiting for referencing and waiting for installation state"]
            ]);
            array_push($container2,$obj21);
        }

        if (count($container2)>0){
            return response()->json([
                    'errors' => $container2
            ], 429);
        }
    }

    /**
     * Function call by EquipmentConsult.vue when the form is submitted for update with the route : /equipment/validation/id (post)
     * Tell if the equipment is ready to be validated
     * The id parameter is the id of the equipment in which we want to validate
     * @return \Illuminate\Http\Response
     * */

    public function validation(Request $request, $id){
        $equipment=Equipment::findOrFail($id) ;
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();

        if ($request->reason=="technical"){
            $mostRecentlyEqTmp->update([
                'technicalVerifier_id' => $request->enteredBy_id,
            ]);

            $states=$mostRecentlyEqTmp->states;
            $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
            foreach($states as $state){
                $date=$state->created_at ;
                $date2=$mostRecentlyState->created_at;
                if ($date>=$date2){
                        $mostRecentlyState=$state ;
                }
            }


            if ($mostRecentlyState->state_name!="Waiting_for_installation"){
                if ($mostRecentlyState!=NULL){
                    $now=Carbon::now('Europe/Paris');
                    $mostRecentlyState->update([
                        'state_endDate' => $now,
                    ]);
                }
                $newState=State::create([
                    'state_remarks' => "This equipment has been validated by a technical verifier",
                    'state_startDate' =>  $now,
                    'state_validate' => "validated",
                    'state_name' => "Waiting_for_installation",
                ]);
                $newState->equipment_temps()->attach($mostRecentlyEqTmp);
            }
        }

        if ($request->reason=="quality"){
            $mostRecentlyEqTmp->update([
                'qualityVerifier_id'=> $request->enteredBy_id,
            ]);

            $states=$mostRecentlyEqTmp->states;
            $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
            foreach($states as $state){
                $date=$state->created_at ;
                $date2=$mostRecentlyState->created_at;
               if ($date>=$date2){
                     $mostRecentlyState=$state ;
                }
            }
            if ($mostRecentlyState->state_name!="In_use"){
                if ($mostRecentlyState!=NULL){
                    $now=Carbon::now('Europe/Paris');
                    $mostRecentlyState->update([
                        'state_endDate' => $now,
                    ]);
                }
                $newState=State::create([
                    'state_remarks' => "This equipment has been validated by a quality verifier",
                    'state_startDate' =>  $now,
                    'state_validate' => "validated",
                    'state_name' => "In_use",
                ]);
                $newState->equipment_temps()->attach($mostRecentlyEqTmp);
            }

        }
       if ($mostRecentlyEqTmp->qualityVerifier_id!=NULL && $mostRecentlyEqTmp->technicalVerifier_id!=NULL){
            $mostRecentlyEqTmp->update([
                'eqTemp_lifeSheetCreated' => true,
                'eqTemp_signatureDate' => Carbon::now('Europe/Paris'),
            ]);
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

        $old_equipment=Equipment::findOrFail($request->oldEq_id);
        $old_eqTemp=EquipmentTemp::where('equipment_id', '=', $request->oldEq_id)->first() ;



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
            'specialProcess_id' => $old_eqTemp->specialProcess_id,
            'eqTemp_location' => $request->eq_location,
            'createdBy_id' => $request->createdBy_id,
        ]);

        //Creation of a new state
        $newState=State::create([
            'state_remarks' => "State by default",
            'state_startDate' =>  Carbon::now('Europe/Paris'),
            'state_validate' => "drafted",
            'state_name' => "Waiting_for_referencing"
        ]) ;

        $newState->equipment_temps()->attach($new_eqTemp);


        $dims=Dimension::where('equipmentTemp_id', '=', $old_eqTemp->id)->get() ;
        $files=File::where('equipmentTemp_id', '=', $old_eqTemp->id)->get() ;
        $powers=Power::where('equipmentTemp_id', '=', $old_eqTemp->id)->get() ;
        $usages=Usage::where('equipmentTemp_id', '=', $old_eqTemp->id)->get() ;
        $risks=Risk::where('equipmentTemp_id', '=', $old_eqTemp->id)->get() ;
        $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $old_eqTemp->id)->get() ;

        foreach($dims as $dim){
            $dim->update([
                'equipmentTemp_id' => $new_eqTemp->id,
                'dim_validate' => 'drafted',
            ]);
        }
        foreach($files as $file){
            $file->update([
                'equipmentTemp_id' => $new_eqTemp->id,
                'file_validate' => 'drafted',
            ]);
        }
        foreach($powers as $power){
            $power->update([
                'equipmentTemp_id' => $new_eqTemp->id,
                'power_validate' => 'drafted',
            ]);
        }
        foreach($usages as $usage){
            $usage->update([
                'equipmentTemp_id' => $new_eqTemp->id,
                'usg_validate' => 'drafted',
            ]);
        }
        foreach($risks as $risk){
            $risk->update([
                'equipmentTemp_id' => $new_eqTemp->id,
                'risk_validate' => 'drafted',
            ]);
        }
        foreach($prvMtnOps as $prvMtnOp){
            $prvMtnOp->update([
                'equipmentTemp_id' => $new_eqTemp->id,
                'prvMtnOp_validate' => 'drafted',
            ]);
        }
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
            'eq_location' => $mostRecentlyEqTmp->eqTemp_location,
            'eq_signatureDate' => $equipment->eq_signatureDate,
        ]);
        return response()->json($obj) ;


    }
}





