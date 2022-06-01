<?php

/*
* Filename : EnumStateNameController.php 
* Creation date : 24 May 2022
* Update date : 24 May 2022
* This file is used to link the view files and the database that concern the enumStateName table. 
* For example : send the fields of the enum, add a new field...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\EnumStateName;
use App\Models\State;

class EnumStateNameController extends Controller
{
    /**
     * Function call by EquipmentDimForm.vue with the route : /state/enum/name (get)
    * Get the fields of the state name enum to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */

    public function send_enum_name (){
        $enums_name=DB::select(DB::raw('SELECT value, id FROM enum_state_names'));
        return response()->json($enums_name) ; 
    }

     /**
     * Function call by EnumManagement.vue with the route : /state/enum/name/add (post)
    * Add a new field for the state name enum in the data base
     */

    public function add_enum_name (Request $request){
        $enum_already_exist=EnumStateName::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_state_name' => ["The value of the field for the new statename already exist in the data base"]
                ]
            ], 429);
        }
        
        
        $enum_name=EnumStateName::create([
            'value' => $request->value, 
        ]);
    }


    /**
     * Function call by EnumManagement.vue with the route : /state/enum/name/update/{id} (post)
    * Add a new field for the state name enum in the data base
    * The id parameter correspond to the id of the enumStateName we want to update
     */

    public function update_enum_name (Request $request, $id){
        $enum_already_exist=EnumStateName::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_state_name' => ["The value of the field for the state name already exist in the data base"]
                ]
            ], 429);
        }
        $enum_name=EnumStateName::findOrFail($id) ; 
        $enum_name->update([
            'value' => $request->value, 
        ]); 
    }

    /**
     * Function call by EnumManagement.vue with the route : /state/enum/name/delete/{id} (post)
    * Add a new field for the state name enum in the data base
    * The id parameter correspond to the id of the enumStateName we want to delete
     */

    public function delete_enum_name ($id){
        $enum_name=EnumStateName::findOrFail($id) ; 
        $stateLinked=State::where('enumStateName_id', '=', $id)->get() ; 
        if (count($stateLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_state_name' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_name->delete() ; 
    }
}


