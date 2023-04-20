<?php

/*
* Filename : EnumDimensionUnitController.php 
* Creation date : 24 May 2022
* Update date : 14 Feb 2023
* This file is used to link the view files and the database that concern the enumDimensionUnit table. 
* For example : send the fields of the enum, add a new field...
*/ 


namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\SW01\EnumDimensionUnit;
use App\Models\SW01\Dimension ; 
use App\Models\SW01\EquipmentTemp ;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Controller;

class EnumDimensionUnitController extends Controller
{
      /**
     * Function call by EquipmentDimForm.vue with the route : /dimension/enum/unit (get)
    * Get the fields of the dimension unit enum to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */

    public function send_enum_unit (){
        $enums_units=DB::table('enum_dimension_units')->orderBy('value', 'asc')->get() ;  
        $enums=array() ;
        foreach($enums_units as $enum_unit){
            $enum=([
                "value" => $enum_unit->value,
                "id" => $enum_unit->id,
                "id_enum" => "DimensionUnit",
            ]);
            array_push($enums, $enum) ;
        }
        return response()->json($enums) ; 
    }

     /**
     * Function call by EnumManagement.vue with the route : /dimension/enum/unit/add (post)
    * Add a new field for the dimension unit enum in the data base
     */

    public function add_enum_unit (Request $request){
        $enum_already_exist=EnumDimensionUnit::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_dim_unit' => ["The value of the field for the new dimension unit already exist in the data base"]
                ]
            ], 429);
        }
        
        
        $enum_unit=EnumDimensionUnit::create([
            'value' => $request->value, 
        ]);
    }

    /**
     * Function call by EnumManagement.vue with the route : /dimension/enum/unit/verif/{id} (post)
    * Verify if we can update the dimension unit enum in the data base
    * The id parameter correspond to the id of the enumDimensionUnit we want to update
     */
    public function verif_enum_unit(Request $request, $id){
        $enum_already_exist=EnumDimensionUnit::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_dim_unit' => ["The value of the field for the dimension unit already exist in the data base"]
                ]
            ], 429);
        }
        return response()->json($id) ;
    }

    /**
     * Function call by EnumManagement.vue with the route : /dimension/enum/unit/analyze/{id} (post)
    * Analyze the equipment connected to the dimension unit enum we want to update
    * The id parameter correspond to the id of the enumDimensionUnit we want to update
     */
    public function analyze_enum_unit(Request $request, $id){
        $dimensions=Dimension::where('enumDimensionUnit_id', '=', $id)->get() ;
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
     * Function call by EnumManagement.vue with the route : /dimension/enum/unit/update/{id} (post)
    * Update the dimension unit enum in the data base
    * The id parameter correspond to the id of the enumDimensionUnit we want to update
     */

    public function update_enum_unit (Request $request, $id){
        $enum_unit=EnumDimensionUnit::findOrFail($id) ; 
        $enum_unit->update([
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
     * Function call by EnumManagement.vue with the route : /dimension/enum/unit/delete/{id} (post)
    * Add a new field for the dimension unit enum in the data base
    * The id parameter correspond to the id of the enumDimensionUnit we want to delete
     */

    public function delete_enum_unit ($id){
        $enum_unit=EnumDimensionUnit::findOrFail($id) ; 
        $dimLinked=Dimension::where('enumDimensionUnit_id', '=', $id)->get() ; 
        if (count($dimLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_dim_unit' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_unit->delete() ; 
    }
}












