<?php

/*
* Filename : RawSubFamilyController.php
* Creation date : 4 Jul 2023
* Update date : 4 Jul 2023
* This file is used to link the view files and the database that concern the raw sub family table.
* For example : add a raw family in the data base, update a raw sub family...
*/

namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SW03\RawSubFamily;
use App\Models\SW03\RawFamily;
use Illuminate\Support\Facades\DB;
use App\Models\SW03\EnumPurchasedBy;
use App\Http\Controllers\Controller;

class RawSubFamilyController extends Controller
{
    /**
     * Function call by ArticleSubFamilyForm.vue when the form is submitted for check data with the route : /raw/subFam/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_rawSubFamily(Request $request){

        //-----CASE rawSubFam->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his rawSubFam, so he must enter all the attributes
        if ($request->artSubFam_validate=='validated'){
            $this->validate(
                $request,
                [
                    'artSubFam_ref' => 'required|max:255|string',
                    'artSubFam_design' => 'required|max:255|string',
                    'artSubFam_drawingPath' => 'required|max:255|string',
                ],
                [

                    'artSubFam_ref.required' => 'You must enter a reference for your raw sub family ',
                    'artSubFam_ref.max' => 'You must enter less than 255 characters ',
                    'artSubFam_ref.string' => 'You must enter a string ',

                    'artSubFam_design.required' => 'You must enter a design for your raw sub family ',
                    'artSubFam_design.max' => 'You must enter less than 255 characters ',
                    'artSubFam_design.string' => 'You must enter a string ',

                    'artSubFam_drawingPath.required' => 'You must enter a drawing path for your raw sub family ',
                    'artSubFam_drawingPath.max' => 'You must enter less than 255 characters ',
                    'artSubFam_drawingPath.string' => 'You must enter a string ',
                ]
            );

            if ($request->artSubFam_purchasedBy==NULL || $request->artSubFam_purchasedBy==""){
                return response()->json([
                    'errors' => [
                        'artSubFam_purchasedBy' => ['You must reference who purchased this raw sub family'],
                    ],
                ], 422);
            }
        }else{
             //-----CASE artSubFam->validate=drafted or artSubFam->validate=to be validated----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations
            $this->validate(
                $request,
                [
                    'artSubFam_ref' => 'required|max:255|string',
                    'artSubFam_design' => 'required|max:255|string',
                    'artSubFam_drawingPath' => 'max:255',
                ],
                [
                    'artSubFam_ref.required' => 'You must enter a reference for your raw sub family ',
                    'artSubFam_ref.max' => 'You must enter a maximum of 255 characters',
                    'artSubFam_ref.string' => 'You must enter a string ',

                    'artSubFam_design.required' => 'You must enter a designation for your raw sub family ',
                    'artSubFam_design.max' => 'You must enter a maximum of 255 characters',
                    'artSubFam_design.string' => 'You must enter a string ',

                    'artSubFam_drawingPath.max' => 'You must enter a maximum of 255 characters',
                    'artSubFam_drawingPath.string' => 'You must enter a string ',

                ]
            );
        }

    if ($request->reason=="add"){
        //we checked if the reference entered is already used for another raw sub family
        $component_already_exist=RawSubFamily::where('rawSubFam_ref', '=', $request->artSubFam_ref)->first();
        if ($component_already_exist!=null){
            return response()->json([
                'errors' => [
                    'artSubFam_ref' => ["This reference is already use for another raw sub family"]
                ]
            ], 429);
        }
    }else{
        if ($request->reason=="update"){
            //we checked if the reference entered is already used for another raw family
            $component_already_exist=RawSubFamily::where('rawSubFam_ref', '=', $request->artSubFam_ref, 'and')->where('id', '<>', $request->artSubFam_id)->first();
            if ($component_already_exist!=null){
                return response()->json([
                    'errors' => [
                        'artSubFam_ref' => ["This reference is already use for another raw sub family"]
                    ]
                ], 429);
            }
        }
    }

         
    }

    /**
     * Function call by ArticleSubFamilyForm.vue when the form is submitted for insert with the route : /raw/subFam/add (post)
     * Add a new enregistrement of raw sub family in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new raw sub family
     */
    public function add_rawSubFamily(Request $request){
        $enum=NULL;
        if ($request->artSubFam_purchasedBy!="" && $request->artSubFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artSubFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }

        //Creation of a new rawSubFam
        $rawSubFamily=rawSubFamily::create([
            'rawSubFam_ref' => $request->artSubFam_ref,
            'rawSubFam_design' => $request->artSubFam_design,
            'rawSubFam_drawingPath'=> $request->artSubFam_drawingPath,
            'enumPurchasedBy_id' => $enum,
            'rawSubFam_validate' => $request->artSubFam_validate,
            'rawSubFam_active' => $request->artSubFam_active,
            'rawFam_id' => $request->artFam_id,
        ]) ;

        $rawSubFamily_id=$rawSubFamily->id ;

        return response()->json($rawSubFamily_id) ;
    }

