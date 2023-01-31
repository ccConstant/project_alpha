<?php

/*
* Filename : HistoryController.php 
* Creation date : 18 Jan 2023
* Update date : 18 Jan 2023
* This file is used to link the view files and the database that concern the history table. 
* For example : add the history of an equipment or of a mme in the database, send this history to the view, etc.
*/ 



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mme;
use App\Models\Equipment;
use App\Models\EquipmentTemp;
use App\Models\History ; 
use App\Models\MmeTemp ; 
class HistoryController extends Controller
{
    public function verif_history(Request $request){

        // We need to do many verifications on the data entered by the user.
        // If the user make a mistake, we send to the vue an error to explain it and print it to the user.
        $this->validate(
            $request,
            [
                'history_reason' => 'required|min:3|max:255',
            ], 
        );
    }

    public function add_history_for_eq($id_eq, Request $request){
        $eq=Equipment::findOrfail($id_eq);
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id_eq)->orderBy('created_at', 'desc')->first();
        $history=History::create([
            'history_numVersion' => $eq->eq_nbrVersion,
            'history_reasonUpdate' => $request->history_reasonUpdate, 
            'equipmentTemp_id' => $mostRecentlyEqTmp->id,
            'mmeTemp_id' => 1,
        ]) ; 

    }
}
