<?php

namespace App\Http\Controllers\SW01;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ;
use App\Models\SW01\VerificationRealized ;
use App\Models\SW01\MmeTemp ;
use App\Models\SW01\Mme ;
use App\Models\SW01\MmeState ;
use App\Models\User ;
use App\Models\SW01\Verification ;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Controller;


class VerificationRealizedController extends Controller
{


    /**
     * Function call by MmeVerifRlzForm.vue when the form is submitted for insert with the route :'/mme/add/mme_state/verifRlz' (post)
     * Add a new enregistrement of verification realized in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : the id of the new verifRlz
     */
    public function add_verifRlz(Request $request){
        $state=MmeState::findOrFail($request->state_id) ;
        $verif=Verification::findOrFail($request->verif_id) ;

        //Creation of a new verification realized
        $verifRlz=VerificationRealized::create([
            'verifRlz_reportNumber' => $request->verifRlz_reportNumber,
            'verifRlz_validate' => $request->verifRlz_validate,
            'verifRlz_startDate' => $request->verifRlz_startDate,
            'verifRlz_endDate' => $request->verifRlz_endDate,
            'verifRlz_isPassed' => $request->verifRlz_isPassed,
            'verifRlz_entryDate' => Carbon::now('Europe/Paris'),
            'state_id' => $request->state_id,
            'verif_id' => $request->verif_id,
            'enteredBy_id' => $request->enteredBy_id,
            'verifRlz_comment' => $request->verifRlz_comment,

        ]) ;

        // On update la nextDate dans verification

        $ymd=explode('-', $request->verifRlz_startDate) ;
        $year=$ymd[0] ;
        $month=$ymd[1] ;
        $day=$ymd[2] ;

        $nextDate=Carbon::create($year, $month, $day, 0, 0, 0);

        if ($verif->verif_symbolPeriodicity=='Y'){
            $nextDate->addYears($verif->verif_periodicity) ;
        }

        if ($verif->verif_symbolPeriodicity=='M'){
            $nextDate->addMonths($verif->verif_periodicity) ;
        }

        if ($verif->verif_symbolPeriodicity=='D'){
            $nextDate->addDays($verif->verif_periodicity) ;
        }
        if ($verif->verif_symbolPeriodicity=='H'){
            $nextDate->addHours($verif->verif_periodicity) ;
        }

        if ($request->verifRlz_validate === "validated") {
            $verif->update([
                'verif_nextDate' => $nextDate,
            ]);
        }
        $verifRlz_id=$verifRlz->id;
        return response()->json($verifRlz_id) ;
    }


