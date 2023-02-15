<?php

/*
* Filename : EnumUsageMetrologicalLevelController.php 
* Creation date : 21 Jun 2022
* Update date : 14 Feb 2023
* This file is used to link the view files and the database that concern the enumUsageMetrologicalLevel table. 
* For example : send the fields of the enum, add a new field...
*/ 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\MmeUsage;
use App\Models\EnumUsageMetrologicalLevel;
use App\Models\MmeTemp;
use App\Http\Controllers\HistoryController;

class EnumUsageMetrologicalLevelController extends Controller
{
   
    /**
     * Function call by MmeUsageForm.vue with the route : /usage/enum/metrologicalLevel (get)
    * Get the fields of the usage metrological level enum in the data base and give them to the vue for print them in the form 
    * @return \Illuminate\Http\Response
    */

    public function send_enum_metrologicalLevel (){
        $enums_metrologicalLevel=DB::table('enum_usage_metrological_levels')->orderBy('value', 'asc')->get() ;   
        $enums=array() ;
        foreach($enums_metrologicalLevel as $enum_metrologicalLevel){
            $enum=([
                "value" => $enum_metrologicalLevel->value,
                "id" => $enum_metrologicalLevel->id,
                "id_enum" => "MetrologicalLevel",
            ]);
            array_push($enums, $enum) ;
        }
        return response()->json($enums) ; 
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
     * Function call by EnumManagement.vue with the route : /usage/enum/metrologicalLevel/verif/{id} (post)
    * Verify if we can update the usage metrologicalLevel enum in the data base
    * The id parameter correspond to the id of the enumMetrologicalLevel we want to update
     */
    public function verif_enum_metrologicalLevel(Request $request, $id){
        $enum_already_exist=EnumUsageMetrologicalLevel::where('value', '=', $request->value)->where('id','<>', $id)->get();
        $enum=EnumUsageMetrologicalLevel::findOrfail($id) ;
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_metrologicalLevel' => ["The value of the field for the usage metrological level already exist in the data base"]
                ]
            ], 429);
        }
        return response()->json($id) ;
    }

    /**
    * Function call by EnumManagement.vue with the route : /usage/enum/metrologicalLevel/analyze/{id} (post)
    * Analyze the MME connected to the usage metrological level enum we want to update
    * The id parameter correspond to the id of the enumMetrologicalLevel we want to update
     */
    public function analyze_enum_metrologicalLevel(Request $request, $id){
        $usages=MmeUsage::where('enumUsageMetrologicalLevel_id', '=', $id)->get() ;
        $mmes=array() ; 
        $validated_mme=array() ;
        foreach($usages as $usage){
            $mme_temp=$usage->mme_temps ;
            $mme=([
                "mmeTemp_id" => $mme_temp->id,
                "name" => $mme_temp->mme->mme_name,
                "internalReference" => $mme_temp->mme->mme_internalReference,
            ]);
            if($mme_temp->mmeTemp_lifeSheetCreated==1){
                array_push($validated_mme, $mme) ;
            }else{
                array_push($mmes, $mme) ;
            }
            
        }
        $final=([
            "id" => $id,
            "mmes" => $mmes,
            "validated_mme" => $validated_mme,
        ]);

        return response()->json($final) ;
    }


    /**
     * Function call by EnumManagement.vue with the route : /usage/enum/metrologicalLevel/update/{id} (post)
    * Update the verification verifAcceptanceAuthority enum in the data base
    * The id parameter correspond to the id of the enumVerifAcceptanceAuthority we want to update
     */

    public function update_enum_metrologicalLevel (Request $request, $id){
        $enum=EnumUsageMetrologicalLevel::findOrFail($id) ; 
        $enum->update([
            'value' => $request->value, 
        ]);
        foreach ($request->validated_mme as $mme){
            $mme_temp=MmeTemp::findOrFail($mme['mmeTemp_id']) ; 
            $mme=$mme_temp->mme ;
 
            $version=$mme->mme_nbrVersion+1;
            $mme->update([
                'mme_nbrVersion' => $version
            ]);
            $mme_temp->update([
                'mmeTemp_lifeSheetCreated' => 0, 
                'qualityVerifier_id' => NULL,
                'technicalVerifier_id' => NULL,
                'mmeTemp_version' => $version,
            ]);

            //We created a new enregistrement of history for explain the reason of the enum updates
            $HistoryController= new HistoryController() ; 
            $HistoryController->add_history_for_mme($mme->id, $request) ; 
        }



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
