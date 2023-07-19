<?php

/*
* Filename : EnumStorageConditionController.php
* Creation date : 27 Apr 2023
* Update date : 28 Apr 2023
* This file is used to link the view files and the database that concern the EnumStorageCondition table.
* For example : send the fields of the enum, add a new field...
*/

namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\SW03\EnumStorageCondition;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\RawFamily;
use App\Models\SW03\CompSubFamily;
use App\Models\SW03\ConsSubFamily;
use App\Models\SW03\RawSubFamily;

class EnumStorageConditionController extends Controller
{
    /**
     * Function call by EnumManagement.vue with the route : /artFam/enum/storageCondition (get)
    * Get the fields of the art fam storage condition enum to the vue for print them in the form
     * @return \Illuminate\Http\Response
     */

     public function send_enum_storageCondition (){
        $enums_storageCondition=DB::table('enum_storage_conditions')->orderBy('value', 'asc')->get() ;
        $enums=array() ;
        foreach($enums_storageCondition as $enum_storageCondition){
            $enum=([
                "value" => $enum_storageCondition->value,
                "id" => $enum_storageCondition->id,
                "id_enum" => "StorageCondition",
            ]);
            array_push($enums, $enum) ;
        }
        return response()->json($enums) ;
    }

    /**
     * Function call by EnumManagement.vue with the route : /artFam/enum/storageCondition/add (post)
    * Add a new field for the art fam storage condition enum in the data base
     */

     public function add_enum_storageCondition (Request $request){
        $enum_type=EnumStorageCondition::create([
            'value' => $request->value,
        ]);
    }