     /**
     * Function call by MmeVerifRlzForm.vue when the form is submitted for check data with the route : '/verifRlz/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_verifRlz(Request $request){
        $state=MmeState::findOrFail($request->state_id) ;
        $verif=Verification::findOrFail($request->verif_id) ;

        if ($request->verifRlz_validate=="validated"){
            if ($request->verifRlz_endDate=='' || $request->verifRlz_endDate===NULL){
                return response()->json([
                    'errors' => [
                        'verifRlz_endDate' => ["You have to entered the endDate of your verification realized for validate it"]
                    ]
                ], 429);
            }
        }



        $this->validate(
            $request,
            [
                'verifRlz_reportNumber' => 'required|min:3|max:255',

                'verifRlz_comment' => 'max:255',
            ],
            [
                'verifRlz_reportNumber.required' => 'You must enter a report number for the verification realized ',
                'verifRlz_reportNumber.min' => 'You must enter at least three characters ',
                'verifRlz_reportNumber.max' => 'You must enter a maximum of 255 characters',

                'verifRlz_comment.max' => 'You must enter a maximum of 255 characters',
            ]
        );
        if ($request->verifRlz_startDate=='' || $request->verifRlz_startDate===NULL){
            return response()->json([
                'errors' => [
                    'verifRlz_startDate' => ["You have to entered the startDate of your verification realized"]
                ]
            ], 429);
        }

        if ($request->reason=="update"){
            $verifRlz=VerificationRealized::findOrFail($request->verifRlz_id ) ;
            if ($verifRlz->verifRlz_validate=="validated"){

                if ($verifRlz->realizedBy_id==NULL){
                    return response()->json([
                        'errors' => [
                            'verifRlz_validate' => ["You have to entered the realizator of this verification realized for validate it"]
                        ]
                    ], 429);
                }

                if ($verifRlz->approvedBy_id==NULL){
                    return response()->json([
                        'errors' => [
                            'verifRlz_validate' => ["You have to entered the person who approved this verification realized for validate it"]
                        ]
                    ], 429);
                }

                return response()->json([
                    'errors' => [
                        'verifRlz_validate' => ["You can't update a verification realized already validated"]
                    ]
                ], 429);
            }
        }else{
            if ($request->verifRlz_validate=="validated"){

                return response()->json([
                    'errors' => [
                        'verifRlz_validate' => ["You have to entered the realizator of this verification realized for validate it"]
                    ]
                ], 429);



                return response()->json([
                    'errors' => [
                        'verifRlz_validate' => ["You have to entered the person who approved this verification realized for validate it"]
                    ]
                ], 429);
            }

        }

        $oneMonthAgo=Carbon::now()->subMonth(1) ;
        if ($request->verifRlz_startDate!=NULL && $request->verifRlz_startDate<$oneMonthAgo){
            return response()->json([
                'errors' => [
                    'verifRlz_startDate' => ["You can't enter a date that is older than one month"]
                ]
            ], 429);
        }

        if ($request->verifRlz_endDate!=NULL && $request->verifRlz_endDate<$oneMonthAgo){
            return response()->json([
                'errors' => [
                    'verifRlz_endDate' => ["You can't enter a date that is older than one month"]
                ]
            ], 429);
        }

        if ($state->state_startDate!=NULL && $request->verifRlz_startDate!=NULL){
            if ($request->verifRlz_startDate<$state->state_startDate){
                return response()->json([
                    'errors' => [
                        'verifRlz_startDate' => ["You can't entered this startDate because it must be after the startDate of the state"]
                    ]
                ], 429);
            }
        }
        if ($state->state_startDate!=NULL && $request->verifRlz_endDate!=NULL){
            if ($request->verifRlz_endDate<$state->state_startDate){
                return response()->json([
                    'errors' => [
                        'verifRlz_endDate' => ["You can't entered this endDate because it must be after the startDate of the state"]
                    ]
                ], 429);
            }
        }

        if ($request->verifRlz_startDate!=NULL && $request->verifRlz_endDate!=NULL){
            if ($request->verifRlz_endDate < $request->verifRlz_startDate){
                return response()->json([
                    'errors' => [
                        'verifRlz_endDate' => ["You must entered a startDate that is before endDate"]
                    ]
                ], 429);

            }
        }
    }

    /**
     * Function call by MmeVerifRlzForm.vue when the form is submitted for update with the route :'/mme/update/mme_state/verifRlz/{id}' (post)
     * Update an enregistrement of verification realized in the data base with the informations entered in the form
     * The id parameter correspond to the id of the verification realized we want to update
     * */
    public function update_verifRlz(Request $request, $id){
        $verifRlz=VerificationRealized::findOrFail($id) ;
        $verifRlz->update([
            'verifRlz_reportNumber' => $request->verifRlz_reportNumber,
            'verifRlz_validate' => $request->verifRlz_validate,
            'verifRlz_startDate' => $request->verifRlz_startDate,
            'verifRlz_endDate' => $request->verifRlz_endDate,
            'verifRlz_isPassed' => $request->verifRlz_isPassed,
            'verifRlz_comment' => $request->verifRlz_comment,
        ]);

        $verif=Verification::findOrFail($verifRlz->verif_id) ;

        // On update la nextDate dans verif

        $ymd=explode('-', $request->verifRlz_startDate) ;
        $year=$ymd[0] ;
        $month=$ymd[1] ;
        $day=$ymd[2] ;

        $nextDate=Carbon::create($year, $month, $day, 0, 0, 0);

        if ($verif->verif_symbolPeriodicity=='Y'){
            $nextDate->addYears($verif->verif_periodicity) ;
        }

        if ($verif->verif_symbolPeriodicity=='M'){
            $nextDate->addMonths($verif->verif_periodicity) ;
        }

        if ($verif->verif_symbolPeriodicity=='D'){
            $nextDate->addDays($verif->verif_periodicity) ;
        }
        if ($verif->verif_symbolPeriodicity=='H'){
            $nextDate->addHours($verif->verif_periodicity) ;
        }
        if ($request->verifRlz_validate === 'validated') {
            $verif->update([
                'verif_nextDate' => $nextDate,
            ]);
        }
    }


    /**
     * Function call by MmeVerifRlzForm.vue when we want to delete a verification realized with the route : /mme_state/delete/verifRlz/{id}(post)
     * Delete a verification realized thanks to the id given in parameter
     * The id parameter correspond to the id of the verification realized we want to delete
     * */
    public function delete_verifRlz($id){
        $verifRlz=VerificationRealized::findOrFail($id);
        if ($verifRlz->verifRlz_validate=='validated'){
            return response()->json([
                'errors' => [
                    'verifRlz_delete' => ["You can delete a verification realized validated"]
                ]
            ], 429);
        }else{
            $verifRlz->delete() ;
        }
    }

