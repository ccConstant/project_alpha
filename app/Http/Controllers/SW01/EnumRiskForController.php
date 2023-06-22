<?php

/*
* Filename : EnumRiskForController.php 
* Creation date : 24 May 2022
* Update date : 7 Jun 2023
* This file is used to link the view files and the database that concern the enumRiskFor table. 
* For example : send the fields of the enum, add a new field...
*/ 


namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\SW01\Risk;
use App\Models\SW01\EnumRiskFor;
use App\Models\SW01\EquipmentTemp;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Controller;
use App\Models\SW01\State;
use Carbon\Carbon;

class EnumRiskForController extends Controller
{
   
    /**
     * Function call by EquipmentRiskForm.vue with the route : /risk/enum/riskfor (get)
    * Get the fields of the risk target enum in the data base and give them to the vue for print them in the form 
    * @return \Illuminate\Http\Response
    */

    public function send_enum_riskFor (){
        $enums_riskFor=DB::table('enum_risk_fors')->orderBy('value', 'asc')->get() ;   
        $enums=array() ;
        foreach($enums_riskFor as $enum_riskFor){
            $enum=([
                "value" => $enum_riskFor->value,
                "id" => $enum_riskFor->id,
                "id_enum" => "RiskFor",
            ]);
            array_push($enums, $enum) ;
        }
        return response()->json($enums) ; 
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
     * Function call by EnumManagement.vue with the route : /risk/enum/riskfor/verif/{id} (post)
    * Verify if we can update the risk target enum in the data base
    * The id parameter correspond to the id of the enumRiskFor we want to update
     */
    public function verif_enum_riskFor(Request $request, $id){
        $enum_already_exist=EnumRiskFor::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_riskfor' => ["The value of the field for the risk target already exist in the data base"]
                ]
            ], 429);
        }
        return response()->json($id) ;
    }

    /**
     * Function call by EnumManagement.vue with the route : /risk/enum/riskfor/analyze/{id} (post)
    * Analyze the equipment connected to the risk target enum we want to update
    * The id parameter correspond to the id of the enumRiskFor we want to update
     */
    public function analyze_enum_riskFor(Request $request, $id){
        $risks=Risk::where('enumRiskFor_id', '=', $id)->get() ;
        $equipments=array() ; 
        $validated_eq=array() ;
        $id_eqs=array() ;
        $id_eqs_validated=array() ;
        $cpt=0;
        $cpt_validated=0;
       foreach($risks as $risk){
            $equipment_temp=$risk->equipment_temps ;
            $equipment=([
                "eqTemp_id" => $equipment_temp->id,
                "name" => $equipment_temp->equipment->eq_name,
                "internalReference" => $equipment_temp->equipment->eq_internalReference,
            ]);
            if($equipment_temp->eqTemp_lifeSheetCreated==1){
                foreach($id_eqs_validated as $id){
                    if($id!=$equipment_temp->id){
                        $cpt_validated++;
                    }
                }
                if ($cpt_validated==count($id_eqs_validated)){
                    array_push($validated_eq, $equipment) ;
                    array_push($id_eqs_validated, $equipment_temp->id) ;
                }
                $cpt_validated=0;
            }else{
                foreach($id_eqs as $id){
                    if($id!=$equipment_temp->id){
                        $cpt++;
                    }
                }
                if ($cpt==count($id_eqs)){
                    array_push($equipments, $equipment) ;
                    array_push($id_eqs, $equipment_temp->id) ;
                }
                $cpt=0;
            }
            
        }
        $final=([
            "id" => $id,
            "equipments" => $equipments,
            "validated_eq" => $validated_eq,
        ]);

        return response()->json($final) ;
    }

    
              

    /**
     * Function call by EnumManagement.vue with the route : /risk/enum/riskfor/update/{id} (post)
    * Update the risk target enum in the data base
    * The id parameter correspond to the id of the enumRiskFor we want to update
     */

    public function update_enum_riskFor(Request $request, $id){
        $enum_target=EnumRiskFor::findOrFail($id) ; 
        $enum_target->update([
            'value' => $request->value, 
        ]);
        if ($request->validated_eq!=NULL){
            foreach ($request->validated_eq as $eq){
                $equipment_temp=EquipmentTemp::findOrFail($eq['eqTemp_id']) ; 
                $eq=$equipment_temp->equipment ;
                
                $version=$eq->eq_nbrVersion+1;
                $eq->update([
                    'eq_nbrVersion' => $version
                ]);
                $equipment_temp->update([
                    'eqTemp_lifeSheetCreated' => 0, 
                    'qualityVerifier_id' => NULL,
                    'technicalVerifier_id' => NULL,
                    'eqTemp_version' => $version,
                    'eqTemp_signatureDate' => NULL,
                ]);

                $states=$equipment_temp->states;
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
                $newState=State::create([
                    'state_remarks' => "Equipment Enum Update (update risk for): new version of life sheet created",
                    'state_startDate' =>  Carbon::now('Europe/Paris'),
                    'state_validate' => "validated",
                    'state_name' => "Waiting_for_referencing"
                ]) ;

                $newState->equipment_temps()->attach($equipment_temp);

                //We created a new enregistrement of history for explain the reason of the enum updates
                $HistoryController= new HistoryController() ; 
                $HistoryController->add_history_for_eq($eq->id, $request) ; 
            }
        }



    }

    /**
     * Function call by EnumManagement.vue with the route : /risk/enum/riskFor/delete/{id} (post)
    * Add a new field for the risk for enum in the data base
    * The id parameter correspond to the id of the enumRiskFor we want to delete
     */

    public function delete_enum_riskFor ($id){
        $enum_riskFor=EnumRiskFor::findOrFail($id) ; 
        if ($enum_riskFor->value=="Production Environnement" || $enum_riskFor->value=="User Safety" || $enum_riskFor->value=="Product Quality"){
            return response()->json([
                'errors' => [
                    'enum_riskfor' => ["You can't delete this enum because it belongs of the principals risk target"]
                ]
            ], 429);
        }
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
