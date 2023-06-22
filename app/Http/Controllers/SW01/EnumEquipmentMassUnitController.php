<?php

/*
* Filename : EnumEquipmentMassUnitController.php 
* Creation date : 24 May 2022
* Update date : 7 Jun 2023
* This file is used to link the view files and the database that concern the enumEquipmentMassUnit table. 
* For example : send the fields of the enum, add a new field...
*/ 



namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\EnumEquipmentMassUnit;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Controller;
use App\Models\SW01\State;
use Carbon\Carbon;


class EnumEquipmentMassUnitController extends Controller
{
    /**
     * Function call by EquipmentIDForm.vue with the route : /equipment/enum/massUnit (post)
    * Get the fields of the equipment mass unit enum in the data base and give them to the vue for print them in the form 
    * @return \Illuminate\Http\Response
    */
    public function send_enum_massUnit (){
        $enums_massUnit=DB::table('enum_equipment_mass_units')->orderBy('value', 'asc')->get() ;  
        $enums=array() ;
        foreach($enums_massUnit as $enum_massUnit){
            $enum=([
                "value" => $enum_massUnit->value,
                "id" => $enum_massUnit->id,
                "id_enum" => "EquipmentMassUnit",
            ]);
            array_push($enums, $enum) ;
        }
        return response()->json($enums) ; 
    }

    /**
     * Function call by EnumManagement.vue with the route : /equipment/enum/massUnit/add (post)
    * Add a new field for the equipment massUnit enum in the data base
     */

    public function add_enum_massUnit (Request $request){
        $enum_already_exist=EnumEquipmentMassUnit::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_eq_massUnit' => ["The value of the field for the new equipment mass unit already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_massUnit=EnumEquipmentMassUnit::create([
            'value' => $request->value, 
        ]);
    }

    /**
     * Function call by EnumManagement.vue with the route : /equipment/enum/massUnit/verif/{id} (post)
    * Verify if we can update the equipment massUnit enum in the data base
    * The id parameter correspond to the id of the enumEquipmentMassUnit we want to update
     */
    public function verif_enum_massUnit(Request $request, $id){
        $enum_already_exist=EnumEquipmentMassUnit::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_eq_massUnit' => ["The value of the field for the equipment massUnit already exist in the data base"]
                ]
            ], 429);
        }
        return response()->json($id) ;
    }

    /**
     * Function call by EnumManagement.vue with the route : /equipment/enum/massUnit/analyze/{id} (post)
    * Analyze the equipment we want to update
    * The id parameter correspond to the id of the enumEquipmentMassUnit we want to update
     */
    public function analyze_enum_massUnit(Request $request, $id){
        $equipmentTemps=EquipmentTemp::where('enumMassUnit_id', '=', $id)->get() ;
        $equipments=array() ; 
        $validated_eq=array() ;
        $id_eqs=array() ;
        $id_eqs_validated=array() ;
        $cpt=0;
        $cpt_validated=0;
        foreach($equipmentTemps as $equipmentTemp){
            $equipment=([
                "eqTemp_id" => $equipmentTemp->id,
                "name" => $equipmentTemp->equipment->eq_name,
                "internalReference" => $equipmentTemp->equipment->eq_internalReference,
            ]);
            if($equipmentTemp->eqTemp_lifeSheetCreated==1){
                foreach($id_eqs_validated as $id){
                    if($id!=$equipmentTemp->id){
                        $cpt_validated++;
                    }
                }
                if ($cpt_validated==count($id_eqs_validated)){
                    array_push($validated_eq, $equipment) ;
                    array_push($id_eqs_validated, $equipmentTemp->id) ;
                }
                $cpt_validated=0;
            }else{
                foreach($id_eqs as $id){
                    if($id!=$equipmentTemp->id){
                        $cpt++;
                    }
                }
                if ($cpt==count($id_eqs)){
                    array_push($equipments, $equipment) ;
                    array_push($id_eqs, $equipmentTemp->id) ;
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
     * Function call by EnumManagement.vue with the route : /dimension/enum/massUnit/update/{id} (post)
    * Update the dimension massUnit enum in the data base
    * The id parameter correspond to the id of the enumDimensionMassUnit we want to update
     */

    public function update_enum_massUnit (Request $request, $id){
        $enum_massUnit=EnumEquipmentMassUnit::findOrFail($id) ; 
        $enum_massUnit->update([
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
                    'state_remarks' => "Equipment Enum Update (update eq mass unit) : new version of life sheet created",
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
     * Function call by EnumManagement.vue with the route : /equipment/enum/MassUnit/delete/{id} (post)
    * Add a new field for the equipment massUnit enum in the data base
    * The id parameter correspond to the id of the enumEquipmentMassUnit we want to delete
     */

    public function delete_enum_massUnit ($id){
        $enum_massUnit=EnumEquipmentMassUnit::findOrFail($id) ; 
        $eqLinked=EquipmentTemp::where('enumMassUnit_id', '=', $id)->get() ; 
        if (count($eqLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_eq_massUnit' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_massUnit->delete() ; 
    }

}