    /**
     * Function call by ReferenceAVerifRlz.vue with the route : /mme_state/verifRlz/send/{id} (get)
     * Get the verifications realized of the mme whose id is passed in parameter
     * The id parameter corresponds to the id of the state from which we want the verifications realized
     * @return \Illuminate\Http\Response
     */

    public function send_verifRlz($id) {
        $state = MmeState::findOrFail($id);
        $container=array() ;
        $verifRlzs=VerificationRealized::where('state_id', '=', $id)->get();
        foreach ($verifRlzs as $verifRlz) {
            $verif=Verification::findOrFail($verifRlz->verif_id) ;

            $enteredBy_firstName=NULL;
            $enteredBy_lastName=NULL;
            $realizedBy_firstName=NULL;
            $realizedBy_lastName=NULL ;
            $approvedBy_firstName=NULL;
            $approvedBy_lastName=NULL ;

            if ($verifRlz->realizedBy_id!=NULL){
                $realizedBy=User::findOrFail($verifRlz->realizedBy_id) ;
                $realizedBy_firstName=$realizedBy->user_firstName ;
                $realizedBy_lastName=$realizedBy->user_lastName ;
            }
            if ($verifRlz->enteredBy_id!=NULL){
                $enteredBy=User::findOrFail($verifRlz->enteredBy_id) ;
                $enteredBy_firstName=$enteredBy->user_firstName ;
                $enteredBy_lastName=$enteredBy->user_lastName ;
            }
            if ($verifRlz->approvedBy_id!=NULL){
                $approvedBy=User::findOrFail($verifRlz->approvedBy_id) ;
                $approvedBy_firstName=$approvedBy->user_firstName ;
                $approvedBy_lastName=$approvedBy->user_lastName ;
            }

            $obj=([
                "id" => $verifRlz->id,
                "verifRlz_reportNumber" => $verifRlz->verifRlz_reportNumber,
                "verifRlz_startDate" => $verifRlz->verifRlz_startDate,
                "verifRlz_endDate" => $verifRlz->verifRlz_endDate,
                "verifRlz_entryDate" => $verifRlz->verifRlz_entryDate,
                "verifRlz_validate" => $verifRlz->verifRlz_validate,
                'verifRlz_isPassed' => (boolean)$verifRlz->verifRlz_isPassed,
                "verifRlz_comment" => $verifRlz->verifRlz_comment,
                "verif_id" => $verifRlz->verif_id,
                "verif_number" => (string)$verif->verif_number,
                "verif_description" => $verif->verif_description,
                "verif_protocol" => $verif->verif_protocol,
                "verif_expectedResult" => $verif->verif_expectedResult,
                'verif_nonComplianceLimit' => $verif->verif_nonComplianceLimit,
                "realizedBy_firstName" => $realizedBy_firstName,
                "realizedBy_lastName" => $realizedBy_lastName,
                "enteredBy_firstName" => $enteredBy_firstName,
                "enteredBy_lastName" => $enteredBy_lastName,
                "approvedBy_firstName" => $approvedBy_firstName,
                "approvedBy_lastName" => $approvedBy_lastName,
            ]);

            array_push($container,$obj);
        }
        return response()->json($container) ;
    }

     /**
     * Function call by verifRlzManagementModal.vue when we want to approuve a verification realized with the route : /verifRlz/approve/{id}
     * Approuve a verification realized
     * The id parameter correspond to the id of the verification realized we want to approuve
     * */
    public function approve_verifRlz(Request $request, $id){
        $user=User::findOrFail($request->user_id) ;

        if (!Auth::attempt(['user_pseudo' => $request->user_pseudo, 'password' => $request->user_password])) {
            return response()->json([
                'errors' => [
                    'connexion' => ["Verification failed, the couple pseudo password isn't recognized"]
                ]
            ], 429);
        }

        $verifRlz=VerificationRealized::findOrFail($id) ;
        $verifRlz->update([
            'approvedBy_id' => $user->id,
        ]);
    }

     /**
     * Function call by VerifRlzManagementModal.vue when we want to tell that we are the realizator of a verification realized with the route : '/verifRlz/realize/{id}
     * Tell that you have realize a verification realized
     * The id parameter correspond to the id of the verification realized we want to entered the realizator
     * */
    public function realize_verifRlz(Request $request, $id){
        $user=User::findOrFail($request->user_id) ;

        if (!Auth::attempt(['user_pseudo' => $request->user_pseudo, 'password' => $request->user_password])) {
            return response()->json([
                'errors' => [
                    'connexion' => ["Verification failed, the couple pseudo password isn't recognized"]
                ]
            ], 429);
        }

        $verifRlz=VerificationRealized::findOrFail($id) ;
        $verifRlz->update([
            'realizedBy_id' => $user->id,
        ]);
    }
}
