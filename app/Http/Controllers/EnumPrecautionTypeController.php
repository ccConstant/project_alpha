<?php

/*
* Filename : EnumPrecautionTypeController.php 
* Creation date : 21 Jun 2022
* Update date : 21 Jun 2022
* This file is used to link the view files and the database that concern the enumPrecautionType table. 
* For example : send the fields of the enum, add a new field...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\Precaution;
use App\Models\EnumPrecautionType;

class EnumPrecautionTypeController extends Controller{
     /**
     * Function call by EquipmentPrecautionForm.vue with the route : /precaution/enum/type' (get)
    * Get the fields of the precaution type enum to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */
    public function send_enum_type (){
        $enums_type=DB::table('enum_precaution_types')->orderBy('value', 'asc')->get() ;   
        return response()->json($enums_type) ; 
    }

    /**
     * Function call by EnumManagement.vue with the route : /precaution/enum/type/add (post)
    * Add a new field for the precaution type enum in the data base
     */

    public function add_enum_type (Request $request){
        $enum_already_exist=EnumPrecautionType::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_prctn_type' => ["The value of the field for the new precaution type already exist in the data base"]
                ]
            ], 429);
        }
        
        
        $enum_type=EnumPrecautionType::create([
            'value' => $request->value, 
        ]);
    }

    /**
     * Function call by EnumManagement.vue with the route : /precaution/enum/type/update/{id} (post)
    * Add a new field for the precaution type enum in the data base
    * The id parameter correspond to the id of the EnumPrecautionType we want to update
     */

    public function update_enum_type (Request $request, $id){
        $enum_type=EnumPrecautionType::findOrFail($id) ; 
        if ($enum_type->value=="Preservation" || $enum_type->value=="Handling" || $enum_type->value=="Storage"){
            return response()->json([
                'errors' => [
                    'enum_prctn_type' => ["You can't modify this enum because it belongs of the principals precaution type"]
                ]
            ], 429);
        }
        $enum_already_exist=EnumPrecautionType::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_prctn_type' => ["The value of the field for the precaution type already exist in the data base"]
                ]
            ], 429);
        }
        
        

        $enum_type->update([
            'value' => $request->value, 
        ]); 
    }

    /**
     * Function call by EnumManagement.vue with the route : /precaution/enum/type/delete/{id} (post)
    * Add a new field for the precaution type enum in the data base
    * The id parameter correspond to the id of the EnumPrecautionType we want to delete
     */

    public function delete_enum_type ($id){
        $enum_type=EnumPrecautionType::findOrFail($id) ; 
        if ($enum_type->value=="Preservation" || $enum_type->value=="Handling" || $enum_type->value=="Storage"){
            return response()->json([
                'errors' => [
                    'enum_prctn_type' => ["You can't delete this enum because it belongs of the principals precaution type"]
                ]
            ], 429);
        }
        $prctnLinked=Precaution::where('enumPrecautionType_id', '=', $id)->get() ; 
        if (count($prctnLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_prctn_type' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_type->delete() ; 
    }
}

