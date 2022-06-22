<?php

/*
* Filename : EnumVerificationRequiredSkillController.php 
* Creation date : 21 Jun 2022
* Update date : 21 Jun 2022
* This file is used to link the view files and the database that concern the EnumVerificationRequiredSkill table. 
* For example : send the fields of the enum, add a new field...
*/ 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Verification;
use App\Models\EnumVerificationRequiredSkill;
use Illuminate\Support\Facades\DB ; 

class EnumVerificationRequiredSkillController extends Controller
{

    /**
     * Function call by VerificationFom.vue with the route : /verification/enum/requiredSkill (get)
    * Get the fields of the required skill enum in the data base and give them to the vue for print them in the form 
    * @return \Illuminate\Http\Response
    */

    public function send_enum_verificationRequiredSkill (){
        $enums_verifRequiredSkills=DB::select(DB::raw('SELECT DISTINCT value, id FROM enum_verification_required_skills'));
        return response()->json($enums_verifRequiredSkills) ; 
    }

    /**
     * Function call by EnumManagement.vue with the route : '/verification/enum/requiredSkill/add (post)
    * Add a new field for the required skill enum in the data base
     */

    public function add_enum_requiredSkill (Request $request){
        $enum_already_exist=EnumVerificationRequiredSkill::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_requiredSkill' => ["The value of the field for the new required skill already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_requiredSkill=EnumVerificationRequiredSkill::create([
            'value' => $request->value, 
        ]);
    }

    /**
     * Function call by EnumManagement.vue with the route : /verification/enum/requiredSkill/update/{id}'(post)
    * Add a new field for the required skill enum in the data base
    * The id parameter correspond to the id of the enumRequiredSkill we want to update
     */

    public function update_enum_requiredSkill (Request $request, $id){
        $enum_requiredSkill=EnumVerificationRequiredSkill::findOrFail($id) ; 
        $enum_already_exist=EnumVerificationRequiredSkill::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_requiredSkill' => ["The value of the field for the required skill already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_requiredSkill->update([
            'value' => $request->value, 
        ]); 
    }

    /**
     * Function call by EnumManagement.vue with the route : /verification/enum/requiredSkill/delete/{id} (post)
    * Add a new field for the risk for enum in the data base
    * The id parameter correspond to the id of the enumRiskFor we want to delete
     */

    public function delete_enum_requiredSkill($id){
        $enum_requiredSkill=EnumVerificationRequiredSkill::findOrFail($id) ; 
        $VerifLinked=Verification::where('enumRequiredSkill_id', '=', $id)->get() ; 
        if (count($VerifLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_requiredSkill' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_requiredSkill->delete() ; 
    }
}
