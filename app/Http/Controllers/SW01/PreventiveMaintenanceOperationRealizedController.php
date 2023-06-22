<?php

namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ;
use App\Models\SW01\PreventiveMaintenanceOperationRealized ;
use App\Models\SW01\EquipmentTemp ;
use App\Models\SW01\Equipment ;
use App\Models\SW01\State ;
use App\Models\User ;
use App\Models\SW01\PreventiveMaintenanceOperation ;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Controller;


class PreventiveMaintenanceOperationRealizedController extends Controller
{


    /**
     * Function call by EquipmentPrvMtnOpRlzForm.vue when the form is submitted for insert with the route :/equipment/add/state/prvMtnOpRlz (post)
     * Add a new enregistrement of preventive maintenance operation realized in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : the id of the new prvMtnOpRlz
     */
    public function add_prvMtnOpRlz(Request $request){
        $state=State::findOrFail($request->state_id) ;
        $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($request->prvMtnOp_id) ;

        //Creation of a new preventive maintenance operation realized
        $prvMtnOpRlz=PreventiveMaintenanceOperationRealized::create([
            'prvMtnOpRlz_reportNumber' => $request->prvMtnOpRlz_reportNumber,
            'prvMtnOpRlz_validate' => $request->prvMtnOpRlz_validate,
            'prvMtnOpRlz_startDate' => $request->prvMtnOpRlz_startDate,
            'prvMtnOpRlz_endDate' => $request->prvMtnOpRlz_endDate,
            'prvMtnOpRlz_entryDate' => Carbon::now('Europe/Paris'),
            'state_id' => $request->state_id,
            'prvMtnOp_id' => $request->prvMtnOp_id,
            'enteredBy_id' => $request->enteredBy_id,
            'prvMtnOpRlz_comment' => $request->prvMtnOpRlz_comment,

        ]) ;

        // On update la nextDate dans prvMtnOp

        $ymd=explode('-', $request->prvMtnOpRlz_startDate);
        $year=$ymd[0] ;
        $month=$ymd[1] ;
        $day=$ymd[2] ;

        $nextDate=Carbon::create($year, $month, $day, 0, 0, 0);

        if ($prvMtnOp->prvMtnOp_symbolPeriodicity=='Y'){
            $nextDate->addYears($prvMtnOp->prvMtnOp_periodicity) ;
        }

        if ($prvMtnOp->prvMtnOp_symbolPeriodicity=='M'){
            $nextDate->addMonths($prvMtnOp->prvMtnOp_periodicity) ;
        }

        if ($prvMtnOp->prvMtnOp_symbolPeriodicity=='D'){
            $nextDate->addDays($prvMtnOp->prvMtnOp_periodicity) ;
        }
        if ($prvMtnOp->prvMtnOp_symbolPeriodicity=='H'){
            $nextDate->addHours($prvMtnOp->prvMtnOp_periodicity) ;
        }
        if ($request->prvMtnOpRlz_validate === 'validated') {
            $prvMtnOp->update([
                'prvMtnOp_nextDate' => $nextDate,
            ]);
        }

        $prvMtnOpRlz_id=$prvMtnOpRlz->id;
        return response()->json($prvMtnOpRlz_id) ;
    }


