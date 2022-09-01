<?php

/*
* Filename : EnumVerifAcceptanceAuthorityController.php 
* Creation date : 21 Jun 2022
* Update date : 21 Jun 2022
* This file is used to link the view files and the database that concern the enumVerifAcceptanceAuthority table. 
* For example : send the fields of the enum, add a new field...
*/ 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\Verification;
use App\Models\EnumVerifAcceptanceAuthority;

class EnumVerifAcceptanceAuthorityController extends Controller
{
   
    /**
     * Function call by MmeUsageForm.vue with the route : /usage/enum/verifAcceptanceAuthority (get)
    * Get the fields of the usage verifAcceptanceAuthority enum in the data base and give them to the vue for print them in the form 
    * @return \Illuminate\Http\Response
    */

    public function send_enum_verifAcceptanceAuthority (){
        $enums_verifAcceptanceAuthority=DB::table('enum_verif_acceptance_authorities')->orderBy('value', 'asc')->get() ;  
        return response()->json($enums_verifAcceptanceAuthority) ; 
    }

    /**
     * Function call by EnumManagement.vue with the route : /usage/enum/verifAcceptanceAuthority/add (post)
    * Add a new field for the usage for enum in the data base
     */

    public function add_enum_verifAcceptanceAuthority (Request $request){
        $enum_already_exist=EnumVerifAcceptanceAuthority::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_verifAcceptanceAuthority' => ["The value of the field for the new usage verif Acceptance Authority already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_verifAcceptanceAuthority=EnumVerifAcceptanceAuthority::create([
            'value' => $request->value, 
        ]);
    }

    /**
     * Function call by EnumManagement.vue with the route : /usage/enum/verifAcceptanceAuthority/update/{id} (post)
    * Add a new field for the verifAcceptanceAuthority enum in the data base
    * The id parameter correspond to the id of the EnumverifAcceptanceAuthority we want to update
     */

    public function update_enum_verifAcceptanceAuthority (Request $request, $id){
        $enum_verifAcceptanceAuthority=EnumVerifAcceptanceAuthority::findOrFail($id) ; 
        $enum_already_exist=EnumVerifAcceptanceAuthority::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_verifAcceptanceAuthority' => ["The value of the field for the usage verif Acceptance Authority already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_verifAcceptanceAuthority->update([
            'value' => $request->value, 
        ]); 
    }

    /**
     * Function call by EnumManagement.vue with the route : /risk/enum/verifAcceptanceAuthority/delete/{id} (post)
    * Add a new field for the verifAcceptanceAuthority enum in the data base
    * The id parameter correspond to the id of the EnumverifAcceptanceAuthority we want to delete
     */

    public function delete_enum_verifAcceptanceAuthority ($id){
        $enum_verifAcceptanceAuthority=EnumVerifAcceptanceAuthority::findOrFail($id) ; 
        $VerificationLinked=Verification::where('EnumVerifAcceptanceAuthority_id', '=', $id)->get() ; 
        if (count($VerificationLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_verifAcceptanceAuthority' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_verifAcceptanceAuthority->delete() ; 
    }
}