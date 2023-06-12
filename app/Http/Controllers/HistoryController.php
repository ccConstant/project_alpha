<?php

/*
* Filename : HistoryController.php
* Creation date : 18 Jan 2023
* Update date : 14 Feb 2023
* This file is used to link the view files and the database that concern the history table.
* For example : add the history of an equipment or of a mme in the database, send this history to the view, etc.
*/



namespace App\Http\Controllers;

use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\RawFamily;
use Carbon\CarbonTimeZone;
use DateTimeZone;
use Illuminate\Http\Request;
use App\Models\SW01\Mme;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\History ;
use App\Models\SW01\MmeTemp ;
use Carbon\Carbon;
class HistoryController extends Controller
{

     /**
     * Function call by EquipmentIDForm.vue when the form is submitted for insert with the route : /history/add/equipment/{id} (post)
     * Add a new enregistrement of history in the data base with the reason entered in the form
     */
    public function add_history_for_eq($id_eq, Request $request){
        return response()->json([
            'request' => $request->all(),
            'id_eq' => $id_eq,
        ]);
        $eq=Equipment::findOrFail($id_eq);
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id_eq)->orderBy('created_at', 'desc')->first();
        $version=$eq->eq_nbrVersion-1 ;
        $history=History::create([
            'history_numVersion' => $version,
            'history_reasonUpdate' => $request->history_reasonUpdate,
            'equipmentTemp_id' => $mostRecentlyEqTmp->id,
            'mmeTemp_id' => NULL,
        ]) ;
    }

    /**
     * Function call by MMEIDForm.vue when the form is submitted for insert with the route : /history/add/mme/{id} (post)
     * Add a new enregistrement of history in the data base with the reason entered in the form
     */
    public function add_history_for_mme($id_mme, Request $request){
        $mme=Mme::findOrfail($id_mme);
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id_mme)->orderBy('created_at', 'desc')->first();
        $history=History::create([
            'history_numVersion' => $mme->mme_nbrVersion-1,
            'history_reasonUpdate' => $request->history_reasonUpdate,
            'equipmentTemp_id' => NULL,
            'mmeTemp_id' => $mostRecentlyMmeTmp->id,
        ]) ;

    }

    /**
     * Function call by EquipmentVersionHistory.vue with the route : /history/send/equipment/{id}
     * Get equipment history corresponding to the equipment id in the data base for print it in the vue
     * The id parameter corresponds to the id of the equipment from which we want the history
     * @return \Illuminate\Http\Response
     */

     public function send_history_for_eq ($id){
        $equipment= Equipment::findOrFail($id) ;
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyEqTmp!=NULL){
            $histories=History::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get();
        }
        $containerHistory=array() ;
        foreach($histories as $history){
            $date = Carbon::createFromDate($history->created_at->year, $history->created_at->month, $history->created_at->day);
            $historyObj=([
                "id" => $history->id,
                "history_numVersion" => $history->history_numVersion,
                "history_reasonUpdate" => $history->history_reasonUpdate,
                "history_date" => $date->format('d M o')
            ]);
            array_push($containerHistory,$historyObj);
        }
        return response()->json($containerHistory);
    }

    /**
     * Function call by MMEVersionHistory.vue with the route : /history/send/mme/{id}
     * Get mme history corresponding to the mme id in the data base for print it in the vue
     * The id parameter corresponds to the id of the mme from which we want the history
     * @return \Illuminate\Http\Response
     */

     public function send_history_for_mme ($id){
        $mme= Mme::findOrFail($id) ;
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $id)->orderBy('created_at', 'desc')->first();
        if ($mostRecentlyMmeTmp!=NULL){
            $histories=History::where('mmeTemp_id', '=', $mostRecentlyMmeTmp->id)->get();
        }
        $containerHistory=array() ;
        foreach($histories as $history){
            $date = Carbon::createFromDate($history->created_at->year, $history->created_at->month, $history->created_at->day);
            $historyObj=([
                "id" => $history->id,
                "history_numVersion" => $history->history_numVersion,
                "history_reasonUpdate" => $history->history_reasonUpdate,
                "history_date" => $date->format('d M o')
            ]);
            array_push($containerHistory,$historyObj);
        }
        return response()->json($containerHistory);
    }

    public function add_history_for_article(Request $request, $type, $id) {
         if ($type === 'cons') {
             $article = ConsFamily::findOrfail($id);
             $hist = History::create([
                'history_numVersion' => $article->consFam_nbrVersion-1,
                 'history_reasonUpdate' => $request->history_reasonUpdate,
                 'consFam_id' => $id
             ]);
         } else if ($type === 'comp') {
             $article = CompFamily::findOrfail($id);
             $hist = History::create([
                 'history_numVersion' => $article->compFam_nbrVersion-1,
                 'history_reasonUpdate' => $request->history_reasonUpdate,
                 'compFam_id' => $id
             ]);
         } else if ($type === 'raw') {
             $article = RawFamily::findOrfail($id);
             $hist = History::create([
                 'history_numVersion' => $article->rawFam_nbrVersion-1,
                 'history_reasonUpdate' => $request->history_reasonUpdate,
                 'rawFam_id' => $id
             ]);
         }
    }

    public function send_history_for_article($type, $id) {
         if ($type === 'cons') {
             $hist = History::all()->where('consFam_id', '=', $id);
             return response()->json($hist);
         } else if ($type === 'comp') {
             $hist = History::all()->where('compFam_id', '=', $id);
             return response()->json($hist);
         } else if ($type === 'raw') {
             $hist = History::all()->where('rawFam_id', '=', $id);
             return response()->json($hist);
         }
    }
}
