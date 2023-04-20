<?php

/*
* Filename : DimensionController.php 
* Creation date : 11 May 2022
* Update date : 17 May 2022
* This file is used to link the view files and the database that concern the dimension table. 
* For example : add a dimension for an equipment, update a dimension, import a dimension, delete a dimension...
*/ 

namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\SW01\Dimension ; 
use App\Models\SW01\EquipmentTemp ; 
use App\Models\SW01\Equipment ; 
use App\Models\SW01\EnumDimensionName ; 
use App\Models\SW01\EnumDimensionType ; 
use App\Models\SW01\EnumDimensionUnit ; 
use Carbon\Carbon;
use App\Http\Controllers\Controller;


class DimensionController extends Controller
{

    /**
     * Function call by EquipmentDimForm.vue when the form is submitted for insert with the route :/equipment/add/dim (post)
     * Add a new enregistrement of dimension in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response
     */
    public function add_dimension(Request $request){
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();

        //A dimension is linked to its name. So we need to find the id of the name choosen by the user and write it in the attribute of the dimension.
        //But if no one name is choosen by the user we define this id to NULL
        // And if the name choosen is find in the data base the NULL value will be replace by the id value
        $name_id=NULL ;
        if ($request->dim_name!='' && $request->dim_name!=NULL){
            $name= EnumDimensionName::where('value', '=', $request->dim_name)->first() ;
            $name_id=$name->id ; 
        }

        //A dimension is linked to its type. So we need to find the id of the type choosen by the user and write it in the attribute of the dimension.
        //But if no one type is choosen by the user we define this id to NULL
        // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $type_id=NULL ; 
        if ($request->dim_type!='' && $request->dim_type!=NULL){
            $type= EnumDimensionType::where('value', '=', $request->dim_type)->first() ;
            $type_id=$type->id ; 
        }
        
        //A dimension is linked to its unit. So we need to find the id of the unit choosen by the user and write it in the attribute of the dimension.
        //But if no one unit is choosen by the user we define this id to NULL
        // And if the unit choosen is find in the data base the NULL value will be replace by the id value
        $unit_id=NULL ; 
        if ($request->dim_unit!='' && $request->dim_unit!=NULL){
            $unit= EnumDimensionUnit::where('value', '=', $request->dim_unit)->first() ;
            $unit_id=$unit->id ; 
        }
        
        //Creation of a new dimension
        $dimension=Dimension::create([
            'dim_value' => $request->dim_value,
            'dim_validate' => $request->dim_validate,
            'enumDimensionType_id' => $type_id,
            'enumDimensionName_id' => $name_id,
            'enumDimensionUnit_id' => $unit_id,
            'equipmentTemp_id' => $mostRecentlyEqTmp->id,
        ]) ; 

        $dim_id=$dimension->id;
        $id_eq=intval($request->eq_id) ; 
        if ($mostRecentlyEqTmp!=NULL){
            if ($mostRecentlyEqTmp->qualityVerifier_id!=null){
                $mostRecentlyEqTmp->update([
                    'qualityVerifier_id' => NULL,
                ]);
            }
            if ($mostRecentlyEqTmp->technicalVerifier_id!=null){
                $mostRecentlyEqTmp->update([
                    'technicalVerifier_id' => NULL,
                ]);
            }
             //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for add dimension
            if ((boolean)$mostRecentlyEqTmp->eqTemp_lifeSheetCreated==true && $mostRecentlyEqTmp->eqTemp_validate=="validated"){
                //We need to increase the number of equipment temp linked to the equipment
               $version_eq=$equipment->eq_nbrVersion+1 ; 
               //Update of equipment
               $equipment->update([
                   'eq_nbrVersion' =>$version_eq,
               ]);
               //We need to increase the version of the equipment temp (because we create a new equipment temp)
               $version =  $mostRecentlyEqTmp->eqTemp_version+1 ; 
               //update of equipment temp
               $mostRecentlyEqTmp->update([
                'eqTemp_version' => $version,
                'eqTemp_date' => Carbon::now('Europe/Paris'),
                'eqTemp_lifeSheetCreated' => false,
               ]);
            }
        }
    }

