<?php

/*
* Filename : RawFamilyController.php
* Creation date : 28 Apr 2023
* Update date : 5 Jul 2023
* This file is used to link the view files and the database that concern the raw family table.
* For example : add a raw family in the data base, update a raw family...
*/

namespace App\Http\Controllers\SW03;

use App\Models\SW03\Criticality;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\PurchaseSpecification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SW03\RawFamily;
use Illuminate\Support\Facades\DB;
use App\Models\SW03\EnumPurchasedBy;

class RawFamilyController extends Controller
{
    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for check data with the route : /raw/family/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_rawFamily(Request $request){

        //-----CASE rawFam->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his rawFam, so he must enter all the attributes
        if ($request->artFam_validate=='validated'){
            $this->validate(
                $request,
                [
                    'artFam_ref' => 'required|max:255|string',
                    'artFam_design' => 'required|max:255|string',
                    'artFam_drawingPath' => 'required|max:255|string',
                ],
                [

                    'artFam_ref.required' => 'You must enter a reference for your raw family ',
                    'artFam_ref.max' => 'You must enter less than 255 characters ',
                    'artFam_ref.string' => 'You must enter a string ',

                    'artFam_design.required' => 'You must enter a design for your raw family ',
                    'artFam_design.max' => 'You must enter less than 255 characters ',
                    'artFam_design.string' => 'You must enter a string ',

                    'artFam_drawingPath.required' => 'You must enter a drawing path for your raw family ',
                    'artFam_drawingPath.max' => 'You must enter less than 255 characters ',
                    'artFam_drawingPath.string' => 'You must enter a string ',

                ]
            );

            if ($request->artFam_purchasedBy==NULL || $request->artFam_purchasedBy==""){
                return response()->json([
                    'errors' => [
                        'artFam_purchasedBy' => ['You must reference who purchased this raw family'],
                    ],
                ], 422);
            }



        }else{
             //-----CASE artFam->validate=drafted or artFam->validate=to be validated----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations
            $this->validate(
                $request,
                [
                    'artFam_ref' => 'required|max:255|string',
                    'artFam_design' => 'required|max:255|string',
                    'artFam_drawingPath' => 'max:255|string',
                ],
                [
                    'artFam_ref.required' => 'You must enter a reference for your raw family ',
                    'artFam_ref.max' => 'You must enter a maximum of 255 characters',
                    'artFam_ref.string' => 'You must enter a string ',

                    'artFam_design.required' => 'You must enter a designation for your raw family ',
                    'artFam_design.max' => 'You must enter a maximum of 255 characters',
                    'artFam_design.string' => 'You must enter a string ',

                    'artFam_drawingPath.max' => 'You must enter a maximum of 255 characters',
                    'artFam_drawingPath.string' => 'You must enter a string ',
                ]
            );
        }

         //we checked if the reference entered is already used for another raw family
         $raw_already_exist=RawFamily::where('rawFam_ref', '=', $request->artFam_ref, 'and')->where('id', '<>', $request->artFam_id)->first() ;
         if ($raw_already_exist!=null){
             return response()->json([
                 'errors' => [
                     'artFam_ref' => ["This reference is already use for another raw family"]
                 ]
             ], 429);
         }
    }

    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for insert with the route : /raw/family/add (post)
     * Add a new enregistrement of raw family in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new raw family
     */
    public function add_rawFamily(Request $request){
        $enum=NULL;
        if ($request->artFam_purchasedBy!="" && $request->artFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }

        //Creation of a new rawFam
        $rawFamily=rawFamily::create([
            'rawFam_ref' => $request->artFam_ref,
            'rawFam_design' => $request->artFam_design,
            'rawFam_drawingPath'=> $request->artFam_drawingPath,
            'enumPurchasedBy_id' => $enum,
          /*  'rawFam_variablesCharac' => $request->artFam_variablesCharac,
            'rawFam_variablesCharacDesign' => $request->artFam_variablesCharacDesign,*/
            'rawFam_validate' => $request->artFam_validate,
            'rawFam_active' => $request->artFam_active,
           /* 'rawFam_genRef' => $request->artFam_genRef,
            'rawFam_genDesign' => $request->artFam_genDesign,*/
            'rawFam_subFam' => $request->artFam_subFam,
        ]) ;

        $rawFamily_id=$rawFamily->id ;

        return response()->json($rawFamily_id) ;
    }

