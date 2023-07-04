<?php

/*
* Filename : CompFamilyController.php
* Creation date : 25 Apr 2023
* Update date : 4 Jul 2023
* This file is used to link the view files and the database that concern the comp family table.
* For example : add a comp family in the data base, update a comp family...
*/

namespace App\Http\Controllers\SW03;

use App\Models\SW03\Criticality;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\PurchaseSpecification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use Illuminate\Support\Facades\DB;
use App\Models\SW03\EnumPurchasedBy;

class CompFamilyController extends Controller
{
    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for check data with the route : /comp/family/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_compFamily(Request $request){

        //-----CASE compFam->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his compFam, so he must enter all the attributes
        if ($request->artFam_validate=='validated'){
            $this->validate(
                $request,
                [
                    'artFam_ref' => 'required|min:3|max:255|string',
                    'artFam_design' => 'required|min:3|max:255|string',
                    'artFam_drawingPath' => 'required|min:3|max:255|string',
                    'artFam_version' => 'required|min:2|max:4|string',
                ],
                [

                    'artFam_ref.required' => 'You must enter a reference for your comp family ',
                    'artFam_ref.min' => 'You must enter at least three characters ',
                    'artFam_ref.max' => 'You must enter less than 255 characters ',
                    'artFam_ref.string' => 'You must enter a string ',

                    'artFam_design.required' => 'You must enter a design for your comp family ',
                    'artFam_design.min' => 'You must enter at least three characters ',
                    'artFam_design.max' => 'You must enter less than 255 characters ',
                    'artFam_design.string' => 'You must enter a string ',

                    'artFam_drawingPath.required' => 'You must enter a drawing path for your comp family ',
                    'artFam_drawingPath.min' => 'You must enter at least three characters ',
                    'artFam_drawingPath.max' => 'You must enter less than 255 characters ',
                    'artFam_drawingPath.string' => 'You must enter a string ',

                    'artFam_version.required' => 'You must enter a version for your comp family ',
                    'artFam_version.min' => 'You must enter at least two characters ',
                    'artFam_version.max' => 'You must enter less than 4 characters ',
                    'artFam_version.string' => 'You must enter a string ',
                ]
            );

            if ($request->artFam_purchasedBy==NULL || $request->artFam_purchasedBy==""){
                return response()->json([
                    'errors' => [
                        'artFam_purchasedBy' => ['You must reference who purchased this comp family'],
                    ],
                ], 422);
            }
        }else{
             //-----CASE artFam->validate=drafted or artFam->validate=to be validated----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations
            $this->validate(
                $request,
                [
                    'artFam_ref' => 'required|min:3|max:255|string',
                    'artFam_design' => 'required|min:3|max:255|string',
                    'artFam_drawingPath' => 'max:255',
                    'artFam_version' => 'max:4',
                ],
                [
                    'artFam_ref.required' => 'You must enter a reference for your comp family ',
                    'artFam_ref.min' => 'You must enter at least three characters ',
                    'artFam_ref.max' => 'You must enter a maximum of 255 characters',
                    'artFam_ref.string' => 'You must enter a string ',

                    'artFam_design.required' => 'You must enter a designation for your comp family ',
                    'artFam_design.min' => 'You must enter at least three characters ',
                    'artFam_design.max' => 'You must enter a maximum of 255 characters',
                    'artFam_design.string' => 'You must enter a string ',

                    'artFam_drawingPath.max' => 'You must enter a maximum of 255 characters',
                    'artFam_drawingPath.string' => 'You must enter a string ',

                    'artFam_version.max' => 'You must enter a maximum of 4 characters',
                    'artFam_version.string' => 'You must enter a string ',

                ]
            );
        }

         //we checked if the reference entered is already used for another component family
         $component_already_exist=CompFamily::where('compFam_ref', '=', $request->artFam_ref, 'and')->where('id', '<>', $request->artFam_id)->first();
         if ($component_already_exist!=null){
             return response()->json([
                 'errors' => [
                     'artFam_ref' => ["This reference is already use for another comp family"]
                 ]
             ], 429);
         }
    }

    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for insert with the route : /comp/family/add (post)
     * Add a new enregistrement of comp family in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new comp family
     */
    public function add_compFamily(Request $request){
        $enum=NULL;
        if ($request->artFam_purchasedBy!="" && $request->artFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }

        //Creation of a new compFam
        $compFamily=compFamily::create([
            'compFam_ref' => $request->artFam_ref,
            'compFam_design' => $request->artFam_design,
            'compFam_drawingPath'=> $request->artFam_drawingPath,
            'enumPurchasedBy_id' => $enum,
            'compFam_validate' => $request->artFam_validate,
            'compFam_version' => $request->artFam_version,
            'compFam_active' => $request->artFam_active,
        ]) ;

        $compFamily_id=$compFamily->id ;

        return response()->json($compFamily_id) ;
    }