      /**
     * Function call by ListOfArticle.vue when the form is submitted for insert with the route : /raw/subFam/send (post)
     * Get all the family raw corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_rawSubFamilies($id){
        $rawSubFamilies = RawSubFamily::where('rawFam_id', '=', $id)->get();
        $array = [];
        foreach ($rawSubFamilies as $rawSubFamily) {
            $purchaseBy = EnumPurchasedBy::find($rawSubFamily->enumPurchasedBy_id);
            if ($purchaseBy != null) {
                $purchaseBy = $purchaseBy->first()->value;
            } else {
                $purchaseBy = null;
            }
            $qualityApprover = User::find($rawSubFamily->rawSubFam_qualityApproverId);
            if ($qualityApprover != null){
                $qualityApprover = strtoupper($qualityApprover->user_lastName) . ' ' . $qualityApprover->user_firstName;
            } else {
                $qualityApprover = null;
            }
            $technicalReviewer = User::find($rawSubFamily->rawSubFam_technicalReviewerId);
            if ($technicalReviewer != null){
                $technicalReviewer = strtoupper($technicalReviewer->user_lastName) . ' ' . $technicalReviewer->user_firstName;
            } else {
                $technicalReviewer = null;
            }
            $obj = [
                'id' => $rawSubFamily->id,
                'reference' => $rawSubFamily->rawSubFam_ref,
                'designation' => $rawSubFamily->rawSubFam_design,
                'drawingPath' => $rawSubFamily->rawSubFam_drawingPath,
                'nbrVersion' => $rawSubFamily->rawSubFam_nbrVersion,
                'validate' => $rawSubFamily->rawSubFam_validate,
                'active' => $rawSubFamily->rawSubFam_active,
                'purchasedBy' => $purchaseBy,
                'qualityApproverId' => $rawSubFamily->rawSubFam_qualityApproverId,
                'qualityApproverName' => $qualityApprover,
                'technicalReviewerId' => $rawSubFamily->rawSubFam_technicalReviewerId,
                'technicalReviewerName' => $technicalReviewer,
                'signatureDate' => $rawSubFamily->rawSubFam_signatureDate,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    /**
     * Function call by ArticleUpdate.vue when the form is submitted for update with the route :/raw/family/update/{id} (post)
     * Update an enregistrement of raw family in the data base with the informations entered in the form
     * The id parameter correspond to the id of the raw family we want to update
     * */
    public function update_rawSubFamily(Request $request, $id) {
        $rawSubFamily = RawSubFamily::findOrfail($id);
        if ($rawSubFamily->rawSubFam_signatureDate != null) {
            $rawSubFamily->update([
                'rawSubFam_nbrVersion' => $rawSubFamily->rawSubFam_nbrVersion + 1,
            ]);
        }
        $enum=NULL;
        if ($request->artSubFam_purchasedBy!="" && $request->artSubFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artSubFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }
        $rawSubFamily->update([
            'rawSubFam_ref' => $request->artSubFam_ref,
            'rawSubFam_design' => $request->artSubFam_design,
            'rawSubFam_drawingPath' => $request->artSubFam_drawingPath,
            'rawSubFam_qualityApproverId' => null,
            'rawSubFam_technicalReviewerId' => null,
            'rawSubFam_signatureDate' => null,
            'rawSubFam_validate' => $request->artSubFam_validate,
            'rawSubFam_active' => $request->artSubFam_active,
            'enumPurchasedBy_id' => $enum,
        ]);
        return response()->json($rawSubFamily->id);
    }
}