    /**
     * Function call by ListOfArticle.vue when the form is submitted for insert with the route : /raw/family/send (post)
     * Get all the family raw corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_rawFamilies(){
        $rawFamilies=RawFamily::all() ;
        $array = [];
        foreach ($rawFamilies as $rawFamily){
            $purchaseBy = EnumPurchasedBy::find($rawFamily->enumPurchasedBy_id);
            if ($purchaseBy != null){
                $purchaseBy = $purchaseBy->first()->value;
            } else {
                $purchaseBy = null;
            }
            $qualityApprover = User::find($rawFamily->rawFam_qualityApproverId);
            if ($qualityApprover != null){
                $qualityApprover = strtoupper($qualityApprover->user_lastName) . ' ' . $qualityApprover->user_firstName;
            } else {
                $qualityApprover = null;
            }
            $technicalReviewer = User::find($rawFamily->rawFam_technicalReviewerId);
            if ($technicalReviewer != null){
                $technicalReviewer = strtoupper($technicalReviewer->user_lastName) . ' ' . $technicalReviewer->user_firstName;
            } else {
                $technicalReviewer = null;
            }
            $obj = [
                'id' => $rawFamily->id,
                'rawFam_ref' => $rawFamily->rawFam_ref,
                'rawFam_design' => $rawFamily->rawFam_design,
                'rawFam_drawingPath' => $rawFamily->rawFam_drawingPath,
                'rawFam_validate' => $rawFamily->rawFam_validate,
                'rawFam_active' => $rawFamily->rawFam_active,
                'rawFam_purchasedBy' => $purchaseBy,
                'rawFam_qualityApproverId' => $rawFamily->rawFam_qualityApproverId,
                'rawFam_qualityApproverName' => $qualityApprover,
                'rawFam_technicalReviewerId' => $rawFamily->rawFam_technicalReviewerId,
                'rawFam_technicalReviewerName' => $technicalReviewer,
                'rawFam_signatureDate' => $rawFamily->rawFam_signatureDate,
                'rawFam_nbrVersion' => $rawFamily->rawFam_nbrVersion,
                'rawFam_subFam' => $rawFamily->rawFam_subFam,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

     /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for insert with the route : /raw/family/send/{id} (post)
     * Get the family raw corresponding of the id parameter
     * The id parameter corresponds to the id of the family raw from which we want the informations
     * @return \Illuminate\Http\Response
     */
    public function send_rawFamily($id) {
        $rawFamily = RawFamily::find($id);
        $purchaseBy = EnumPurchasedBy::find($rawFamily->enumPurchasedBy_id);
        if ($purchaseBy != null){
            $purchaseBy = $purchaseBy->first()->value;
        } else {
            $purchaseBy = null;
        }
        $qualityApprover = User::find($rawFamily->rawFam_qualityApproverId);
        if ($qualityApprover != null){
            $qualityApprover = strtoupper($qualityApprover->user_lastName) . ' ' . $qualityApprover->user_firstName;
        } else {
            $qualityApprover = null;
        }
        $technicalReviewer = User::find($rawFamily->rawFam_technicalReviewerId);
        if ($technicalReviewer != null){
            $technicalReviewer = strtoupper($technicalReviewer->user_lastName) . ' ' . $technicalReviewer->user_firstName;
        } else {
            $technicalReviewer = null;
        }
        $obj = [
            'id' => $rawFamily->id,
            'rawFam_ref' => $rawFamily->rawFam_ref,
            'rawFam_design' => $rawFamily->rawFam_design,
            'rawFam_drawingPath' => $rawFamily->rawFam_drawingPath,
            'rawFam_validate' => $rawFamily->rawFam_validate,
            'rawFam_active' => $rawFamily->rawFam_active,
            'rawFam_purchasedBy' => $purchaseBy,
            'rawFam_qualityApproverId' => $rawFamily->rawFam_qualityApproverId,
            'rawFam_qualityApproverName' => $qualityApprover,
            'rawFam_technicalReviewerId' => $rawFamily->rawFam_technicalReviewerId,
            'rawFam_technicalReviewerName' => $technicalReviewer,
            'rawFam_signatureDate' => $rawFamily->rawFam_signatureDate,
            'rawFam_nbrVersion' => $rawFamily->rawFam_nbrVersion,
            'rawFam_subFam' => $rawFamily->rawFam_subFam,
        ];
        return response()->json($obj);
    }