    /**
     * Function call by EquipmentDimForm.vue when the form is submitted for check data with the route : /dimension/verif''(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_dimension(Request $request){

        //-----CASE dim->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his dimension, so he must enter all the attributes
        if ($request->dim_validate=='validated'){
            $this->validate(
                $request,
                [
                    'dim_value' => 'required|min:1|max:50',
                ],
                [
                    'dim_value.required' => 'You must enter a value for your dimension ',
                    'dim_value.min' => 'You must enter at least one character ',
                    'dim_value.max' => 'You must enter a maximum of 50 characters',

                
                ]
            );

            //verification about dim_type, if no one value is selected we need to alert the user
            if ($request->dim_type=='' || $request->dim_type==NULL ){
                //verification about dim_name, if no one value is selected we need to alert the user
                if ($request->dim_name=='' || $request->dim_name==NULL){
                     //verification about dim_unit, if no one value is selected we need to alert the user
                    if ($request->dim_unit=='' || $request->dim_unit==NULL ){
                        return response()->json([
                            'errors' => [
                                'dim_name' => ["You must choose a name for your dimension"],
                                'dim_type' => ["You must choose a type for your dimension"],
                                'dim_unit' => ["You must choose a unit for your dimension"]
                            ]
                        ], 429);
                    }else{
                        return response()->json([
                            'errors' => [
                                'dim_name' => ["You must choose a name for your dimension"],
                                'dim_type' => ["You must choose a type for your dimension"]
                            ]
                        ], 429);
                    }
                        
                }else{
                    if ($request->dim_unit=='' || $request->dim_unit==NULL ){
                        return response()->json([
                            'errors' => [
                                'dim_type' => ["You must choose a type for your dimension"],
                                'dim_unit' => ["You must choose a unit for your dimension"]
                            ]
                        ], 429);
                    }else{
                        return response()->json([
                            'errors' => [
                                'dim_type' => ["You must choose a type for your dimension"]
                            ]
                        ], 429);

                    }
                }
            }else{
                if ($request->dim_name=='' || $request->dim_name==NULL){
                    //verification about dim_unit, if no one value is selected we need to alert the user
                   if ($request->dim_unit=='' || $request->dim_unit==NULL ){
                       return response()->json([
                           'errors' => [
                               'dim_name' => ["You must choose a name for your dimension"],
                               'dim_unit' => ["You must choose a unit for your dimension"]
                           ]
                       ], 429);
                   }else{
                       return response()->json([
                           'errors' => [
                               'dim_name' => ["You must choose a name for your dimension"]
                           ]
                       ], 429);
                   }
                       
               }else{
                   if ($request->dim_unit=='' || $request->dim_unit==NULL ){
                       return response()->json([
                           'errors' => [
                               'dim_unit' => ["You must choose a unit for your dimension"]
                           ]
                       ], 429);
                    }
                } 
            }

        }else{
             //-----CASE dim->validate=drafted or dim->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations 
            $this->validate(
                $request,
                [
                    'dim_value' => 'required|min:1|max:50',
                ],
                [
                    'dim_value.required' => 'You must enter a value for your dimension ',
                    'dim_value.min' => 'You must enter at least one character ',
                    'dim_value.max' => 'You must enter a maximum of 50 characters',

                
                ]
            );
        }
    }


    /**
     * Function call by ReferenceADim.vue with the route : /dimension/send/{$id} (get)
     * Get the dimensions of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the dimensions
     * @return \Illuminate\Http\Response
     */