     /**
     * Function call by EquipmentPrvMtnOpRlzForm.vue when the form is submitted for check data with the route : /prvMtnOpRlz/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_prvMtnOpRlz(Request $request){
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
            }
        }

        $this->validate(
            $request,
            [
                'prvMtnOpRlz_reportNumber' => 'required|min:3|max:255',
                'prvMtnOpRlz_comment' => 'max:255',
            ],
            [
                'prvMtnOpRlz_reportNumber.required' => 'You must enter a report number for the preventive maintenance operation realized ',
                'prvMtnOpRlz_reportNumber.min' => 'You must enter at least three characters ',
                'prvMtnOpRlz_reportNumber.max' => 'You must enter a maximum of 255 characters',

                'prvMtnOpRlz_comment.max' => 'You must enter a maximum of 255 characters',
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

    /**
     * Function call by EquipmentPrvMtnOpRlzForm.vue when the form is submitted for update with the route :/equipment/update/prvMtnOpRlz/{id} (post)
     * Update an enregistrement of preventive maintenance operation realized in the data base with the informations entered in the form
     * The id parameter correspond to the id of the preventive maintenance operation realized we want to update
     * */
    public function update_prvMtnOpRlz(Request $request, $id){
        $prvMtnOpRlz=PreventiveMaintenanceOperationRealized::FindOrFail($id) ;
        $prvMtnOpRlz->update([
            'prvMtnOpRlz_reportNumber' => $request->prvMtnOpRlz_reportNumber,
            'prvMtnOpRlz_validate' => $request->prvMtnOpRlz_validate,
            'prvMtnOpRlz_startDate' => $request->prvMtnOpRlz_startDate,
            'prvMtnOpRlz_endDate' => $request->prvMtnOpRlz_endDate,
            'prvMtnOpRlz_comment' => $request->prvMtnOpRlz_comment,
        ]);

        $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($prvMtnOpRlz->prvMtnOp_id) ;

        // On update la nextDate dans prvMtnOp

        $ymd=explode('-', $request->prvMtnOpRlz_startDate) ;
        $year=$ymd[0] ;
        $month=$ymd[1] ;
        $day=$ymd[2] ;

        $nextDate=Carbon::create($year, $month, $day, 0, 0, 0);

        if ($prvMtnOp->prvMtnOp_symbolPeriodicity=='Y'){
            $nextDate->addYears($prvMtnOp->prvMtnOp_periodicity) ;
        }

        if ($prvMtnOp->prvMtnOp_symbolPeriodicity=='M'){
            $nextDate->addMonths($prvMtnOp->prvMtnOp_periodicity) ;
        }

        if ($prvMtnOp->prvMtnOp_symbolPeriodicity=='D'){
            $nextDate->addDays($prvMtnOp->prvMtnOp_periodicity) ;
        }
        if ($prvMtnOp->prvMtnOp_symbolPeriodicity=='H'){
            $nextDate->addHours($prvMtnOp->prvMtnOp_periodicity) ;
        }
        if ($request->prvMtnOpRlz_validate === 'validated') {
            $prvMtnOp->update([
                'prvMtnOp_nextDate' => $nextDate,
            ]);
        }
    }


    /**
     * Function call by EquipmentPrvMtnOpRlzForm.vue when we want to delete a preventive maintenance operation realized with the route : /state/delete/prvMtnOpRlz/{id}(post)
     * Delete a preventive maintenance operation realized thanks to the id given in parameter
     * The id parameter correspond to the id of the preventive maintenance operation realized we want to delete
     * */
    public function delete_prvMtnOpRlz($id){
        $prvMtnOpRlz=PreventiveMaintenanceOperationRealized::findOrFail($id);
        if ($prvMtnOpRlz->prvMtnOpRlz_validate=='validated'){
            return response()->json([
                'errors' => [
                    'prvMtnOpRlz_delete' => ["You can delete a preventive maintenance operation realized validated"]
                ]
            ], 429);
        }else{
            $prvMtnOpRlz->delete() ;
        }
    }

    /**
     * Function call by ReferenceAPrvMtnOpRlz.vue with the route : /state/prvMtnOpRlz/send/{id}(get)
     * Get the preventive maintenance operations realized of the equipment whose id is passed in parameter
     * The id parameter corresponds to the id of the state from which we want the preventive maintenance operation realized
     * @return \Illuminate\Http\Response
     */

