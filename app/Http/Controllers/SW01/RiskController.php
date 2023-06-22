<?php

/*
* Filename : RiskController.php 
* Creation date : 17 May 2022
* Update date : 23 May 2022
* This file is used to link the view files and the database that concern the risk table. 
* For example : add a risk for an equipment in the data base, update it, delete it...
*/ 


namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\SW01\EquipmentTemp ; 
use App\Models\SW01\Risk ; 
use App\Models\SW01\Equipment ; 
use App\Models\SW01\EnumRiskFor; 
use App\Models\SW01\PreventiveMaintenanceOperation ; 
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\SW01\State;


class RiskController extends Controller
{

    /*************************************************** TREATMENTS FOR AN EQUIPMENT ***************************************************\
     

    /**
     * Function call by EquipmentRiskForm.vue when the form is submitted for insert with the route : /equipment/add/risk/ (post)
     * Add a new enregistrement of risk in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new risk
     */
    public function add_risk_eqTemp(Request $request){

        $equipment=Equipment::findOrfail($request->eq_id) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
        //A risk is linked to its target. So we need to find the id of the type choosen by the user and write it in the attribute of the risk.
         //But if no one type is choosen by the user we define this id to NULL
         // And if the type choosen is find in the data base the NULL value will be replace by the id value
         $target_id=NULL ; 
        if ($request->risk_for!='' && $request->risk_for!=NULL){
             $target= EnumRiskFor::where('value', '=', $request->risk_for)->first() ;
             $target_id=$target->id ; 
         }

         //Creation of a new risk
         $risk=Risk::create([
            'risk_remarks' => $request->risk_remarks,
            'risk_wayOfControl' => $request->risk_wayOfControl,
            'risk_validate' => $request->risk_validate,
            'enumRiskFor_id'=> $target_id,
            'equipmentTemp_id' => $mostRecentlyEqTmp->id,
        ]) ;
            
         $risk_id=$risk->id;
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
            //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for add risk
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

                $states=$mostRecentlyEqTmp->states;
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
                    'state_remarks' => "Equipment Update (add risk eq) : new version of life sheet created",
                    'state_startDate' =>  Carbon::now('Europe/Paris'),
                    'state_validate' => "validated",
                    'state_name' => "Waiting_for_referencing"
                ]) ;

                $newState->equipment_temps()->attach($mostRecentlyEqTmp);
            }
         }
         return response()->json($risk_id) ; 
    }

     /**
     * Function call by EquipmentRiskForm.vue when the form is submitted for update with the route : /equipment/update/risk/{id} (post)
     * Update an enregistrement of risk in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the risk we want to update
     * */
    public function update_risk_eqTemp(Request $request, $id){

        //A risk is linked to its target. So we need to find the id of the type choosen by the user and write it in the attribute of the risk.
         //But if no one type is choosen by the user we define this id to NULL
         // And if the type choosen is find in the data base the NULL value will be replace by the id value
         $target_id=NULL ; 
        if ($request->risk_for!='' && $request->risk_for!=NULL){
             $target= EnumRiskFor::where('value', '=', $request->risk_for)->first() ;
             $target_id=$target->id ; 
        }

        $equipment=Equipment::findOrfail($request->eq_id) ; 
        //We search the most recently equipment temp of the equipment 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->latest()->first();
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
            //We checked if the most recently equipment temp is validate and if a life sheet has been already created.
            //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for update risk
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

               $states=$mostRecentlyEqTmp->states;
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
                'state_remarks' => "Equipment Update (update risk eq) : new version of life sheet created",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_validate' => "validated",
                'state_name' => "Waiting_for_referencing"
            ]) ;

            $newState->equipment_temps()->attach($mostRecentlyEqTmp);
                
                // In the other case, we can modify the informations without problems
            }
            $risk=Risk::findOrFail($id) ; 
            $risk->update([
                'risk_remarks' => $request->risk_remarks,
                'risk_wayOfControl' => $request->risk_wayOfControl,
                'risk_validate' => $request->risk_validate,
                'enumRiskFor_id'=> $target_id,
            ]); 
        }
    }


     /**
     * Function call by ReferenceARisk.vue with the route : /equipment/risk/send/{id} (get)
     * Get the risks of the equipment whose id is passed in parameter
     *  The id parameter corresponds to the id of the equipment from which we want the risks
     * @return \Illuminate\Http\Response
     */

    public function send_risks_eqTemp($id) {
        $mostRecentlyEqTmp=EquipmentTemp::where('equipment_id', '=', $id)->latest()->first();
        $risks = Risk::where('equipmentTemp_id', '=', $mostRecentlyEqTmp->id)->get();
        $container=array() ; 
        foreach ($risks as $risk) {
            $target = NULL ; 
            if ($risk->enumRiskFor_id!=NULL){
                $target = $risk->enumRiskFor->value ;
            }
            $obj=([
                'id' => $risk->id,
                'risk_remarks' => $risk->risk_remarks,
                'risk_wayOfControl' => $risk->risk_wayOfControl,
                'risk_validate' => $risk->risk_validate,
                'risk_for'=> $target,
            ]) ; 
            array_push($container,$obj);
        }
        return response()->json($container) ;
    }



    /*************************************************** TREATMENTS FOR AN PREVENTIVE MAINTENANCE OPERATION  ***************************************************\
    

    /**
     * Function call by EquipmentRiskForm.vue when the form is submitted for insert with the route : /equipment/add/prvMtnOp/risk/ (post)
     * Add a new enregistrement of risk in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new risk
     */
    public function add_risk_prvMtnOp(Request $request){

        //A risk is linked to its target. So we need to find the id of the type choosen by the user and write it in the attribute of the risk.
         //But if no one type is choosen by the user we define this id to NULL
         // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $target_id=NULL ; 
        if ($request->risk_for!='' && $request->risk_for!=NULL){
             $target= EnumRiskFor::where('value', '=', $request->risk_for)->first() ;
             $target_id=$target->id ; 
         }
         
         //Creation of a new risk
         $risk=Risk::create([
             'risk_remarks' => $request->risk_remarks,
             'risk_wayOfControl' => $request->risk_wayOfControl,
             'risk_validate' => $request->risk_validate,
             'enumRiskFor_id'=> $target_id,
             'preventiveMaintenanceOperation_id' => $request->prvMtnOp_id,
         ]) ; 

 
         $risk_id=$risk->id;
         $id_eq=intval($request->eq_id) ; 
         $equipment=Equipment::findOrfail($request->eq_id) ; 
         $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($request->prvMtnOp_id) ;
         $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
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
           //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for add risk
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

              $states=$mostRecentlyEqTmp->states;
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
                'state_remarks' => "Equipment Update (add risk prv mtn op) : new version of life sheet created",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_validate' => "validated",
                'state_name' => "Waiting_for_referencing"
            ]) ;

            $newState->equipment_temps()->attach($mostRecentlyEqTmp);
           }
            return response()->json($risk_id) ; 
         }
    }

     /**
     * Function call by EquipmentRiskForm.vue when the form is submitted for update with the route : /equipment/update/prvMtnOp/risk/{id} (post)
     * Update an enregistrement of risk linked to the preventive maintenance operation in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the risk we want to update
     * */
    public function update_risk_prvMtnOp(Request $request, $id){

        //A risk is linked to its target. So we need to find the id of the type choosen by the user and write it in the attribute of the risk.
         //But if no one type is choosen by the user we define this id to NULL
         // And if the type choosen is find in the data base the NULL value will be replace by the id value
         $target_id=NULL ; 
        if ($request->risk_for!='' && $request->risk_for!=NULL){
             $target= EnumRiskFor::where('value', '=', $request->risk_for)->first() ;
             $target_id=$target->id ; 
         }

         $id_eq=intval($request->eq_id) ; 
         $equipment=Equipment::findOrfail($request->eq_id) ; 
         $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($request->prvMtnOp_id) ;
         $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->orderBy('created_at', 'desc')->first();
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
            //We checked if the most recently equipment temp is validate and if a life sheet has been already created.
            //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for update risk
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

               $states=$mostRecentlyEqTmp->states;
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
                'state_remarks' => "Equipment Update (update risk prv mtn op) : new version of life sheet created",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_validate' => "validated",
                'state_name' => "Waiting_for_referencing"
            ]) ;

            $newState->equipment_temps()->attach($mostRecentlyEqTmp);
                
                // In the other case, we can modify the informations without problems
            }
            $risk=Risk::findOrFail($id) ; 
            $risk->update([
                'risk_remarks' => $request->risk_remarks,
                'risk_wayOfControl' => $request->risk_wayOfControl,
                'risk_validate' => $request->risk_validate,
                'enumRiskFor_id'=> $target_id,
            ]) ; 
        }
    }


    /**
     * Function call by ReferenceARisk.vue with the route : /prvMtnOp/risk/send/{$id} (get)
     * Get the risks of the preventive maintenance operation whose id is passed in parameter
     * The id parameter corresponds to the id of the preventive maintenance operation from which we want the risks
     * @return \Illuminate\Http\Response
     */

    public function send_risks_prvMtnOp($id) {
        $risks = Risk::where('preventiveMaintenanceOperation_id', '=', $id)->get();
        $container=array() ; 
        foreach ($risks as $risk) {
            $target = NULL ; 
            if ($risk->enumRiskFor_id!=NULL){
                $target = $risk->enumRiskFor->value ;
            }
            $obj=([
                'id' => $risk->id,
                'risk_remarks' => $risk->risk_remarks,
                'risk_wayOfControl' => $risk->risk_wayOfControl,
                'risk_validate' => $risk->risk_validate,
                'risk_for'=> $target,
                'prvMtnOp_id' => $id,
            ]) ; 
            array_push($container,$obj);
        }
        return response()->json($container) ;
    }

    /**
     * Function call by LifeSheetPDF.vue with the route : /prvMtnOp/risk/send/pdf/{$id} (get)
     * Get all the risks of all the preventive maintenance operation linked of one equipement whose id is passed in parameter
     * The id parameter corresponds to the id of the equipment from which we want the risks linked to the prv mtn op linked 
     * @return \Illuminate\Http\Response
     */

    public function send_risks_pdf($id) {
        $prvMtnOps=PreventiveMaintenanceOperation::where('equipmentTemp_id', '=', $id)->get() ; 
        $container=array(); 
        foreach($prvMtnOps as $prvMtnOp){
            $risks = Risk::where('preventiveMaintenanceOperation_id', '=', $prvMtnOp->id)->get();
            foreach ($risks as $risk) {
                $target = NULL ; 
                if ($risk->enumRiskFor_id!=NULL){
                    $target = $risk->enumRiskFor->value ;
                }
                $obj=([
                    'id' => $risk->id,
                    'risk_remarks' => $risk->risk_remarks,
                    'risk_wayOfControl' => $risk->risk_wayOfControl,
                    'risk_validate' => $risk->risk_validate,
                    'risk_for'=> $target,
                    'prvMtnOp_id' => $id,
                    'prvMtnOp_number' => $prvMtnOp->prvMtnOp_number,
                ]) ; 
                array_push($container,$obj);
            }
        
        }
        return response()->json($container) ;
    }
         


    /*************************************************** GENERALS TREATMENTS   ***************************************************\



    /**
     * Function call by EquipmentRiskForm.vue when the form is submitted for check data with the route : /risk/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_risk(Request $request){

        //-----CASE risk->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his risk, so he must enter all the attributes
        if ($request->risk_validate=='validated'){
            $this->validate(
                $request,
                [
                    'risk_remarks' => 'required|min:3|max:255',
                    'risk_wayOfControl' => 'required|min:3|max:255',
                ],
                [
                    'risk_remarks.required' => 'You must enter a remark for your risk ',
                    'risk_remarks.min' => 'You must enter at least three characters ',
                    'risk_remarks.max' => 'You must enter less than 255 characters ',
                    'risk_wayOfControl.required' => 'You must enter a way of control for your risk',
                    'risk_wayOfControl.min' => 'You must enter at least three characters ',
                    'risk_wayOfControl.max' => 'You must enter less than 255 characters ',
                ]
            );

            //verification about risk_for, if no one value is selected we need to alert the user
            if ($request->risk_for=='' || $request->risk_for==NULL ){
                return response()->json([
                    'errors' => [
                        'risk_for' => ["You must choose a target for your risk"]
                    ]
                ], 429);
            }

        }else{
             //-----CASE risk->validate=drafted or risk->validate=to be validate----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations 
            $this->validate(
                $request,
                [
                    'risk_remarks' => 'required|min:3|max:255',
                    'risk_wayOfControl' => 'max:255',
                ],
                [
                    'risk_remarks.required' => 'You must enter a remark for your risk ',
                    'risk_remarks.min' => 'You must enter at least three characters ',
                    'risk_remarks.max' => 'You must enter less than 255 characters ',
                    'risk_wayOfControl.max' => 'You must enter less than 255 characters ',

                ]
            );
        }
    }


      /**
     * Function call by EquipmentRiskForm.vue when we want to delete a risk with the route : /equipment/delete/risk{id} (post)
     * Delete a risk thanks to the id given in parameter
     * The id parameter correspond to the id of the risk we want to delete
     * */
    public function delete_risk(Request $request, $id){
        $equipment=Equipment::findOrfail($request->eq_id) ; 
        //We search the most recently equipment temp of the equipment 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $request->eq_id)->latest()->first();
        
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
        
        //We checked if the most recently equipment temp is validate and if a life sheet has been already created.
        //If the equipment temp is validated and a life sheet has been already created, we need to update the equipment temp and increase it's version (that's mean another life sheet version) for update dimension
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

            $states=$mostRecentlyEqTmp->states;
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
                'state_remarks' => "Equipment Update (delete risk) : new version of life sheet created",
                'state_startDate' =>  Carbon::now('Europe/Paris'),
                'state_validate' => "validated",
                'state_name' => "Waiting_for_referencing"
            ]) ;

            $newState->equipment_temps()->attach($mostRecentlyEqTmp);
        }
        
        $risk=Risk::findOrFail($id);
        $risk->delete() ; 
    }
}


