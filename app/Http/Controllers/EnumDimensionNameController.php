<?php

/*
* Filename : EnumDimensionNameController.php 
* Creation date : 24 May 2022
* Update date : 24 May 2022
* This file is used to link the view files and the database that concern the enumDimensionName table. 
* For example : send the fields of the enum, add a new field...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EnumDimensionName;
use App\Models\Dimension;

class EnumDimensionNameController extends Controller
{
    /**
     * Function call by EquipmentDimForm.vue with the route : /dimension/enum/name (get)
    * Get the fields of the dimension name enum to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */

    public function send_enum_name (){
        $enums_name=EnumDimensionName::all() ;
        return response()->json($enums_name) ; 
    }

     /**
     * Function call by EnumManagement.vue with the route : /dimension/enum/name/add (post)
    * Add a new field for the dimension name enum in the data base
     */

    public function add_enum_name (Request $request){
        $enum_already_exist=EnumDimensionName::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_dim_name' => ["The value of the field for the new dimension name already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_name=EnumDimensionName::create([
            'value' => $request->value, 
        ]);
    }


    /**
     * Function call by EnumManagement.vue with the route : /dimension/enum/name/update/{id} (post)
    * Add a new field for the dimension name enum in the data base
    * The id parameter correspond to the id of the enumDimensionName we want to update
     */

    public function update_enum_name (Request $request, $id){
        $enum_already_exist=EnumDimensionName::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_dim_name' => ["The value of the field for the dimension name already exist in the data base"]
                ]
            ], 429);
        }

        $enum_name=EnumDimensionName::findOrFail($id) ; 
        $enum_name->update([
            'value' => $request->value, 
        ]); 
    }

    /**
     * Function call by EnumManagement.vue with the route : /dimension/enum/name/delete/{id} (post)
    * Add a new field for the dimension name enum in the data base
    * The id parameter correspond to the id of the enumDimensionName we want to delete
     */

    public function delete_enum_name ($id){

        $enum_name=EnumDimensionName::findOrFail($id) ; 
        $dimLinked=Dimension::where('enumDimensionName_id', '=', $id)->get() ; 
        if (count($dimLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_dim_name' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_name->delete() ; 
    }
}





