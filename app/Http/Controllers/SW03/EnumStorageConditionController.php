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
            $enum->compFamily()->attach($compFam) ;
            return response()->json($enum->id) ;
        }else{
            if ($request->artFam_type=="RAW"){
                $rawFam=RawFamily::findOrFail($id);
                $enum->rawFamily()->attach($rawFam) ;
                return response()->json($enum->id) ;
            }else{
                if ($request->artFam_type=="CONS"){
                    $consFam=ConsFamily::findOrFail($id);
                    $enum->consFamily()->attach($consFam) ;
                    return response()->json($enum->id) ;
                }
            }
        }
    }

    public function send_enum_storageCondition_linked($type, $id) {
        $article = null;
        if ($type === 'cons') {
            $article = ConsFamily::findOrFail($id);
        } else if ($type === 'comp') {
            $article = CompFamily::findOrFail($id);
        } else if ($type === 'raw') {
            $article = RawFamily::findOrFail($id);
        }
        $sto_cond = $article->storage_conditions;
        $detailed = EnumStorageCondition::all()->where('id', '=', $sto_cond->storageCondition_id)->first();
        $array = [];
        foreach ($sto_cond as $sto) {
            array_push($array, [
                'id' => $sto->id,
                'storageCondition_id' => $sto->storageCondition_id,
                'value' => $detailed->value,
            ]);
        }
    }
}