    /**
     * Function call by ArticleUpdate.vue when the form is submitted for update with the route :/raw/family/update/{id} (post)
     * Update an enregistrement of raw family in the data base with the informations entered in the form
     * The id parameter correspond to the id of the raw family we want to update
     * */
    public function update_rawFamily(Request $request, $id) {
        $rawFamily = RawFamily::findOrfail($id);
        if ($rawFamily->rawFam_signatureDate != null) {
            $rawFamily->update([
                'rawFam_nbrVersion' => $rawFamily->rawFam_nbrVersion + 1,
            ]);
        }
        $enum=NULL;
        if ($request->artFam_purchasedBy!="" && $request->artFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }
        $rawFamily->update([
            'rawFam_ref' => $request->artFam_ref,
            'rawFam_design' => $request->artFam_design,
            'rawFam_drawingPath' => $request->artFam_drawingPath,
            'rawFam_qualityApproverId' => null,
            'rawFam_technicalReviewerId' => null,
            'rawFam_signatureDate' => null,
            'rawFam_validate' => $request->artFam_validate,
            'rawFam_active' => $request->artFam_active,
            'enumPurchasedBy_id' => $enum,
            'rawFam_subFam' => $request->artFam_subFam,
        ]);
        return response()->json($rawFamily);
    }

    /**
     * Function call by ArticleConsult.vue when the form is submitted for update with the route : /raw/verifValidation/{id} (post)
     * Tell if the raw family is ready to be validated
     * The id parameter is the id of the raw family in which we want to validate
     * @return \Illuminate\Http\Response
     * */
    public function verifValidation_rawFamily($id) {
        $article = RawFamily::all()->where('id', '==', $id)->first();
        if ($article->rawFam_validate !== 'validated') {
            return response()->json([
                'error' => 'This article family is not validated'
            ], 429);
        }
        $criticalities = Criticality::all()->where('rawFam_id', '==', $id);
        foreach ($criticalities as $criticality) {
            if ($criticality->crit_validate !== 'validated') {
                return response()->json([
                    'error' => 'This article family has not validated criticalities'
                ], 429);
            }
        }
        $purSpes = PurchaseSpecification::all()->where('rawFam_id', '==', $id);
        foreach ($purSpes as $purSpe) {
            if ($purSpe->purSpe_validate !== 'validated') {
                return response()->json([
                    'error' => 'This article family has not validated purchase specifications'
                ], 429);
            }
        }
        $incmgInsp = IncomingInspection::all()->where('rawFam_id', '==', $id);
        foreach ($incmgInsp as $incmg) {
            if ($incmg->incmgInsp_validate !== 'validated') {
                return response()->json([
                    'error' => 'This article family has not validated incoming inspections'
                ], 429);
            }
        }
    }

    /**
     * Function call by ArticleConsult.vue when the form is submitted for update with the route : /raw/validation/id (post)
     * Tell if the raw family is ready to be validated
     * The id parameter is the id of the raw in which we want to validate
     * @return \Illuminate\Http\Response
     * */
    public function validate_rawFamily(Request $request, $id) {
        $rawFamily = RawFamily::findOrfail($id);
        if ($request->reason === 'technical') {
            $rawFamily->update([
                'rawFam_technicalReviewerId' => $request->user_id,
            ]);
        } else if ($request->reason === 'quality') {
            $rawFamily->update([
                'rawFam_qualityApproverId' => $request->user_id,
            ]);
        }
        if ($rawFamily->rawFam_technicalReviewerId != null && $rawFamily->rawFam_qualityApproverId != null) {
            $rawFamily->update([
                'rawFam_signatureDate' => Carbon::now(),
            ]);
        }
    }
}
