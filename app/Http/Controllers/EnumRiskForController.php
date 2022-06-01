<?php

/*
* Filename : EnumRiskForController.php 
* Creation date : 24 May 2022
* Update date : 24 May 2022
* This file is used to link the view files and the database that concern the enumRiskFor table. 
* For example : send the fields of the enum, add a new field...
*/ 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\Risk;
use App\Models\EnumRiskFor;

class EnumRiskForController extends Controller
{
   
    /**
     * Function call by EquipmentRiskForm.vue with the route : /risk/enum/riskfor (get)
    * Get the fields of the risk target enum in the data base and give them to the vue for print them in the form 
    * @return \Illuminate\Http\Response
    */

    public function send_enum_riskFor (){
        $enums_riskFor=DB::select(DB::raw('SELECT DISTINCT value, id FROM enum_risk_fors'));
        return response()->json($enums_riskFor) ; 
    }

    /**
     * Function call by EnumManagement.vue with the route : /risk/enum/riskfor/add (post)
    * Add a new field for the risk for enum in the data base
     */

    public function add_enum_riskFor (Request $request){
        $enum_already_exist=EnumRiskFor::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_riskfor' => ["The value of the field for the new risk target already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_riskFor=EnumRiskFor::create([
            'value' => $request->value, 
        ]);
    }

    /**
     * Function call by EnumManagement.vue with the route : /risk/enum/riskfor/update/{id} (post)
    * Add a new field for the risk for enum in the data base
    * The id parameter correspond to the id of the enumRiskFor we want to update
     */

    public function update_enum_riskFor (Request $request, $id){
        $enum_already_exist=EnumRiskFor::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_riskfor' => ["The value of the field for the risk target already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_riskFor=EnumRiskFor::findOrFail($id) ; 
        $enum_riskFor->update([
            'value' => $request->value, 
        ]); 
    }

    /**
     * Function call by EnumManagement.vue with the route : /risk/enum/riskFor/delete/{id} (post)
    * Add a new field for the risk for enum in the data base
    * The id parameter correspond to the id of the enumRiskFor we want to delete
     */

    public function delete_enum_riskFor ($id){
        $enum_riskFor=EnumRiskFor::findOrFail($id) ; 
        $riskLinked=Risk::where('enumRiskFor_id', '=', $id)->get() ; 
        if (count($riskLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_riskfor' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_riskFor->delete() ; 
    }
}
