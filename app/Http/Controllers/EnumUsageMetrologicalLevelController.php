<?php

/*
* Filename : EnumUsageMetrologicalLevelController.php 
* Creation date : 21 Jun 2022
* Update date : 21 Jun 2022
* This file is used to link the view files and the database that concern the enumUsageMetrologicalLevel table. 
* For example : send the fields of the enum, add a new field...
*/ 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\MmeUsage;
use App\Models\EnumUsageMetrologicalLevel;

class EnumUsageMetrologicalLevelController extends Controller
{
   
    /**
     * Function call by MmeUsageForm.vue with the route : /usage/enum/metrologicalLevel (get)
    * Get the fields of the usage metrological level enum in the data base and give them to the vue for print them in the form 
    * @return \Illuminate\Http\Response
    */

    public function send_enum_metrologicalLevel (){
        $enums_metrologicalLevel=DB::table('enum_usage_metrological_levels')->orderBy('value', 'asc')->get() ;   
        return response()->json($enums_metrologicalLevel) ; 
    }

    /**
     * Function call by EnumManagement.vue with the route : /usage/enum/metrologicalLevel/add (post)
    * Add a new field for the usage for enum in the data base
     */

    public function add_enum_metrologicalLevel (Request $request){
        $enum_already_exist=EnumUsageMetrologicalLevel::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_metrologicalLevel' => ["The value of the field for the new usage metrological level already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_metrologicalLevel=EnumUsageMetrologicalLevel::create([
            'value' => $request->value, 
        ]);
    }

    /**
     * Function call by EnumManagement.vue with the route : /usage/enum/metrologicalLevel/update/{id} (post)
    * Add a new field for the metrological level enum in the data base
    * The id parameter correspond to the id of the EnumUsageMetrologicalLevel we want to update
     */

    public function update_enum_metrologicalLevel (Request $request, $id){
        $enum_metrologicalLevel=EnumUsageMetrologicalLevel::findOrFail($id) ; 
        $enum_already_exist=EnumUsageMetrologicalLevel::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_metrologicalLevel' => ["The value of the field for the usage metrological level already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_metrologicalLevel->update([
            'value' => $request->value, 
        ]); 
    }

    /**
     * Function call by EnumManagement.vue with the route : /usage/enum/metrologicalLevel/delete/{id} (post)
    * Add a new field for the metrological level enum in the data base
    * The id parameter correspond to the id of the EnumUsageMetrologicalLevel we want to delete
     */

    public function delete_enum_metrologicalLevel ($id){
        $enum_metrologicalLevel=EnumUsageMetrologicalLevel::findOrFail($id) ; 
        $UsageLinked=MmeUsage::where('enumUsageMetrologicalLevel_id', '=', $id)->get() ; 
        if (count($UsageLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_metrologicalLevel' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_metrologicalLevel->delete() ; 
    }
}