    /**
     * Function call by EnumManagement.vue with the route : /artFam/enum/storageCondition/verif/{id} (post)
    * Verify if we can update the artFam storage condition enum in the data base
    * The id parameter correspond to the id of the enum storage condition we want to update
     */
    public function verif_enum_storageCondition(Request $request, $id){
        $enum_already_exist=EnumStorageCondition::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_storage_condition' => ["The value of the field for the new artFam storage condition already exist in the data base"]
                ]
            ], 429);
        }
        return response()->json($id) ;
    }

    /**
     * Function call by ArticleEnumStorageCondition with the route : /artFam/enum/storageCondition/link (post)
    * Link a storage condition to an article in the data base
     */

    public function link_enum_storageCondition (Request $request, $id){
        $enum=EnumStorageCondition::where('value', '=', $request->artFam_storageCondition)->first();
        if ($request->artFam_type=="COMP"){
            $compFam=CompFamily::findOrFail($id);
            $enum->compFamily()->attach($compFam, [
                'validate' => $request->validate,
            ]) ;
            return response()->json($enum->id) ;
        }else if ($request->artFam_type=="RAW"){
            $rawFam=RawFamily::findOrFail($id);
            $enum->rawFamily()->attach($rawFam, [
                'validate' => $request->validate,
            ]) ;
            return response()->json($enum->id) ;
        }else if ($request->artFam_type=="CONS"){
            $consFam=ConsFamily::findOrFail($id);
            $enum->consFamily()->attach($consFam, [
                'validate' => $request->validate,
            ]) ;
            return response()->json($enum->id) ;
        }
    }

     /**
     * Function call by ArticleEnumStorageCondition with the route : /artSubFam/enum/storageCondition/link (post)
    * Link a storage condition to an article sub family in the data base
     */

     public function link_enum_storageCondition_subFam (Request $request, $id){
        $enum=EnumStorageCondition::where('value', '=', $request->artSubFam_storageCondition)->first();
        if ($request->artSubFam_type=="COMP"){
            $compSubFam=CompSubFamily::findOrFail($id);
            $enum->compSubFamily()->attach($compSubFam, [
                'validate' => $request->validate,
            ]) ;
            return response()->json($enum->id) ;
        }else if ($request->artSubFam_type=="RAW"){
            $rawSubFam=RawSubFamily::findOrFail($id);
            $enum->rawSubFamily()->attach($rawSubFam, [
                'validate' => $request->validate,
            ]) ;
            return response()->json($enum->id) ;
        }else if ($request->artSubFam_type=="CONS"){
            $consSubFam=ConsSubFamily::findOrFail($id);
            $enum->consSubFamily()->attach($consSubFam, [
                'validate' => $request->validate,
            ]) ;
            return response()->json($enum->id) ;
        }
    }


    public function update_enum_storageCondition_linked (Request $request, $id){
        $enum_type=EnumStorageCondition::findOrFail($id) ;
        $enum_type->update([
            'value' => $request->value,
        ]);
        $consFams = $enum_type->consFamily;
        $compFams = $enum_type->compFamily;
        $rawFams = $enum_type->rawFamily;
        foreach ($consFams as $art){
            $nbrVersion = null;
            $nbrVersion = $art->consFam_nbrVersion;
            $art->update([
                'consFam_nbrVersion' => $nbrVersion+1,
                'consFam_qualityApproverId' => NULL,
                'consFam_technicalReviewerId' => NULL,
                'consFam_signatureDate' => NULL,
            ]);
        }
        foreach ($compFams as $art){
            $nbrVersion = null;
            $nbrVersion = $art->compFam_nbrVersion;
            $art->update([
                'compFam_nbrVersion' => $nbrVersion+1,
                'compFam_qualityApproverId' => NULL,
                'compFam_technicalReviewerId' => NULL,
                'compFam_signatureDate' => NULL,
            ]);
        }
        foreach ($rawFams as $art){
            $nbrVersion = null;
            $nbrVersion = $art->rawFam_nbrVersion;
            $art->update([
                'rawFam_nbrVersion' => $nbrVersion+1,
                'rawFam_qualityApproverId' => NULL,
                'rawFam_technicalReviewerId' => NULL,
                'rawFam_signatureDate' => NULL,
            ]);
        }
    }

    public function send_enum_storageCondition_usage($type, $id) {
        if ($type === "comp"){
            $compFam=CompFamily::findOrFail($id);
            return response()->json($compFam->storage_conditions);
        }else if ($type === "raw"){
            $rawFam=RawFamily::findOrFail($id);
            return response()->json($rawFam->storage_conditions);
        }else if ($type === "cons"){
            $consFam=ConsFamily::findOrFail($id);
            return response()->json($consFam->storage_conditions);
        }
    }

    public function send_enum_storageCondition_usage_subFam($type, $id) {
        if ($type === "COMP"){
            $compSubFam=CompSubFamily::findOrFail($id);
            return response()->json($compSubFam->storage_conditions);
        }else if ($type === "RAW"){
            $rawSubFam=RawSubFamily::findOrFail($id);
            return response()->json($rawSubFam->storage_conditions);
        }else if ($type === "CONS"){
            $consSubFam=ConsSubFamily::findOrFail($id);
            return response()->json($consSubFam->storage_conditions);
        }
    }

    public function unlink_enum_storageCondition (Request $request, $type, $id){
        $enum=EnumStorageCondition::where('value', '=', $request->artFam_id)->first();
        if ($type === "comp"){
            $compFam=CompFamily::findOrFail($id);
            $enum->compFamily()->detach($compFam);
        }else if ($type === "raw"){
            $rawFam=RawFamily::findOrFail($id);
            $enum->rawFamily()->detach($rawFam);
        }else if ($type === "cons"){
            $consFam=ConsFamily::findOrFail($id);
            $enum->consFamily()->detach($consFam);
        }
    }

    public function send_enum_storageCondition_linked($id){
        $articles = [];
        $signed_articles = [];
        $sto_conds = EnumStorageCondition::all()->where('id', '=', $id)->first();
        $consFams = $sto_conds->consFamily;
        $compFams = $sto_conds->compFamily;
        $rawFams = $sto_conds->rawFamily;
        foreach ($consFams as $consFam){
            if ($consFam->signatureDate === null) {
                array_push($articles, [
                    'id' => $consFam->id,
                    'type' => 'cons',
                    'artFam_ref' => $consFam->consFam_ref,
                    'artFam_design' => $consFam->consFam_design,
                ]);
            } else {
                array_push($signed_articles, [
                    'id' => $consFam->id,
                    'type' => 'cons',
                    'artFam_ref' => $consFam->consFam_ref,
                    'artFam_design' => $consFam->consFam_design,
                    'artFam_signatureDate' => $consFam->consFam_signatureDate,
                ]);
            }
        }
        foreach ($compFams as $compFam){
            if ($compFam->signatureDate === null) {
                array_push($articles, [
                    'id' => $compFam->id,
                    'type' => 'comp',
                    'artFam_ref' => $compFam->compFam_ref,
                    'artFam_design' => $compFam->compFam_design,
                ]);
            } else {
                array_push($signed_articles, [
                    'id' => $compFam->id,
                    'type' => 'comp',
                    'artFam_ref' => $compFam->compFam_ref,
                    'artFam_design' => $compFam->compFam_design,
                    'artFam_signatureDate' => $compFam->compFam_signatureDate,
                ]);
            }
        }
        foreach ($rawFams as $rawFam){
            if ($rawFam->signatureDate === null) {
                array_push($articles, [
                    'id' => $rawFam->id,
                    'type' => 'raw',
                    'artFam_ref' => $rawFam->rawFam_ref,
                    'artFam_design' => $rawFam->rawFam_design,
                ]);
            } else {
                array_push($signed_articles, [
                    'id' => $rawFam->id,
                    'type' => 'raw',
                    'artFam_ref' => $rawFam->rawFam_ref,
                    'artFam_design' => $rawFam->rawFam_design,
                    'artFam_signatureDate' => $rawFam->rawFam_signatureDate,
                ]);
            }
        }
        return response()->json([
            'articles' => $articles,
            'signed_articles' => $signed_articles,
            'consFam' => $consFams,
            'compFam' => $compFams,
            'rawFam' => $rawFams,
        ]);
    }

    public function delete_enum_stoConds ($id){
        $stoConds = [];
        $comp=CompFamily::all()->where('enumType_id', '=', $id);
        foreach ($comp as $c){
            array_push($stoConds, $c->storage_conditions);
        }
        $raw=RawFamily::all()->where('enumType_id', '=', $id);
        foreach ($raw as $r){
            array_push($stoConds, $r->storage_conditions);
        }
        $cons=ConsFamily::all()->where('enumType_id', '=', $id);
        foreach ($cons as $c){
            array_push($stoConds, $c->storage_conditions);
        }
        if (count($stoConds)!=0){
            return response()->json([
                'errors' => [
                    'enum_stoConds' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum = EnumStorageCondition::all()->find($id);
        $enum->delete() ;
    }

    public function update_storageCondition_linked(Request $request, $id) {
        $artFamily = null;
        if ($request->artFam_type === "comp"){
            $artFamily=CompFamily::findOrFail($request->artFam_id);
        }else if ($request->artFam_type === "raw"){
            $artFamily=RawFamily::findOrFail($request->artFam_id);
        }else if ($request->artFam_type === "cons"){
            $artFamily=ConsFamily::findOrFail($request->artFam_id);
        }
        $oldEnumStodConds = EnumStorageCondition::all()->where('value', '=', $request->oldValue)->first();
        $newEnumStodConds = EnumStorageCondition::all()->where('value', '=', $request->value)->first();

        $artFamily->storage_conditions()->detach($oldEnumStodConds);
        $artFamily->storage_conditions()->attach($newEnumStodConds);
    }
}