    public function send_compFamilies(){
        $compFamilies=CompFamily::all() ;
        $array = [];
        foreach ($compFamilies as $compFamily) {
            $purchaseBy = EnumPurchasedBy::all()->find($compFamily->enumPurchasedBy_id);
            if ($purchaseBy != null) {
                $purchaseBy = $purchaseBy->first()->value;
            } else {
                $purchaseBy = null;
            }
            $qualityApprover = User::all()->find($compFamily->compFam_qualityApproverId);
            if ($qualityApprover != null){
                $qualityApprover = strtoupper($qualityApprover->user_lastName) . ' ' . $qualityApprover->user_firstName;
            } else {
                $qualityApprover = null;
            }
            $technicalReviewer = User::find($compFamily->compFam_technicalReviewerId);
            if ($technicalReviewer != null){
                $technicalReviewer = strtoupper($technicalReviewer->user_lastName) . ' ' . $technicalReviewer->user_firstName;
            } else {
                $technicalReviewer = null;
            }
            $obj = [
                'id' => $compFamily->id,
                'compFam_ref' => $compFamily->compFam_ref,
                'compFam_design' => $compFamily->compFam_design,
                'compFam_drawingPath' => $compFamily->compFam_drawingPath,
                'compFam_variablesCharac' => $compFamily->compFam_variablesCharac,
                'compFam_variablesCharacDesign' => $compFamily->compFam_variablesCharacDesign,
                'compFam_version' => $compFamily->compFam_version,
                'compFam_nbrVersion' => $compFamily->compFam_nbrVersion,
                'compFam_validate' => $compFamily->compFam_validate,
                'compFam_active' => $compFamily->compFam_active,
                'compFam_technicalReviewerId' => $compFamily->compFam_technicalReviewerId,
                'compFam_technicalReviewerName' => $technicalReviewer,
                'compFam_qualityApproverId' => $compFamily->compFam_qualityApproverId,
                'compFam_qualityApproverName' => $qualityApprover,
                'compFam_purchasedBy' => $purchaseBy,
                'compFam_signatureDate' => $compFamily->compFam_signatureDate,
                'compFam_created_at' => $compFamily->created_at,
                'compFam_updated_at' => $compFamily->updated_at,
                'compFam_genRef' => $compFamily->compFam_genRef,
                'compFam_genDesign' => $compFamily->compFam_genDesign,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    public function send_compFamily($id) {
        $compFamily = CompFamily::find($id);
        $purchaseBy = EnumPurchasedBy::find($compFamily->enumPurchasedBy_id);
        if ($purchaseBy != null) {
            $purchaseBy = $purchaseBy->first()->value;
        } else {
            $purchaseBy = null;
        }
        $qualityApprover = User::find($compFamily->compFam_qualityApproverId);
        if ($qualityApprover != null){
            $qualityApprover = strtoupper($qualityApprover->user_lastName) . ' ' . $qualityApprover->user_firstName;
        } else {
            $qualityApprover = null;
        }
        $technicalReviewer = User::find($compFamily->compFam_technicalReviewerId);
        if ($technicalReviewer != null){
            $technicalReviewer = strtoupper($technicalReviewer->user_lastName) . ' ' . $technicalReviewer->user_firstName;
        } else {
            $technicalReviewer = null;
        }
        $obj = [
            'id' => $compFamily->id,
            'compFam_ref' => $compFamily->compFam_ref,
            'compFam_design' => $compFamily->compFam_design,
            'compFam_drawingPath' => $compFamily->compFam_drawingPath,
            'compFam_variablesCharac' => $compFamily->compFam_variablesCharac,
            'compFam_variablesCharacDesign' => $compFamily->compFam_variablesCharacDesign,
            'compFam_version' => $compFamily->compFam_version,
            'compFam_nbrVersion' => $compFamily->compFam_nbrVersion,
            'compFam_validate' => $compFamily->compFam_validate,
            'compFam_active' => $compFamily->compFam_active,
            'compFam_technicalReviewerId' => $compFamily->compFam_technicalReviewerId,
            'compFam_technicalReviewerName' => $technicalReviewer,
            'compFam_qualityApproverId' => $compFamily->compFam_qualityApproverId,
            'compFam_qualityApproverName' => $qualityApprover,
            'compFam_purchasedBy' => $purchaseBy,
            'compFam_signatureDate' => $compFamily->compFam_signatureDate,
            'compFam_created_at' => $compFamily->created_at,
            'compFam_updated_at' => $compFamily->updated_at,
            'compFam_genRef' => $compFamily->compFam_genRef,
            'compFam_genDesign' => $compFamily->compFam_genDesign,
        ];
        return response()->json($obj);
    }

    public function update_compFamily(Request $request, $id) {
        $compFamily = CompFamily::findOrfail($id);
        if ($compFamily->compFam_signatureDate != null) {
            $compFamily->update([
                'compFam_nbrVersion' => $compFamily->compFam_nbrVersion + 1,
            ]);
        }
        $enum=NULL;
        if ($request->artFam_purchasedBy!="" && $request->artFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }
        $compFamily->update([
            'compFam_ref' => $request->artFam_ref,
            'compFam_design' => $request->artFam_design,
            'compFam_drawingPath' => $request->artFam_drawingPath,
            'compFam_variablesCharac' => $request->artFam_variablesCharac,
            'compFam_variablesCharacDesign' => $request->artFam_variablesCharacDesign,
            'compFam_version' => $request->artFam_version,
            'compFam_qualityApproverId' => null,
            'compFam_technicalReviewerId' => null,
            'compFam_signatureDate' => null,
            'compFam_validate' => $request->artFam_validate,
            'compFam_active' => $request->artFam_active,
            'compFam_genRef' => $request->artFam_genRef,
            'compFam_genDesign' => $request->artFam_genDesign,
            'enumPurchasedBy_id' => $enum,
        ]);
        return response()->json($compFamily);
    }

    public function verifValidation_compFamily($id) {
        $article = CompFamily::all()->where('id', '==', $id)->first();
        if ($article->compFam_validate !== 'validated') {
            return response()->json([
                'error' => 'This article family is not validated'
            ], 429);
        }
        $criticalities = Criticality::all()->where('compFam_id', '==', $id);
        foreach ($criticalities as $criticality) {
            if ($criticality->crit_validate !== 'validated') {
                return response()->json([
                    'error' => 'This article family has not validated criticalities'
                ], 429);
            }
        }
        $purSpes = PurchaseSpecification::all()->where('compFam_id', '==', $id);
        foreach ($purSpes as $purSpe) {
            if ($purSpe->purSpe_validate !== 'validated') {
                return response()->json([
                    'error' => 'This article family has not validated purchase specifications'
                ], 429);
            }
        }
        $incmgInsp = IncomingInspection::all()->where('compFam_id', '==', $id);
        foreach ($incmgInsp as $incmg) {
            if ($incmg->incmgInsp_validate !== 'validated') {
                return response()->json([
                    'error' => 'This article family has not validated incoming inspections'
                ], 429);
            }
        }
    }

    public function validate_compFamily(Request $request, $id) {
        $compFamily = CompFamily::findOrfail($id);
        if ($request->reason === 'technical') {
            $compFamily->update([
                'compFam_technicalReviewerId' => $request->user_id,
            ]);
        } else if ($request->reason === 'quality') {
            $compFamily->update([
                'compFam_qualityApproverId' => $request->user_id,
            ]);
        }
        if ($compFamily->compFam_technicalReviewerId != null && $compFamily->compFam_qualityApproverId != null) {
            $compFamily->update([
                'compFam_signatureDate' => Carbon::now(),
            ]);
        }
    }
}
