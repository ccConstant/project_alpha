<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Mme ;  
use App\Models\MmeTemp ;  
use App\Models\MmeUsage ;  
use Carbon\Carbon;

class PrecautionController extends Controller
{
    

     /**
     * Function call by MmePrecautionForm.vue when the form is submitted for check data with the route : /precaution/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_precaution(Request $request){        
        $state=State::findOrFail($request->state_id) ; 
        $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($request->prvMtnOp_id) ; 

        if ($request->prvMtnOpRlz_validate=="validated"){
            if ($request->prvMtnOpRlz_endDate=='' || $request->prvMtnOpRlz_endDate===NULL){
                return response()->json([
                    'errors' => [
                        'prvMtnOpRlz_endDate' => ["You have to entered the endDate of your preventive maintenance operation realized for validate it"]
                    ]
                ], 429);
            }

            if ($request->reason=="update"){
                $prvMtnOpRlz=PreventiveMaintenanceOperationRealized::findOrFail($request->prvMtnOpRlz_id) ; 
                if ($prvMtnOpRlz->realizedBy_id==NULL){
                    return response()->json([
                        'errors' => [
                            'prvMtnOpRlz_validate' => ["You have to entered the realizator of this preventive maintenance operation realized for validate it"]
                        ]
                    ], 429);
                }
    
                if ($prvMtnOpRlz->approvedBy_id==NULL){
                    return response()->json([
                        'errors' => [
                            'prvMtnOpRlz_validate' => ["You have to entered the person who approved this preventive maintenance operation realized for validate it"]
                        ]
                    ], 429);
                }

            }else{
                return response()->json([
                    'errors' => [
                        'prvMtnOpRlz_validate' => ["You have to entered the realizator of this preventive maintenance operation realized for validate it"]
                    ]
                ], 429);
            
                return response()->json([
                    'errors' => [
                        'prvMtnOpRlz_validate' => ["You have to entered the person who approved this preventive maintenance operation realized for validate it"]
                    ]
                ], 429);
            }
        }
        
        $this->validate(
            $request,
            [
                'prvMtnOpRlz_reportNumber' => 'required|min:3|max:255',
            ],
            [
                'prvMtnOpRlz_reportNumber.required' => 'You must enter a report number for the preventive maintenance operation realized ',
                'prvMtnOpRlz_reportNumber.min' => 'You must enter at least three characters ',
                'prvMtnOpRlz_reportNumber.max' => 'You must enter a maximum of 255 characters',

            
            ]
        );
        if ($request->prvMtnOpRlz_startDate=='' || $request->prvMtnOpRlz_startDate===NULL){
            return response()->json([
                'errors' => [
                    'prvMtnOpRlz_startDate' => ["You have to entered the startDate of your preventive maintenance operation realized"]
                ]
            ], 429);
        }
    
        if ($request->reason=="update"){
            $prvMtnOpRlz=PreventiveMaintenanceOperationRealized::FindOrFail($request->prvMtnOpRlz_id ) ;
            if ($prvMtnOpRlz->prvMtnOpRlz_validate=="validated"){
                return response()->json([
                    'errors' => [
                        'prvMtnOpRlz_validate' => ["You can't update a preventive maintenance operation realized already validated"]
                    ]
                ], 429);
            }
        }

        $oneMonthAgo=Carbon::now()->subMonth(1) ; 
        if ($request->prvMtnOpRlz_startDate!=NULL && $request->prvMtnOpRlz_startDate<$oneMonthAgo){
            return response()->json([
                'errors' => [
                    'prvMtnOpRlz_startDate' => ["You can't enter a date that is older than one month"]
                ]
            ], 429);
        }

        if ($request->prvMtnOpRlz_endDate!=NULL && $request->prvMtnOpRlz_endDate<$oneMonthAgo){
            return response()->json([
                'errors' => [
                    'prvMtnOpRlz_endDate' => ["You can't enter a date that is older than one month"]
                ]
            ], 429);
        }

        if ($state->state_startDate!=NULL && $request->prvMtnOpRlz_startDate!=NULL){
            if ($request->prvMtnOpRlz_startDate<$state->state_startDate){
                return response()->json([
                    'errors' => [
                        'prvMtnOpRlz_startDate' => ["You can't entered this startDate because it must be after the startDate of the state"]
                    ]
                ], 429);
            }
        }
        if ($state->state_startDate!=NULL && $request->prvMtnOpRlz_endDate!=NULL){
            if ($request->prvMtnOpRlz_endDate<$state->state_startDate){
                return response()->json([
                    'errors' => [
                        'prvMtnOpRlz_endDate' => ["You can't entered this endDate because it must be after the startDate of the state"]
                    ]
                ], 429);
            }
        }

        if ($request->prvMtnOpRlz_startDate!=NULL && $request->prvMtnOpRlz_endDate!=NULL){
            if ($request->prvMtnOpRlz_endDate < $request->prvMtnOpRlz_startDate){
                return response()->json([
                    'errors' => [
                        'prvMtnOpRlz_endDate' => ["You must entered a startDate that is before endDate"]
                    ]
                ], 429);

            }
        }
    }
}
