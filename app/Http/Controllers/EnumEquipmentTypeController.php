<?php

/*
* Filename : EnumEquipmentTypeController.php 
* Creation date : 24 May 2022
* Update date : 14 Feb 2023
* This file is used to link the view files and the database that concern the enumEquipmentType table. 
* For example : send the fields of the enum, add a new field...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\Equipment;
use App\Models\EquipmentTemp;
use App\Models\EnumEquipmentType;
use App\Http\Controllers\HistoryController;

class EnumEquipmentTypeController extends Controller
{
    /**
     * Function call by EquipmentIDForm.vue with the route : /equipment/enum/type (get)
    * Get the fields of the equipment type enum to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */

    public function send_enum_type (){
        $enums_type=DB::table('enum_equipment_types')->orderBy('value', 'asc')->get() ;  
        $enums=array() ;
        foreach($enums_type as $enum_type){
            $enum=([
                "value" => $enum_type->value,
                "id" => $enum_type->id,
                "id_enum" => "EquipmentType",
            ]);
            array_push($enums, $enum) ;
        }
        return response()->json($enums) ; 
    }

    /**
     * Function call by EnumManagement.vue with the route : /equipment/enum/type/add (post)
    * Add a new field for the equipment type enum in the data base
     */

    public function add_enum_type (Request $request){
        $enum_already_exist=EnumEquipmentType::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_eq_type' => ["The value of the field for the new equipment type already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_type=EnumEquipmentType::create([
            'value' => $request->value, 
        ]);
    }

    /**
     * Function call by EnumManagement.vue with the route : /equipment/enum/type/verif/{id} (post)
    * Verify if we can update the equipment type enum in the data base
    * The id parameter correspond to the id of the enumEquipmentType we want to update
     */
    public function verif_enum_type(Request $request, $id){
        $enum_already_exist=EnumEquipmentType::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_eq_type' => ["The value of the field for the equipment type already exist in the data base"]
                ]
            ], 429);
        }
        return response()->json($id) ;
    }

    /**
     * Function call by EnumManagement.vue with the route : /equipment/enum/type/analyze/{id} (post)
    * Analyze the equipment we want to update
    * The id parameter correspond to the id of the enumEquipmentType we want to update
     */
    public function analyze_enum_type(Request $request, $id){
        $equipmentTemps=EquipmentTemp::where('enumType_id', '=', $id)->get() ;
        $equipments=array() ; 
        $validated_eq=array() ;
       foreach($equipmentTemps as $equipmentTemp){
            $equipment=([
                "eqTemp_id" => $equipmentTemp->id,
                "name" => $equipmentTemp->equipment->eq_name,
                "internalReference" => $equipmentTemp->equipment->eq_internalReference,
            ]);
            if($equipmentTemp->eqTemp_lifeSheetCreated==1){
                array_push($validated_eq, $equipment) ;
            }else{
                array_push($equipments, $equipment) ;
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
     * Function call by EnumManagement.vue with the route : /dimension/enum/type/update/{id} (post)
    * Update the dimension type enum in the data base
    * The id parameter correspond to the id of the enumDimensionType we want to update
     */

    public function update_enum_type (Request $request, $id){
        $enum_type=EnumEquipmentType::findOrFail($id) ; 
        $enum_type->update([
            'value' => $request->value, 
        ]);
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
            ]);

            //We created a new enregistrement of history for explain the reason of the enum updates
            $HistoryController= new HistoryController() ; 
            $HistoryController->add_history_for_eq($eq->id, $request) ; 
        }

    }


    /**
     * Function call by EnumManagement.vue with the route : /equipment/enum/type/delete/{id} (post)
    * Add a new field for the equipment type enum in the data base
    * The id parameter correspond to the id of the enumEquipmentType we want to delete
     */

    public function delete_enum_type ($id){
        $enum_type=EnumEquipmentType::findOrFail($id) ; 
        $eqLinked=EquipmentTemp::where('enumType_id', '=', $id)->get() ; 
        if (count($eqLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_eq_type' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_type->delete() ; 
    }


}