    public function send_dimensions($id) {
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->latest()->first();
        $dimensions = Dimension::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get();
        $container=array() ; 
        foreach ($dimensions as $dimension) {
            $type = NULL ; 
            if ($dimension->enumDimensionType_id!=NULL){
                $type = $dimension->enumDimensionType->value ;
            }
            $name = NULL ; 
            if ($dimension->enumDimensionName_id!=NULL){
                $name = $dimension->enumDimensionName->value ;
            }

            $unit = NULL ; 
            if ($dimension->enumDimensionUnit_id!=NULL){
                $unit = $dimension->enumDimensionUnit->value ;
            }
            $obj=([
                "id" => $dimension->id,
                "dim_value" => $dimension->dim_value,
                "dim_name" => $name,
                "dim_type" => $type,
                "dim_unit"=> $unit,
                "dim_validate" => $dimension->dim_validate,
            ]);
            array_push($container,$obj);
        }
        return response()->json($container) ;
    }

    /**
     * Function call by LifeSheetPDF.vue with the route : /dimension/send/ByType/{$id} (get)
     * Get the dimensions by type of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the dimensions
     * @return \Illuminate\Http\Response
     */

    public function send_dimensions_by_type($id) {
        $enums_dimType=EnumDimensionType::all() ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->latest()->first();
        $dimensions = Dimension::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get();
        $containerGlobal=array() ; 
        foreach ($enums_dimType as $enum_dimType){
            $container=array() ; 
            foreach($dimensions as $dimension){
                if ($enum_dimType->id==$dimension->enumDimensionType_id){
                    $type = NULL ; 
                    if ($dimension->enumDimensionType_id!=NULL){
                        $type = $dimension->enumDimensionType->value ;
                    }
                    $name = NULL ; 
                    if ($dimension->enumDimensionName_id!=NULL){
                        $name = $dimension->enumDimensionName->value ;
                    }
        
                    $unit = NULL ; 
                    if ($dimension->enumDimensionUnit_id!=NULL){
                        $unit = $dimension->enumDimensionUnit->value ;
                    }
                    $obj=([
                        "id" => $dimension->id,
                        "dim_value" => $dimension->dim_value,
                        "dim_name" => $name,
                        "dim_type" => $type,
                        "dim_unit"=> $unit,
                        "dim_validate" => $dimension->dim_validate,
                    ]);
                    array_push($container,$obj);
                }
            }
            $obj2=([
                "type" => $enum_dimType->value,
                "dimensions" => $container,
            ]);
            array_push($containerGlobal,$obj2);
        }
       
        return response()->json($containerGlobal) ;
    }

