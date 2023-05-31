<?php

/*
* Filename : ConsFamilyController.php
* Creation date : 2 May 2023
* Update date : 2 May 2023
* This file is used to link the view files and the database that concern the cons family table.
* For example : add a cons family in the data base, update a cons family...
*/


namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\Criticality;
use App\Models\SW03\EnumPurchasedBy;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\PurchaseSpecification;
use App\Models\SW03\RawFamily;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ConsFamilyController extends Controller
{
    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for check data with the route : /cons/family/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_consFamily(Request $request)
    {

        //-----CASE consFam->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his consFam, so he must enter all the attributes
        if ($request->artFam_validate == 'validated') {
            $this->validate(
                $request,
                [
                    'artFam_ref' => 'required|min:3|max:255|string',
                    'artFam_design' => 'required|min:3|max:255|string',
                    'artFam_drawingPath' => 'required|min:3|max:255|string',
                    'artFam_version' => 'required|min:2|max:4|string',
                ],
                [

                    'artFam_ref.required' => 'You must enter a reference for your cons family ',
                    'artFam_ref.min' => 'You must enter at least three characters ',
                    'artFam_ref.max' => 'You must enter less than 255 characters ',
                    'artFam_ref.string' => 'You must enter a string ',

                    'artFam_design.required' => 'You must enter a design for your cons family ',
                    'artFam_design.min' => 'You must enter at least three characters ',
                    'artFam_design.max' => 'You must enter less than 255 characters ',
                    'artFam_design.string' => 'You must enter a string ',

                    'artFam_drawingPath.required' => 'You must enter a drawing path for your cons family ',
                    'artFam_drawingPath.min' => 'You must enter at least three characters ',
                    'artFam_drawingPath.max' => 'You must enter less than 255 characters ',
                    'artFam_drawingPath.string' => 'You must enter a string ',

                    'artFam_version.required' => 'You must enter a version for your cons family ',
                    'artFam_version.min' => 'You must enter at least two characters ',
                    'artFam_version.max' => 'You must enter less than 4 characters ',
                    'artFam_version.string' => 'You must enter a string ',
                ]
            );

            if ($request->artFam_purchasedBy == NULL || $request->artFam_purchasedBy == "") {
                return response()->json([
                    'errors' => [
                        'artFam_purchasedBy' => ['You must reference who purchased this cons family'],
                    ],
                ], 422);
            }


        } else {
            //-----CASE artFam->validate=drafted or artFam->validate=to be validated----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations
            $this->validate(
                $request,
                [
                    'artFam_ref' => 'required|min:3|max:255|string',
                    'artFam_design' => 'required|min:3|max:255|string',
                    'artFam_drawingPath' => 'max:255|string',
                    'artFam_version' => 'max:4|string',
                ],
                [
                    'artFam_ref.required' => 'You must enter a reference for your cons family ',
                    'artFam_ref.min' => 'You must enter at least three characters ',
                    'artFam_ref.max' => 'You must enter a maximum of 255 characters',
                    'artFam_ref.string' => 'You must enter a string ',

                    'artFam_design.required' => 'You must enter a designation for your cons family ',
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
        if ($request->artFam_mainRef !== NULL) {
            //we checked if the main reference entered is a real reference for another article family
            $comp = CompFamily::all()->where('compFam_ref', '=', $request->artFam_mainRef);
            $cons = ConsFamily::all()->where('consFam_ref', '=', $request->artFam_mainRef);
            $raw = RawFamily::all()->where('rawFam_ref', '=', $request->artFam_mainRef);
            $cpt = count($comp) + count($cons) + count($raw);
            if ($cpt == 0) {
                return response()->json([
                    'errors' => [
                        'artFam_mainRef' => ["This reference doesn't exist"]
                    ]
                ], 429);
            }
        }
        //we checked if the reference entered is already used for another cons family
        $cons_already_exist = ConsFamily::where('consFam_ref', '=', $request->artFam_ref, 'and')->where('id', '<>', $request->artFam_id)->first();
        if ($cons_already_exist != null) {
            return response()->json([
                'errors' => [
                    'artFam_ref' => ["This reference is already use for another cons family"]
                ]
            ], 429);
        }
    }

    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for insert with the route : /cons/family/add (post)
     * Add a new enregistrement of cons family in the data base with the informations entered in the form
     * @return Response : id of the new cons family
     */
    public function add_consFamily(Request $request)
    {
        $enum = NULL;
        if ($request->artFam_purchasedBy != "" && $request->artFam_purchasedBy != NULL) {
            $enum = EnumPurchasedBy::where('value', '=', $request->artFam_purchasedBy)->first();
            $enum = $enum->id;
        }

        //Creation of a new consFam
        $consFamily = consFamily::create([
            'consFam_ref' => $request->artFam_ref,
            'consFam_design' => $request->artFam_design,
            'consFam_drawingPath' => $request->artFam_drawingPath,
            'enumPurchasedBy_id' => $enum,
            'consFam_validate' => $request->artFam_validate,
            'consFam_version' => $request->artFam_version,
            'consFam_active' => $request->artFam_active,
            'consFam_mainRef' => $request->artFam_mainRef,
        ]);

        $consFamily_id = $consFamily->id;

        return response()->json($consFamily_id);
    }

    public function send_consFamilies()
    {
        $consFamilies = ConsFamily::all();
        $array = [];
        foreach ($consFamilies as $consFamily) {
            $purchaseBy = EnumPurchasedBy::find($consFamily->enumPurchasedBy_id);
            if ($purchaseBy != null) {
                $purchaseBy = $purchaseBy->first()->value;
            } else {
                $purchaseBy = null;
            }
            $qualityApprover = User::find($consFamily->consFam_qualityApproverId);
            if ($qualityApprover != null) {
                $qualityApprover = strtoupper($qualityApprover->user_lastName) . ' ' . $qualityApprover->user_firstName;
            } else {
                $qualityApprover = null;
            }
            $technicalReviewer = User::find($consFamily->consFam_technicalReviewerId);
            if ($technicalReviewer != null) {
                $technicalReviewer = strtoupper($technicalReviewer->user_lastName) . ' ' . $technicalReviewer->user_firstName;
            } else {
                $technicalReviewer = null;
            }
            $obj = [
                'id' => $consFamily->id,
                'consFam_ref' => $consFamily->consFam_ref,
                'consFam_design' => $consFamily->consFam_design,
                'consFam_drawingPath' => $consFamily->consFam_drawingPath,
                'consFam_version' => $consFamily->consFam_version,
                'consFam_nbrVersion' => $consFamily->consFam_nbrVersion,
                'consFam_validate' => $consFamily->consFam_validate,
                'consFam_active' => $consFamily->consFam_active,
                'consFam_purchasedBy' => $purchaseBy,
                'consFam_qualityApproverId' => $consFamily->consFam_qualityApproverId,
                'consFam_qualityApproverName' => $qualityApprover,
                'consFam_technicalReviewerId' => $consFamily->consFam_technicalReviewerId,
                'consFam_technicalReviewerName' => $technicalReviewer,
                'consFam_signatureDate' => $consFamily->consFam_signatureDate,
                'consFam_created_at' => $consFamily->created_at,
                'consFam_updated_at' => $consFamily->updated_at,
                'consFam_mainRef' => $consFamily->consFam_mainRef,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    public function send_consFamily($id)
    {
        $consFamily = ConsFamily::find($id);
        $purchaseBy = EnumPurchasedBy::find($consFamily->enumPurchasedBy_id);
        if ($purchaseBy != null) {
            $purchaseBy = $purchaseBy->first()->value;
        } else {
            $purchaseBy = null;
        }
        $qualityApprover = User::find($consFamily->consFam_qualityApproverId);
        if ($qualityApprover != null) {
            $qualityApprover = strtoupper($qualityApprover->user_lastName) . ' ' . $qualityApprover->user_firstName;
        } else {
            $qualityApprover = null;
        }
        $technicalReviewer = User::find($consFamily->consFam_technicalReviewerId);
        if ($technicalReviewer != null) {
            $technicalReviewer = strtoupper($technicalReviewer->user_lastName) . ' ' . $technicalReviewer->user_firstName;
        } else {
            $technicalReviewer = null;
        }
        $mainDesign = null;
        if ($consFamily->consFam_mainRef !== NULL) {
            $comp = CompFamily::all()->where('compFam_ref', '=', $consFamily->consFam_mainRef);
            $cons = ConsFamily::all()->where('consFam_ref', '=', $consFamily->consFam_mainRef);
            $raw = RawFamily::all()->where('rawFam_ref', '=', $consFamily->consFam_mainRef);
            if (count($comp) !== 0) {
                $mainDesign = $comp->first()->compFam_design;
            } else if (count($cons) !== 0) {
                $mainDesign = $cons->first()->consFam_design;
            } else if (count($raw) !== 0) {
                $mainDesign = $raw->first()->rawFam_design;
            }
        }
        $obj = [
            'id' => $consFamily->id,
            'consFam_ref' => $consFamily->consFam_ref,
            'consFam_design' => $consFamily->consFam_design,
            'consFam_drawingPath' => $consFamily->consFam_drawingPath,
            'consFam_version' => $consFamily->consFam_version,
            'consFam_nbrVersion' => $consFamily->consFam_nbrVersion,
            'consFam_validate' => $consFamily->consFam_validate,
            'consFam_active' => $consFamily->consFam_active,
            'consFam_purchasedBy' => $purchaseBy,
            'consFam_qualityApproverId' => $consFamily->consFam_qualityApproverId,
            'consFam_qualityApproverName' => $qualityApprover,
            'consFam_technicalReviewerId' => $consFamily->consFam_technicalReviewerId,
            'consFam_technicalReviewerName' => $technicalReviewer,
            'consFam_signatureDate' => $consFamily->consFam_signatureDate,
            'consFam_created_at' => $consFamily->created_at,
            'consFam_updated_at' => $consFamily->updated_at,
            'consFam_mainRef' => $consFamily->consFam_mainRef,
            'consFam_mainRefDesign' => $mainDesign,
        ];
        return response()->json($obj);
    }

    public function update_consFamily(Request $request, $id)
    {
        $consFamily = ConsFamily::findOrfail($id);
        if ($consFamily->consFam_signatureDate != null) {
            $consFamily->update([
                'consFam_nbrVersion' => $consFamily->consFam_nbrVersion + 1,
            ]);
        }
        $enum = NULL;
        if ($request->artFam_purchasedBy != "" && $request->artFam_purchasedBy != NULL) {
            $enum = EnumPurchasedBy::where('value', '=', $request->artFam_purchasedBy)->first();
            $enum = $enum->id;
        }
        $consFamily->update([
            'consFam_ref' => $request->artFam_ref,
            'consFam_design' => $request->artFam_design,
            'consFam_drawingPath' => $request->artFam_drawingPath,
            'consFam_version' => $request->artFam_version,
            'consFam_qualityApproverId' => null,
            'consFam_technicalReviewerId' => null,
            'consFam_signatureDate' => null,
            'consFam_validate' => $request->artFam_validate,
            'consFam_active' => $request->artFam_active,
            'enumPurchasedBy_id' => $enum,
            'consFam_mainRef' => $request->artFam_mainRef,
        ]);
        return response()->json($consFamily);
    }

    public function verifValidation_consFamily($id)
    {
        $article = ConsFamily::all()->where('id', '==', $id)->first();
        if ($article->consFam_validate !== 'validated') {
            return response()->json([
                'error' => 'This article family is not validated'
            ], 429);
        }
        $criticalities = Criticality::all()->where('consFam_id', '==', $id);
        foreach ($criticalities as $criticality) {
            if ($criticality->crit_validate !== 'validated') {
                return response()->json([
                    'error' => 'This article family has not validated criticalities'
                ], 429);
            }
        }
        $purSpes = PurchaseSpecification::all()->where('consFam_id', '==', $id);
        foreach ($purSpes as $purSpe) {
            if ($purSpe->purSpe_validate !== 'validated') {
                return response()->json([
                    'error' => 'This article family has not validated purchase specifications'
                ], 429);
            }
        }
        $incmgInsp = IncomingInspection::all()->where('consFam_id', '==', $id);
        foreach ($incmgInsp as $incmg) {
            if ($incmg->incmgInsp_validate !== 'validated') {
                return response()->json([
                    'error' => 'This article family has not validated incoming inspections'
                ], 429);
            }
        }
    }

    public function validate_consFamily(Request $request, $id)
    {
        $consFamily = ConsFamily::findOrfail($id);
        if ($request->reason === 'technical') {
            $consFamily->update([
                'consFam_technicalReviewerId' => $request->user_id,
            ]);
        } else if ($request->reason === 'quality') {
            $consFamily->update([
                'consFam_qualityApproverId' => $request->user_id,
            ]);
        }
        if ($consFamily->consFam_technicalReviewerId != null && $consFamily->consFam_qualityApproverId != null) {
            $consFamily->update([
                'consFam_signatureDate' => Carbon::now(),
            ]);
        }
    }
}

