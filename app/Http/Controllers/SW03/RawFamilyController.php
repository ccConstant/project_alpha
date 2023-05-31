<?php

/*
* Filename : RawFamilyController.php
* Creation date : 28 Apr 2023
* Update date : 28 Apr 2023
* This file is used to link the view files and the database that concern the raw family table.
* For example : add a raw family in the data base, update a raw family...
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

class RawFamilyController extends Controller
{
    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for check data with the route : /raw/family/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_rawFamily(Request $request)
    {

        //-----CASE rawFam->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his rawFam, so he must enter all the attributes
        if ($request->artFam_validate == 'validated') {
            $this->validate(
                $request,
                [
                    'artFam_ref' => 'required|min:3|max:255|string',
                    'artFam_design' => 'required|min:3|max:255|string',
                    'artFam_drawingPath' => 'required|min:3|max:255|string',
                ],
                [

                    'artFam_ref.required' => 'You must enter a reference for your raw family ',
                    'artFam_ref.min' => 'You must enter at least three characters ',
                    'artFam_ref.max' => 'You must enter less than 255 characters ',
                    'artFam_ref.string' => 'You must enter a string ',

                    'artFam_design.required' => 'You must enter a design for your raw family ',
                    'artFam_design.min' => 'You must enter at least three characters ',
                    'artFam_design.max' => 'You must enter less than 255 characters ',
                    'artFam_design.string' => 'You must enter a string ',

                    'artFam_drawingPath.required' => 'You must enter a drawing path for your raw family ',
                    'artFam_drawingPath.min' => 'You must enter at least three characters ',
                    'artFam_drawingPath.max' => 'You must enter less than 255 characters ',
                    'artFam_drawingPath.string' => 'You must enter a string ',
                ]
            );
            if ($request->artFam_purchasedBy == NULL || $request->artFam_purchasedBy == "") {
                return response()->json([
                    'errors' => [
                        'artFam_purchasedBy' => ['You must reference who purchased this raw family'],
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
                ],
                [
                    'artFam_ref.required' => 'You must enter a reference for your raw family ',
                    'artFam_ref.min' => 'You must enter at least three characters ',
                    'artFam_ref.max' => 'You must enter a maximum of 255 characters',
                    'artFam_ref.string' => 'You must enter a string ',

                    'artFam_design.required' => 'You must enter a designation for your raw family ',
                    'artFam_design.min' => 'You must enter at least three characters ',
                    'artFam_design.max' => 'You must enter a maximum of 255 characters',
                    'artFam_design.string' => 'You must enter a string ',

                    'artFam_drawingPath.max' => 'You must enter a maximum of 255 characters',
                    'artFam_drawingPath.string' => 'You must enter a string ',
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
        //we checked if the reference entered is already used for another raw family
        $raw_already_exist = RawFamily::where('rawFam_ref', '=', $request->artFam_ref, 'and')->where('id', '<>', $request->artFam_id)->first();
        if ($raw_already_exist != null) {
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
     * @return Response : id of the new raw family
     */
    public function add_rawFamily(Request $request)
    {
        $enum = NULL;
        if ($request->artFam_purchasedBy != "" && $request->artFam_purchasedBy != NULL) {
            $enum = EnumPurchasedBy::where('value', '=', $request->artFam_purchasedBy)->first();
            $enum = $enum->id;
        }

        //Creation of a new rawFam
        $rawFamily = rawFamily::create([
            'rawFam_ref' => $request->artFam_ref,
            'rawFam_design' => $request->artFam_design,
            'rawFam_drawingPath' => $request->artFam_drawingPath,
            'enumPurchasedBy_id' => $enum,
            'rawFam_validate' => $request->artFam_validate,
            'rawFam_active' => $request->artFam_active,
            'rawFam_mainRef' => $request->artFam_mainRef,
        ]);

        $rawFamily_id = $rawFamily->id;

        return response()->json($rawFamily_id);
    }

    public function send_rawFamilies()
    {
        $rawFamilies = RawFamily::all();
        $array = [];
        foreach ($rawFamilies as $rawFamily) {
            $purchaseBy = EnumPurchasedBy::find($rawFamily->enumPurchasedBy_id);
            if ($purchaseBy != null) {
                $purchaseBy = $purchaseBy->first()->value;
            } else {
                $purchaseBy = null;
            }
            $qualityApprover = User::find($rawFamily->rawFam_qualityApproverId);
            if ($qualityApprover != null) {
                $qualityApprover = strtoupper($qualityApprover->user_lastName) . ' ' . $qualityApprover->user_firstName;
            } else {
                $qualityApprover = null;
            }
            $technicalReviewer = User::find($rawFamily->rawFam_technicalReviewerId);
            if ($technicalReviewer != null) {
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
                'rawFam_created_at' => $rawFamily->created_at,
                'rawFam_updated_at' => $rawFamily->updated_at,
                'rawFam_nbrVersion' => $rawFamily->rawFam_nbrVersion,
                'rawFam_mainRef' => $rawFamily->rawFam_mainRef,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    public function send_rawFamily($id)
    {
        $rawFamily = RawFamily::findOrfail($id);
        $purchaseBy = EnumPurchasedBy::find($rawFamily->enumPurchasedBy_id);
        if ($purchaseBy != null) {
            $purchaseBy = $purchaseBy->first()->value;
        } else {
            $purchaseBy = null;
        }
        $qualityApprover = User::find($rawFamily->rawFam_qualityApproverId);
        if ($qualityApprover != null) {
            $qualityApprover = strtoupper($qualityApprover->user_lastName) . ' ' . $qualityApprover->user_firstName;
        } else {
            $qualityApprover = null;
        }
        $technicalReviewer = User::find($rawFamily->rawFam_technicalReviewerId);
        if ($technicalReviewer != null) {
            $technicalReviewer = strtoupper($technicalReviewer->user_lastName) . ' ' . $technicalReviewer->user_firstName;
        } else {
            $technicalReviewer = null;
        }
        $mainDesign = null;
        if ($rawFamily->rawFam_mainRef !== NULL) {
            $comp = CompFamily::all()->where('compFam_ref', '=', $rawFamily->rawFam_mainRef);
            $cons = ConsFamily::all()->where('consFam_ref', '=', $rawFamily->rawFam_mainRef);
            $raw = RawFamily::all()->where('rawFam_ref', '=', $rawFamily->rawFam_mainRef);
            if (count($comp) !== 0) {
                $mainDesign = $comp->first()->compFam_design;
            } else if (count($cons) !== 0) {
                $mainDesign = $cons->first()->consFam_design;
            } else if (count($raw) !== 0) {
                $mainDesign = $raw->first()->rawFam_design;
            }
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
            'rawFam_created_at' => $rawFamily->created_at,
            'rawFam_updated_at' => $rawFamily->updated_at,
            'rawFam_nbrVersion' => $rawFamily->rawFam_nbrVersion,
            'rawFam_mainRef' => $rawFamily->rawFam_mainRef,
            'rawFam_mainRefDesign' => $mainDesign,
        ];
        return response()->json($obj);
    }

    public function update_rawFamily(Request $request, $id)
    {
        $rawFamily = RawFamily::findOrfail($id);
        if ($rawFamily->rawFam_signatureDate != null) {
            $rawFamily->update([
                'rawFam_nbrVersion' => $rawFamily->rawFam_nbrVersion + 1,
            ]);
        }
        $enum = NULL;
        if ($request->artFam_purchasedBy != "" && $request->artFam_purchasedBy != NULL) {
            $enum = EnumPurchasedBy::where('value', '=', $request->artFam_purchasedBy)->first();
            $enum = $enum->id;
        }
        $rawFamily->update([
            'rawFam_ref' => $request->artFam_ref,
            'rawFam_design' => $request->artFam_design,
            'rawFam_drawingPath' => $request->artFam_drawingPath,
            'rawFam_version' => $request->artFam_version,
            'rawFam_qualityApproverId' => null,
            'rawFam_technicalReviewerId' => null,
            'rawFam_signatureDate' => null,
            'rawFam_validate' => $request->artFam_validate,
            'rawFam_active' => $request->artFam_active,
            'enumPurchasedBy_id' => $enum,
            'rawFam_mainRef' => $request->artFam_mainRef,
        ]);
        return response()->json($rawFamily);
    }

    public function verifValidation_rawFamily($id)
    {
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

    public function validate_rawFamily(Request $request, $id)
    {
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
