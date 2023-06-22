<?php

/*
* Filename : EnumUsageMetrologicalLevelController.php 
* Creation date : 21 Jun 2022
* Update date : 7 Jun 2023
* This file is used to link the view files and the database that concern the enumUsageMetrologicalLevel table. 
* For example : send the fields of the enum, add a new field...
*/ 

namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\SW01\MmeUsage;
use App\Models\SW01\EnumUsageMetrologicalLevel;
use App\Models\SW01\MmeTemp;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Controller;
use App\Models\SW01\MmeState;
use Carbon\Carbon;

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
        $id_mmes=array() ;
        $id_mmes_validated=array() ;
        $cpt=0;
        $cpt_validated=0;
        foreach($usages as $usage){
            $mme_temp=$usage->mme_temps ;
            $mme=([
                "mmeTemp_id" => $mme_temp->id,
                "name" => $mme_temp->mme->mme_name,
                "internalReference" => $mme_temp->mme->mme_internalReference,
            ]);
            if($mme_temp->mmeTemp_lifeSheetCreated==1){
                foreach($id_mmes_validated as $id_mme_validated){
                    if($id_mme_validated!=$mme_temp->id){
                        $cpt_validated++;
                    }
                }
                if ($cpt_validated==count($id_mmes_validated)){
                    array_push($validated_mme, $mme) ;
                    array_push($id_mmes_validated, $mme_temp->id) ;
                }
                $cpt_validated=0;
            }else{
                foreach($id_mmes as $id_mme){
                    if($id_mme!=$mme_temp->id){
                        $cpt++;
                    }
                }
                if ($cpt==count($id_mmes)){
                    array_push($mmes, $mme) ;
                    array_push($id_mmes, $mme_temp->id) ;
                }
                $cpt=0;
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
        if ($request->validated_mme!=NULL){
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
                    'eqTemp_signatureDate' => NULL,
                ]);

                $states=$mme_temp->states;
                if ($states!==NULL){
                    $mostRecentlyState=NULL ;
                    $first=true ;
                    foreach($states as $state){
                        if ($first){
                            $mostRecentlyState=$state ;
                            $first=false;
                        }else{
                            $date=$state->created_at ;
                            $date2=$mostRecentlyState->created_at;
                            if ($date>=$date2){
                                $mostRecentlyState=$state ;
                            }
                        }
                    }
                    if ($mostRecentlyState!=NULL){
                        $mostRecentlyState->update([
                            'state_endDate' => Carbon::now('Europe/Paris'),
                        ]);
                    }
                }

                //Creation of a new state
                $newState=MmeState::create([
                    'state_remarks' => "MME Enum Update (update usage metrological level): new version of life sheet created",
                    'state_startDate' =>  Carbon::now('Europe/Paris'),
                    'state_validate' => "validated",
                    'state_name' => "Waiting_for_referencing"
                ]) ;

                $newState->mme_temps()->attach($mme_temp);

                

                //We created a new enregistrement of history for explain the reason of the enum updates
                $HistoryController= new HistoryController() ; 
                $HistoryController->add_history_for_mme($mme->id, $request) ; 
            }
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
