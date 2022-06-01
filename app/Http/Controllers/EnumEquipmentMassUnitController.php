<?php

/*
* Filename : EnumEquipmentMassUnitController.php 
* Creation date : 24 May 2022
* Update date : 24 May 2022
* This file is used to link the view files and the database that concern the enumEquipmentMassUnit table. 
* For example : send the fields of the enum, add a new field...
*/ 



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\Equipment;
use App\Models\EquipmentTemp;
use App\Models\EnumEquipmentMassUnit;


class EnumEquipmentMassUnitController extends Controller
{
    /**
     * Function call by EquipmentIDForm.vue with the route : /equipment/enum/massUnit (post)
    * Get the fields of the equipment mass unit enum in the data base and give them to the vue for print them in the form 
    * @return \Illuminate\Http\Response
    */
    public function send_enum_massUnit (){
        $enums_massUnit=DB::select(DB::raw('SELECT DISTINCT value, id FROM enum_equipment_mass_units'));
        return response()->json($enums_massUnit) ; 
    }

    /**
     * Function call by EnumManagement.vue with the route : /equipment/enum/massUnit/add (post)
    * Add a new field for the equipment massUnit enum in the data base
     */

    public function add_enum_massUnit (Request $request){
        $enum_already_exist=EnumEquipmentMassUnit::where('value', '=', $request->value)->get();
        if (count($enum_already_exist)!=0){
            return response()->json([
                'errors' => [
                    'enum_eq_massUnit' => ["The value of the field for the new equipment mass unit already exist in the data base"]
                ]
            ], 429);
        }
        
        $enum_massUnit=EnumEquipmentMassUnit::create([
            'value' => $request->value, 
        ]);
    }

      /**
     * Function call by EnumManagement.vue with the route : /equipment/enum/massUnit/update/{id} (post)
    * Add a new field for the equipment massUnit enum in the data base
    * The id parameter correspond to the id of the enumEquipmentMassUnit we want to update
     */

    public function update_enum_massUnit (Request $request, $id){
        $enum_already_exist=EnumEquipmentMassUnit::where('value', '=', $request->value)->where('id','<>', $id)->get();
        if (count($enum_already_exist)!=0 ){
            return response()->json([
                'errors' => [
                    'enum_eq_massUnit' => ["The value of the field for the equipment mass unit already exist in the data base"]
                ]
            ], 429);
        }
        
        
        $enum_massUnit=EnumEquipmentMassUnit::findOrFail($id) ; 
        $enum_massUnit->update([
            'value' => $request->value, 
        ]); 
    }

    /**
     * Function call by EnumManagement.vue with the route : /equipment/enum/MassUnit/delete/{id} (post)
    * Add a new field for the equipment massUnit enum in the data base
    * The id parameter correspond to the id of the enumEquipmentMassUnit we want to delete
     */

    public function delete_enum_massUnit ($id){
        $enum_massUnit=EnumEquipmentMassUnit::findOrFail($id) ; 
        $eqLinked=EquipmentTemp::where('enumMassUnit_id', '=', $id)->get() ; 
        if (count($eqLinked)!=0){
            return response()->json([
                'errors' => [
                    'enum_eq_massUnit' => ["This value is already used in the data base so you can't delete it"]
                ]
            ], 429);
        }
        $enum_massUnit->delete() ; 
    }

}
