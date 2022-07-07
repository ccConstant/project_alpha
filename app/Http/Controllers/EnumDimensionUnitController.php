<?php

/*
* Filename : EnumDimensionUnitController.php 
* Creation date : 24 May 2022
* Update date : 24 May 2022
* This file is used to link the view files and the database that concern the enumDimensionUnit table. 
* For example : send the fields of the enum, add a new field...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EnumDimensionUnit;
use App\Models\Dimension ; 

class EnumDimensionUnitController extends Controller
{
      /**
     * Function call by EquipmentDimForm.vue with the route : /dimension/enum/unit (get)
    * Get the fields of the dimension unit enum to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */

    public function send_enum_unit (){
        $enums_unit=DB::table('enum_dimension_units')->orderBy('value', 'asc')->get() ;  
        return response()->json($enums_unit) ; 
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
     * Function call by EnumManagement.vue with the route : /dimension/enum/unit/update/{id} (post)
    * Add a new field for the dimension unit enum in the data base
    * The id parameter correspond to the id of the enumDimensionUnit we want to update
     */

    public function update_enum_unit (Request $request, $id){
        $enum_already_exist=EnumDimensionUnit::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_dim_unit' => ["The value of the field for the dimension unit already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_unit=EnumDimensionUnit::findOrFail($id) ; 
        $enum_unit->update([
            'value' => $request->value, 
        ]); 
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












