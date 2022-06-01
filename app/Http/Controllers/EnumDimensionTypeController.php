<?php

/*
* Filename : EnumDimensionTypeController.php 
* Creation date : 24 May 2022
* Update date : 24 May 2022
* This file is used to link the view files and the database that concern the enumDimensionType table. 
* For example : send the fields of the enum, add a new field...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EnumDimensionType;
use App\Models\Dimension ; 

class EnumDimensionTypeController extends Controller

{
    /**
     * Function call by EquipmentDimForm.vue with the route : /dimension/enum/type (get)
    * Get the fields of the dimension type enum to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */

    public function send_enum_type (){
        $enums_type=DB::select(DB::raw('SELECT value, id FROM enum_dimension_types'));
        return response()->json($enums_type) ; 
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
     * Function call by EnumManagement.vue with the route : /dimension/enum/type/update/{id} (post)
    * Add a new field for the dimension type enum in the data base
    * The id parameter correspond to the id of the enumDimensionType we want to update
     */

    public function update_enum_type (Request $request, $id){
        $enum_already_exist=EnumDimensionType::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_dim_type' => ["The value of the field for the dimension type already exist in the data base"]
                ]
            ], 429);
        }
        
        
        $enum_type=EnumDimensionType::findOrFail($id) ; 
        $enum_type->update([
            'value' => $request->value, 
        ]); 
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





