<?php

/*
* Filename : EnumDimensionTypeController.php 
* Creation date : 24 May 2022
* Update date : 9 Feb 2023
* This file is used to link the view files and the database that concern the enumDimensionType table. 
* For example : send the fields of the enum, add a new field...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EnumDimensionType;
use App\Models\Dimension ; 
use App\Models\EquipmentTemp ;

class EnumDimensionTypeController extends Controller

{
    /**
     * Function call by EquipmentDimForm.vue with the route : /dimension/enum/type (get)
    * Get the fields of the dimension type enum to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */

    public function send_enum_type (){
        $enums_types=DB::table('enum_dimension_types')->orderBy('value', 'asc')->get() ; 
        $enums=array() ;
        foreach($enums_types as $enum_type){
            $enum=([
                "value" => $enum_type->value,
                "id" => $enum_type->id,
                "id_enum" => "DimensionType",
            ]);
            array_push($enums, $enum) ;
        }
        return response()->json($enums) ; 
    }

     /**
     * Function call by EnumManagement.vue with the route : /dimension/enum/type/add (post)
    * Add a new field for the dimension type enum in the data base
     */

    public function add_enum_type (Request $request){
        $enum_already_exist=EnumDimensionType::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_dim_type' => ["The value of the field for the new dimension type already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_type=EnumDimensionType::create([
            'value' => $request->value, 
        ]);
    }

    /**
     * Function call by EnumManagement.vue with the route : /dimension/enum/type/verif/{id} (post)
    * Verify if we can update the dimension type enum in the data base
    * The id parameter correspond to the id of the enumDimensionType we want to update
     */
    public function verif_enum_type(Request $request, $id){
        $enum_already_exist=EnumDimensionType::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_dim_type' => ["The value of the field for the dimension type already exist in the data base"]
                ]
            ], 429);
        }
        return response()->json($id) ;
    }

    /**
     * Function call by EnumManagement.vue with the route : /dimension/enum/type/analyze/{id} (post)
    * Analyze the equipment connected to the dimension type enum we want to update
    * The id parameter correspond to the id of the enumDimensionType we want to update
     */
    public function analyze_enum_type(Request $request, $id){
        $dimensions=Dimension::where('enumDimensionType_id', '=', $id)->get() ;
        $equipments=array() ; 
        $validated_eq=array() ;
        foreach($dimensions as $dimension){
            $equipment_temp=$dimension->equipment_temps ;
            $equipment=([
                "eqTemp_id" => $equipment_temp->id,
                "name" => $equipment_temp->equipment->eq_name,
                "internalReference" => $equipment_temp->equipment->eq_internalReference,
            ]);
            if($equipment_temp->eqTemp_lifeSheetCreated==1){
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
        $enum_type=EnumDimensionType::findOrFail($id) ; 
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
        }



    }

    /**
     * Function call by EnumManagement.vue with the route : /dimension/enum/type/delete/{id} (post)
    * Add a new field for the dimension type enum in the data base
    * The id parameter correspond to the id of the enumDimensionType we want to delete
     */

    public function delete_enum_type ($id){
        $enum_type=EnumDimensionType::findOrFail($id) ; 
        $dimLinked=Dimension::where('enumDimensionType_id', '=', $id)->get() ; 
        if (count($dimLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_dim_type' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_type->delete() ; 
    }
}





