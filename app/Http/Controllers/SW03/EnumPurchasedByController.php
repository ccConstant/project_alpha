<?php

/*
* Filename : EnumPurchasedByController.php 
* Creation date : 26 Apr 2023
* Update date : 26 Apr 2023
* This file is used to link the view files and the database that concern the EnumPurchasedBy table. 
* For example : send the fields of the enum, add a new field...
*/ 

namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\SW03\EnumPurchasedBy;

class EnumPurchasedByController extends Controller
{
    /**
     * Function call by EnumManagement.vue with the route : /artFam/enum/purchasedBy (get)
    * Get the fields of the art fam purchased by enum to the vue for print them in the form 
     * @return \Illuminate\Http\Response
     */

     public function send_enum_purchasedBy (){
        $enums_purchasedBy=DB::table('enum_purchased_bies')->orderBy('value', 'asc')->get() ;  
        $enums=array() ;
        foreach($enums_purchasedBy as $enum_purchasedBy){
            $enum=([
                "value" => $enum_purchasedBy->value,
                "id" => $enum_purchasedBy->id,
                "id_enum" => "PurchasedBy",
            ]);
            array_push($enums, $enum) ;
        }
        return response()->json($enums) ; 
    }

    /**
     * Function call by EnumManagement.vue with the route : /artFam/enum/purchasedBy/add (post)
    * Add a new field for the art fam purchased by enum in the data base
     */

     public function add_enum_purchasedBy (Request $request){
        $enum_type=EnumPurchasedBy::create([
            'value' => $request->value, 
        ]);
    }

    /**
     * Function call by EnumManagement.vue with the route : /artFam/enum/purchasedBy/verif/{id} (post)
    * Verify if we can update the artFam purchasedBy enum in the data base
    * The id parameter correspond to the id of the enumPurchasedBy we want to update
     */
    public function verif_enum_purchasedBy(Request $request, $id){
        $enum_already_exist=EnumPurchasedBy::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_purchased_by' => ["The value of the field for the new artFam purchasedBy already exist in the data base"]
                ]
            ], 429);
        }
        return response()->json($id) ;
    }

    /**
     * Function call by EnumManagement.vue with the route : /artFam/enum/purchasedBy/analyze/{id} (post)
    * Analyze the article we want to update
    * The id parameter correspond to the id of the enumPurchasedBy we want to update
     */
    /*public function analyze_enum_purchasedBy(Request $request, $id){
        $compFams=CompFamily::where('enumPurchasedBy_id', '=', $id)->get() ;
        $consFams=ConsFamily::where('enumPurchasedBy_id', '=', $id)->get() ;
        $rawFams=RawFamily::where('enumPurchasedBy_id', '=', $id)->get() ;
        $comps=array() ; 
        $validated_comp=array() ;
        $conss=array() ; 
        $validated_cons=array() ;
        $raws=array() ; 
        $validated_raw=array() ;
       foreach($compFams as $compFam){
            $comp=([
                "id" => $compFam->id,
                "reference" => $compFam->compFam_ref,
                "designation" => $compFam->compFam_designation,
            ]);
            if($compFam->compFam_signatureDate!=NULL){
                array_push($validated_comp, $comp) ;
            }else{
                array_push($comps, $comp) ;
            }
            
        }
        foreach($consFams as $consFam){
            $cons=([
                "id" => $compFam->id,
                "reference" => $consFam->consFam_ref,
                "designation" => $consFam->consFam_designation,
            ]);
            if($consFam->consFam_signatureDate!=NULL){
                array_push($validated_cons, $cons) ;
            }else{
                array_push($conss, $cons) ;
            }
            
        }
        foreach($rawFams as $rawFam){
            $raw=([
                "id" => $compFam->id,
                "reference" => $consFam->consFam_ref,
                "designation" => $consFam->consFam_designation,
            ]);
            if($rawFam->rawFam_signatureDate!=NULL){
                array_push($validated_raw, $raw) ;
            }else{
                array_push($raws, $raw) ;
            }
            
        }
        $final=([
            "id" => $id,
            "comp" => $comps,
            "validated_comp" => $validated_comp,
            "cons" => $conss,
            "validated_cons" => $validated_cons,
            "raw" => $raws,
            "validated_raw" => $validated_raw,
        ]);

        return response()->json($final) ;
    }*/


    /**
     * Function call by EnumManagement.vue with the route : /artFam/enum/purchasedBy/update/{id} (post)
    * Update the purchasedBy enum in the data base
    * The id parameter correspond to the id of the enumPurchasedBy we want to update
     */

    /*public function update_enum_purchasedBy (Request $request, $id){
        $enum_type=EnumPurchasedBy::findOrFail($id) ; 
        $enum_type->update([
            'value' => $request->value, 
        ]);
        foreach ($request->validated_comp as $comp){
            $compFam=CompFamily::findOrFail($comp['id']) ;
            
            $version=$compFam->compFam_nbrVersion+1;
            $compFam->update([
                'compFam_nbrVersion' => $version,
                'compFam_qualityApproverId' => NULL,
                'compFam_technicalReviewerId' => NULL,
                'compFam_signatureDate' => NULL,
            ]);

            //We created a new enregistrement of history for explain the reason of the enum updates
            $HistoryController= new HistoryController() ; 
            $HistoryController->add_history_for_art($eq->id, $request) ; 
        }

    }*/


    /**
     * Function call by EnumManagement.vue with the route : /equipment/enum/type/delete/{id} (post)
    * Add a new field for the equipment type enum in the data base
    * The id parameter correspond to the id of the enumPurchasedBy we want to delete
     */

    /*public function delete_enum_type ($id){
        $enum_type=EnumEquipmentType::findOrFail($id) ; 
        $eqLinked=EquipmentTemp::where('enumType_id', '=', $id)->get() ; 
        if (count($eqLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_eq_type' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_type->delete() ; 
    }*/
}
