<?php

/*
* Filename : EnumPowerTypeController.php 
* Creation date : 24 May 2022
* Update date : 24 May 2022
* This file is used to link the view files and the database that concern the enumPowerType table. 
* For example : send the fields of the enum, add a new field...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\Power;
use App\Models\EnumPowerType;

class EnumPowerTypeController extends Controller{
     /**
     * Function call by EquipmentPowForm.vue with the route : /power/enum/type (get)
    * Get the fields of the power type enum to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */
    public function send_enum_type (){
        $enums_type=DB::table('enum_power_types')->orderBy('value', 'asc')->get() ;   
        return response()->json($enums_type) ; 
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
     * Function call by EnumManagement.vue with the route : /power/enum/type/update/{id} (post)
    * Add a new field for the power type enum in the data base
    * The id parameter correspond to the id of the enumPowerType we want to update
     */

    public function update_enum_type (Request $request, $id){
        $enum_already_exist=EnumPowerType::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_pow_type' => ["The value of the field for the power type already exist in the data base"]
                ]
            ], 429);
        }
        
        
        $enum_type=EnumPowerType::findOrFail($id) ; 
        $enum_type->update([
            'value' => $request->value, 
        ]); 
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