    /**
     * Function call by EquipmentDimForm.vue when the form is submitted for update with the route :/equipment/update/dim/{id} (post)
     * Update an enregistrement of dimension in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the dimension we want to update
     * */
    public function update_dimension(Request $request, $id){
        //A dimension is linked to its name. So we need to find the id of the name choosen by the user and write it in the attribute of the dimension.
        //But if no one name is choosen by the user we define this id to NULL
        // And if the name choosen is find in the data base the NULL value will be replace by the id value
        $name_id=NULL ;
        if ($request->dim_name!='' && $request->dim_name!=NULL){
            $name= EnumDimensionName::where('value', '=', $request->dim_name)->first() ;
            $name_id=$name->id ; 
        }

        //A dimension is linked to its type. So we need to find the id of the type choosen by the user and write it in the attribute of the dimension.
        //But if no one type is choosen by the user we define this id to NULL
        // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $type_id=NULL ; 
        if ($request->dim_type!='' && $request->dim_type!=NULL){
            $type= EnumDimensionType::where('value', '=', $request->dim_type)->first() ;
            $type_id=$type->id ; 
        }
        
        //A dimension is linked to its unit. So we need to find the id of the unit choosen by the user and write it in the attribute of the dimension.
        //But if no one unit is choosen by the user we define this id to NULL
        // And if the unit choosen is find in the data base the NULL value will be replace by the id value
        $unit_id=NULL ; 
        if ($request->dim_unit!='' && $request->dim_unit!=NULL){
            $unit= EnumDimensionUnit::where('value', '=', $request->dim_unit)->first() ;
            $unit_id=$unit->id ; 
        }

        $equipment=Equipment::findOrfail($request->eq_id) ; 
        //We search the most recently equipment temp of the equipment 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->latest()->first();
        if ($mostRecentlyEqTmp!=NULL){
            //We checked if the most recently equipment temp is validate and if a life sheet has been already created.
            //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for update dimension
            if ($mostRecentlyEqTmp->qualityVerifier_id!=null){
                $mostRecentlyEqTmp->update([
                    'qualityVerifier_id' => NULL,
                ]);
            }
            if ($mostRecentlyEqTmp->technicalVerifier_id!=null){
                $mostRecentlyEqTmp->update([
                    'technicalVerifier_id' => NULL,
                ]);
            }
            
            if ($mostRecentlyEqTmp->eqTemp_validate=="validated" && (boolean)$mostRecentlyEqTmp->eqTemp_lifeSheetCreated==true){
            
                //We need to increase the number of equipment temp linked to the equipment
                $version_eq=$equipment->eq_nbrVersion+1 ; 
                //Update of equipment
                $equipment->update([
                    'eq_nbrVersion' =>$version_eq,
                ]);

                //We need to increase the version of the equipment temp (because we create a new equipment temp)
               $version =  $mostRecentlyEqTmp->eqTemp_version+1 ; 
               //update of equipment temp
               $mostRecentlyEqTmp->update([
                'eqTemp_version' => $version,
                'eqTemp_date' => Carbon::now('Europe/Paris'),
                'eqTemp_lifeSheetCreated' => false,
               ]);
                
                // In the other case, we can modify the informations without problems
            }
            $dimension=Dimension::findOrFail($id) ; 
            $dimension->update([
                'dim_value' => $request->dim_value,
                'dim_validate' => $request->dim_validate,
                'enumDimensionType_id' => $type_id,
                'enumDimensionName_id' => $name_id,
                'enumDimensionUnit_id' => $unit_id,
            ]);
        }
    }
    

    /**
     * Function call by EquipmentDimForm.vue when we want to delete a dimension with the route : /equipment/delete/usg{id}(post)
     * Delete a dimension thanks to the id given in parameter
     * The id parameter correspond to the id of the dimension we want to delete
     * */
    public function delete_dimension(Request $request, $id){
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        //We search the most recently equipment temp of the equipment 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->latest()->first();
        //We checked if the most recently equipment temp is validate and if a life sheet has been already created.
        //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for update dimension
        if ($mostRecentlyEqTmp->qualityVerifier_id!=null){
            $mostRecentlyEqTmp->update([
                'qualityVerifier_id' => NULL,
            ]);
        }
        if ($mostRecentlyEqTmp->technicalVerifier_id!=null){
            $mostRecentlyEqTmp->update([
                'technicalVerifier_id' => NULL,
            ]);
        }
       
        if ($mostRecentlyEqTmp->eqTemp_validate=="validated" && (boolean)$mostRecentlyEqTmp->eqTemp_lifeSheetCreated==true){
            //We need to increase the number of equipment temp linked to the equipment
            $version_eq=$equipment->eq_nbrVersion+1 ; 
            //Update of equipment
            $equipment->update([
                'eq_nbrVersion' =>$version_eq,
            ]);

            //We need to increase the version of the equipment temp (because we create a new equipment temp)
            $version =  $mostRecentlyEqTmp->eqTemp_version+1 ; 
            //update of equipment temp
            $mostRecentlyEqTmp->update([
            'eqTemp_version' => $version,
            'eqTemp_date' => Carbon::now('Europe/Paris'),
            'eqTemp_lifeSheetCreated' => false,
            ]);
        }
        
        $dimension=Dimension::findOrFail($id);
        $dimension->delete() ; 
    }
}




