<?php

/*
* Filename : EnumVerificationRequiredSkillController.php 
* Creation date : 21 Jun 2022
* Update date : 7 Jun 2023
* This file is used to link the view files and the database that concern the EnumVerificationRequiredSkill table. 
* For example : send the fields of the enum, add a new field...
*/ 

namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use App\Models\SW01\Verification;
use App\Models\SW01\EnumVerificationRequiredSkill;
use App\Models\SW01\MmeTemp;
use Illuminate\Support\Facades\DB ; 
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Controller;
use App\Models\SW01\MmeState;
use Carbon\Carbon;

class EnumVerificationRequiredSkillController extends Controller
{

    /**
     * Function call by VerificationFom.vue with the route : /verification/enum/requiredSkill (get)
    * Get the fields of the required skill enum in the data base and give them to the vue for print them in the form 
    * @return \Illuminate\Http\Response
    */

    public function send_enum_verificationRequiredSkill (){
        $enums_verifRequiredSkills=DB::table('enum_verification_required_skills')->orderBy('value', 'asc')->get() ;  
        $enums=array() ;
        foreach($enums_verifRequiredSkills as $enum_verifRequiredSkill){
            $enum=([
                "value" => $enum_verifRequiredSkill->value,
                "id" => $enum_verifRequiredSkill->id,
                "id_enum" => "RequiredSkill",
            ]);
            array_push($enums, $enum) ;
        }
        return response()->json($enums) ; 
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
     * Function call by EnumManagement.vue with the route : /verification/enum/requiredSkill/verif/{id} (post)
    * Verify if we can update the verification verif acceptance authority enum in the data base
    * The id parameter correspond to the id of the enumrequiredSkill we want to update
     */
    public function verif_enum_requiredSkill(Request $request, $id){
        $enum_already_exist=EnumVerificationRequiredSkill::where('value', '=', $request->value)->where('id','<>', $id)->get();
        $enum=EnumVerificationRequiredSkill::findOrfail($id) ;
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_requiredSkill' => ["The value of the field for the verification required skill already exist in the data base"]
                ]
            ], 429);
        }
        return response()->json($id) ;
    }

    /**
     * Function call by EnumManagement.vue with the route : /verification/enum/requiredSkill/analyze/{id} (post)
    * Analyze the MME connected to the verification verif acceptance authority enum we want to update
    * The id parameter correspond to the id of the enumRequiredSkill we want to update
     */
    public function analyze_enum_requiredSkill(Request $request, $id){
        $verifications=Verification::where('enumRequiredSkill_id', '=', $id)->get() ;
        $mmes=array() ; 
        $validated_mme=array() ;
        $id_mmes=array() ;
        $id_mmes_validated=array() ;
        $cpt=0;
        $cpt_validated=0;
        foreach($verifications as $verification){
            $mme_temp=$verification->mme_temps ;
            $mme=([
                "mmeTemp_id" => $mme_temp->id,
                "name" => $mme_temp->mme->mme_name,
                "internalReference" => $mme_temp->mme->mme_internalReference,
            ]);
            if($mme_temp->mmeTemp_lifeSheetCreated==1){
                foreach($id_mmes_validated as $id_mmes){
                    if($id_mmes!=$mme_temp->id){
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
     * Function call by EnumManagement.vue with the route : /verification/enum/requiredSkill/update/{id} (post)
    * Update the verification requiredSkill enum in the data base
    * The id parameter correspond to the id of the enumrequiredSkill we want to update
     */

    public function update_enum_requiredSkill (Request $request, $id){
        $enum=EnumVerificationRequiredSkill::findOrFail($id) ; 
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
                    'state_remarks' => "MME Enum Update (update verif required skill): new version of life sheet created",
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