    public function send_prvMtnOpRlz($id) {
        $state = State::findOrFail($id);
        $container=array() ;
        if (count($state->preventive_maintenance_operation_realizeds)>0){
            $prvMtnOpRlzs=$state->preventive_maintenance_operation_realizeds ;
            foreach ($prvMtnOpRlzs as $prvMtnOpRlz) {
                $prvMtnOp=PreventiveMaintenanceOperation::findOrFail($prvMtnOpRlz->prvMtnOp_id) ;

                $enteredBy_firstName=NULL;
                $enteredBy_lastName=NULL;
                $realizedBy_firstName=NULL;
                $realizedBy_lastName=NULL ;
                $approvedBy_firstName=NULL;
                $approvedBy_lastName=NULL ;

                if ($prvMtnOpRlz->realizedBy_id!=NULL){
                    $realizedBy=User::findOrFail($prvMtnOpRlz->realizedBy_id) ;
                    $realizedBy_firstName=$realizedBy->user_firstName ;
                    $realizedBy_lastName=$realizedBy->user_lastName ;
                }
                if ($prvMtnOpRlz->enteredBy_id!=NULL){
                    $enteredBy=User::findOrFail($prvMtnOpRlz->enteredBy_id) ;
                    $enteredBy_firstName=$enteredBy->user_firstName ;
                    $enteredBy_lastName=$enteredBy->user_lastName ;
                }
                if ($prvMtnOpRlz->approvedBy_id!=NULL){
                    $approvedBy=User::findOrFail($prvMtnOpRlz->approvedBy_id) ;
                    $approvedBy_firstName=$approvedBy->user_firstName ;
                    $approvedBy_lastName=$approvedBy->user_lastName ;
                }

                $obj=([
                    "id" => $prvMtnOpRlz->id,
                    "prvMtnOpRlz_reportNumber" => $prvMtnOpRlz->prvMtnOpRlz_reportNumber,
                    "prvMtnOpRlz_startDate" => $prvMtnOpRlz->prvMtnOpRlz_startDate,
                    "prvMtnOpRlz_endDate" => $prvMtnOpRlz->prvMtnOpRlz_endDate,
                    "prvMtnOpRlz_entryDate" => $prvMtnOpRlz->prvMtnOpRlz_entryDate,
                    "prvMtnOpRlz_validate" => $prvMtnOpRlz->prvMtnOpRlz_validate,
                    "prvMtnOpRlz_comment" => $prvMtnOpRlz->prvMtnOpRlz_comment,
                    "prvMtnOp_id" => $prvMtnOpRlz->prvMtnOp_id,
                    "prvMtnOp_number" => (string)$prvMtnOp->prvMtnOp_number,
                    "prvMtnOp_description" => $prvMtnOp->prvMtnOp_description,
                    "prvMtnOp_protocol" => $prvMtnOp->prvMtnOp_protocol,
                    "realizedBy_firstName" => $realizedBy_firstName,
                    "realizedBy_lastName" => $realizedBy_lastName,
                    "enteredBy_firstName" => $enteredBy_firstName,
                    "enteredBy_lastName" => $enteredBy_lastName,
                    "approvedBy_firstName" => $approvedBy_firstName,
                    "approvedBy_lastName" => $approvedBy_lastName,
                ]);

                array_push($container,$obj);
            }
        }
        return response()->json($container) ;
    }

     /**
     * Function call by PrvMtnOpRlzManagementModal.vue when we want to approuve a preventive maintenance operation realized with the route : /prvMtnOpRlz/approve/{id}
     * Approuve a preventive maintenance operation realized
     * The id parameter correspond to the id of the preventive maintenance operation realized we want to approuve
     * */
    public function approve_prvMtnOpRlz(Request $request, $id){
        $user=User::findOrFail($request->user_id) ;

        if (!Auth::attempt(['user_pseudo' => $request->user_pseudo, 'password' => $request->user_password])) {
            return response()->json([
                'errors' => [
                    'connexion' => ["Verification failed, the couple pseudo password isn't recognized"]
                ]
            ], 429);
        }

        $prvMtnOpRlz=PreventiveMaintenanceOperationRealized::findOrFail($id) ;
        $prvMtnOpRlz->update([
            'approvedBy_id' => $user->id,
        ]);

        if ($prvMtnOpRlz->prvMtnOpRlz_validate!="validated"){
            $prvMtnOpRlz->update([
                'prvMtnOpRlz_validate' => 'validated',
            ]);
        }
    }

     /**
     * Function call by PrvMtnOpRlzManagementModal.vue when we want to tell that we are the realizator of a preventive maintenance operation realized with the route : /prvMtnOpRlz/realize/{id}
     * Tell that you have realize a preventive maintenance operation realized
     * The id parameter correspond to the id of the preventive maintenance operation realized we want to entered the realizator
     * */
    public function realize_prvMtnOpRlz(Request $request, $id){
        $user=User::findOrFail($request->user_id) ;

        if (!Auth::attempt(['user_pseudo' => $request->user_pseudo, 'password' => $request->user_password])) {
            return response()->json([
                'errors' => [
                    'connexion' => ["Verification failed, the couple pseudo password isn't recognized"]
                ]
            ], 429);
        }

        $prvMtnOpRlz=PreventiveMaintenanceOperationRealized::findOrFail($id) ;
        $prvMtnOpRlz->update([
            'realizedBy_id' => $user->id,
        ]);
    }
}
