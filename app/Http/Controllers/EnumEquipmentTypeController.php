<?php

/*
* Filename : EnumEquipmentTypeController.php 
* Creation date : 24 May 2022
* Update date : 24 May 2022
* This file is used to link the view files and the database that concern the enumEquipmentType table. 
* For example : send the fields of the enum, add a new field...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\Equipment;
use App\Models\EquipmentTemp;
use App\Models\EnumEquipmentType;

class EnumEquipmentTypeController extends Controller
{
    /**
     * Function call by EquipmentIDForm.vue with the route : /equipment/enum/type (get)
    * Get the fields of the equipment type enum to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */

    public function send_enum_type (){
        $enums_type=EnumEquipmentType::all() ;
        return response()->json($enums_type) ; 
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
     * Function call by EnumManagement.vue with the route : /equipment/enum/type/update/{id} (post)
    * Add a new field for the equipment type enum in the data base
    * The id parameter correspond to the id of the enumEquipmentType we want to update
     */

    public function update_enum_type(Request $request, $id){
        $enum_already_exist=EnumEquipmentType::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_eq_type' => ["The value of the field for the equipment type already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_type=EnumEquipmentType::findOrFail($id) ; 
        $enum_type->update([
            'value' => $request->value, 
        ]); 
        return response()->json($request->value) ; 
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
