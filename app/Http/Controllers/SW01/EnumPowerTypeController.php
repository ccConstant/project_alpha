<?php

/*
* Filename : EnumPowerTypeController.php
* Creation date : 24 May 2022
* Update date : 27 Jun 2023
* This file is used to link the view files and the database that concern the enumPowerType table.
* For example : send the fields of the enum, add a new field...
*/


namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ;
use App\Models\SW01\Power;
use App\Models\SW01\EnumPowerType;
use App\Models\SW01\EquipmentTemp ;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Controller;
use App\Models\SW01\State;
use Carbon\Carbon;

class EnumPowerTypeController extends Controller{
     /**
     * Function call by EquipmentPowForm.vue with the route : /power/enum/type (get)
    * Get the fields of the power type enum to the vue for print them in the form
     * @return \Illuminate\Http\Response
     */
    public function send_enum_type (){
        $enums_type=DB::table('enum_power_types')->orderBy('value', 'asc')->get() ;
        $enums=array() ;
        foreach($enums_type as $enum_type){
            $enum=([
                "value" => $enum_type->value,
                "id" => $enum_type->id,
                "id_enum" => "PowerType",
            ]);
            array_push($enums, $enum) ;
        }
        return response()->json($enums) ;

    }

    /**
     * Function call by EnumManagement.vue with the route : /power/enum/type/add (post)
    * Add a new field for the power type enum in the data base
     */

    public function add_enum_type (Request $request){
        $enum_already_exist=EnumPowerType::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_pow_type' => ["The value of the field for the new power type already exist in the data base"]
                ]
            ], 429);
        }


        $enum_type=EnumPowerType::create([
            'value' => $request->value,
        ]);
    }

    /**
     * Function call by EnumManagement.vue with the route : /power/enum/type/verif/{id} (post)
    * Verify if we can update the power type enum in the data base
    * The id parameter correspond to the id of the enumPowerType we want to update
     */
    public function verif_enum_type(Request $request, $id){
        $enum_already_exist=EnumPowerType::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_pow_type' => ["The value of the field for the power type already exist in the data base"]
                ]
            ], 429);
        }
        return response()->json($id) ;
    }

    /**
     * Function call by EnumManagement.vue with the route : /power/enum/type/analyze/{id} (post)
    * Analyze the equipment connected to the power type enum we want to update
    * The id parameter correspond to the id of the enumPowerType we want to update
     */
    public function analyze_enum_type(Request $request, $id){
        $powers=Power::where('enumPowerType_id', '=', $id)->get() ;
        $equipments=array() ;
        $validated_eq=array() ;
        $id_eqs=array() ;
        $id_eqs_validated=array() ;
        $cpt=0;
        $cpt_validated=0;
       foreach($powers as $power){
            $equipment_temp=$power->equipment_temps ;
            $equipment=([
                "eqTemp_id" => $equipment_temp->id,
                "name" => $equipment_temp->equipment->eq_name,
                "internalReference" => $equipment_temp->equipment->eq_internalReference,
            ]);
            if($equipment_temp->eqTemp_lifeSheetCreated==1){
                foreach($id_eqs_validated as $id){
                    if($id!=$equipment_temp->id){
                        $cpt_validated++;
                    }
                }
                if ($cpt_validated==count($id_eqs_validated)){
                    array_push($validated_eq, $equipment) ;
                    array_push($id_eqs_validated, $equipment_temp->id) ;
                }
                $cpt_validated=0;
            }else{
                foreach($id_eqs as $id){
                    if($id!=$equipment_temp->id){
                        $cpt++;
                    }
                }
                if ($cpt==count($id_eqs)){
                    array_push($equipments, $equipment) ;
                    array_push($id_eqs, $equipment_temp->id) ;
                }
                $cpt=0;
            }

        }
        $final=([
            "id" => $id,
            "equipments" => $equipments,
            "validated_eq" => $validated_eq,
        ]);

        return response()->json($final) ;
    }


    /**
     * Function call by EnumManagement.vue with the route : /power/enum/type/update/{id} (post)
    * Update the power type enum in the data base
    * The id parameter correspond to the id of the enumPowerType we want to update
     */

    public function update_enum_type (Request $request, $id){
        $enum_type=EnumPowerType::findOrFail($id) ;
        $enum_type->update([
            'value' => $request->value,
        ]);
        if ($request->validated_eq!=NULL){
            foreach ($request->validated_eq as $eq){
                $equipment_temp=EquipmentTemp::findOrFail($eq['eqTemp_id']) ;
                $eq=$equipment_temp->equipment ;

                $version=$eq->eq_nbrVersion+1;
                $eq->update([
                    'eq_nbrVersion' => $version
                ]);
                $equipment_temp->update([
                    'eqTemp_lifeSheetCreated' => 0,
                    'qualityVerifier_id' => NULL,
                    'technicalVerifier_id' => NULL,
                    'eqTemp_version' => $version,
                    'eqTemp_signatureDate' => NULL,
                ]);

                $states=$equipment_temp->states;
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
                    'state_remarks' => "Equipment Enum Update (update power type): new version of life sheet created",
                    'state_startDate' =>  Carbon::now('Europe/Paris'),
                    'state_validate' => "validated",
                    'state_name' => "Waiting_for_referencing"
                ]) ;

                $newState->equipment_temps()->attach($equipment_temp);

                //We created a new enregistrement of history for explain the reason of the enum updates
                $HistoryController= new HistoryController() ;
                $HistoryController->add_history_for_eq($eq->id, $request) ;
            }
        }



    }

    /**
     * Function call by EnumManagement.vue with the route : /power/enum/type/delete/{id} (post)
    * Add a new field for the power type enum in the data base
    * The id parameter correspond to the id of the enumPowerType we want to delete
     */

    public function delete_enum_type ($id){
        $enum_type=EnumPowerType::findOrFail($id) ;
        $pwLinked=Power::where('enumPowerType_id', '=', $id)->get() ;
        if (count($pwLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_pow_type' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_type->delete() ;
    }
}

