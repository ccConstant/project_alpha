<?php

/*
* Filename : ConsSubFamilyController.php
* Creation date : 4 Jul 2023
* Update date : 4 Jul 2023
* This file is used to link the view files and the database that concern the cons sub family table.
* For example : add a cons family in the data base, update a cons sub family...
*/

namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SW03\ConsSubFamily;
use App\Models\SW03\ConsFamily;
use Illuminate\Support\Facades\DB;
use App\Models\SW03\EnumPurchasedBy;
use App\Http\Controllers\Controller;

class ConsSubFamilyController extends Controller
{
    /**
     * Function call by ArticleSubFamilyForm.vue when the form is submitted for check data with the route : /cons/subFam/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_consSubFamily(Request $request){

        //-----CASE consSubFam->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his consSubFam, so he must enter all the attributes
        if ($request->artSubFam_validate=='validated'){
            $this->validate(
                $request,
                [
                    'artSubFam_ref' => 'required|min:3|max:255|string',
                    'artSubFam_design' => 'required|min:3|max:255|string',
                    'artSubFam_drawingPath' => 'required|min:3|max:255|string',
                    'artSubFam_version' => 'required|min:2|max:4|string',
                ],
                [

                    'artSubFam_ref.required' => 'You must enter a reference for your cons sub family ',
                    'artSubFam_ref.min' => 'You must enter at least three characters ',
                    'artSubFam_ref.max' => 'You must enter less than 255 characters ',
                    'artSubFam_ref.string' => 'You must enter a string ',

                    'artSubFam_design.required' => 'You must enter a design for your cons sub family ',
                    'artSubFam_design.min' => 'You must enter at least three characters ',
                    'artSubFam_design.max' => 'You must enter less than 255 characters ',
                    'artSubFam_design.string' => 'You must enter a string ',

                    'artSubFam_drawingPath.required' => 'You must enter a drawing path for your cons sub family ',
                    'artSubFam_drawingPath.min' => 'You must enter at least three characters ',
                    'artSubFam_drawingPath.max' => 'You must enter less than 255 characters ',
                    'artSubFam_drawingPath.string' => 'You must enter a string ',

                    'artSubFam_version.required' => 'You must enter a version for your cons sub family ',
                    'artSubFam_version.min' => 'You must enter at least two characters ',
                    'artSubFam_version.max' => 'You must enter less than 4 characters ',
                    'artSubFam_version.string' => 'You must enter a string ',
                ]
            );

            if ($request->artSubFam_purchasedBy==NULL || $request->artSubFam_purchasedBy==""){
                return response()->json([
                    'errors' => [
                        'artSubFam_purchasedBy' => ['You must reference who purchased this cons sub family'],
                    ],
                ], 422);
            }
        }else{
             //-----CASE artSubFam->validate=drafted or artSubFam->validate=to be validated----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations
            $this->validate(
                $request,
                [
                    'artSubFam_ref' => 'required|min:3|max:255|string',
                    'artSubFam_design' => 'required|min:3|max:255|string',
                    'artSubFam_drawingPath' => 'max:255',
                    'artSubFam_version' => 'max:4',
                ],
                [
                    'artSubFam_ref.required' => 'You must enter a reference for your cons sub family ',
                    'artSubFam_ref.min' => 'You must enter at least three characters ',
                    'artSubFam_ref.max' => 'You must enter a maximum of 255 characters',
                    'artSubFam_ref.string' => 'You must enter a string ',

                    'artSubFam_design.required' => 'You must enter a designation for your cons sub family ',
                    'artSubFam_design.min' => 'You must enter at least three characters ',
                    'artSubFam_design.max' => 'You must enter a maximum of 255 characters',
                    'artSubFam_design.string' => 'You must enter a string ',

                    'artSubFam_drawingPath.max' => 'You must enter a maximum of 255 characters',
                    'artSubFam_drawingPath.string' => 'You must enter a string ',

                    'artSubFam_version.max' => 'You must enter a maximum of 4 characters',
                    'artSubFam_version.string' => 'You must enter a string ',

                ]
            );
        }

    if ($request->reason=="add"){
        //we checked if the reference entered is already used for another cons sub family
        $component_already_exist=ConsSubFamily::where('consSubFam_ref', '=', $request->artSubFam_ref)->first();
        if ($component_already_exist!=null){
            return response()->json([
                'errors' => [
                    'artSubFam_ref' => ["This reference is already use for another cons sub family"]
                ]
            ], 429);
        }
    }else{
        if ($request->reason=="update"){
            //we checked if the reference entered is already used for another cons family
            $component_already_exist=ConsSubFamily::where('consSubFam_ref', '=', $request->artSubFam_ref, 'and')->where('id', '<>', $request->artSubFam_id)->first();
            if ($component_already_exist!=null){
                return response()->json([
                    'errors' => [
                        'artSubFam_ref' => ["This reference is already use for another cons sub family"]
                    ]
                ], 429);
            }
        }
    }

         
    }

    /**
     * Function call by ArticleSubFamilyForm.vue when the form is submitted for insert with the route : /cons/subFam/add (post)
     * Add a new enregistrement of cons sub family in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new cons sub family
     */
    public function add_consSubFamily(Request $request){
        $enum=NULL;
        if ($request->artSubFam_purchasedBy!="" && $request->artSubFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artSubFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }

        //Creation of a new consSubFam
        $consSubFamily=consSubFamily::create([
            'consSubFam_ref' => $request->artSubFam_ref,
            'consSubFam_design' => $request->artSubFam_design,
            'consSubFam_drawingPath'=> $request->artSubFam_drawingPath,
            'enumPurchasedBy_id' => $enum,
            'consSubFam_validate' => $request->artSubFam_validate,
            'consSubFam_version' => $request->artSubFam_version,
            'consSubFam_active' => $request->artSubFam_active,
            'consFam_id' => $request->artFam_id,
        ]) ;

        $consSubFamily_id=$consSubFamily->id ;

        return response()->json($consSubFamily_id) ;
    }

      /**
     * Function call by ListOfArticle.vue when the form is submitted for insert with the route : /cons/subFam/send (post)
     * Get all the family cons corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_consSubFamilies($id){
        $consSubFamilies = ConsSubFamily::where('consFam_id', '=', $id)->get();
        $array = [];
        foreach ($consSubFamilies as $consSubFamily) {
            $purchaseBy = EnumPurchasedBy::find($consSubFamily->enumPurchasedBy_id);
            if ($purchaseBy != null) {
                $purchaseBy = $purchaseBy->first()->value;
            } else {
                $purchaseBy = null;
            }
            $qualityApprover = User::find($consSubFamily->consSubFam_qualityApproverId);
            if ($qualityApprover != null){
                $qualityApprover = strtoupper($qualityApprover->user_lastName) . ' ' . $qualityApprover->user_firstName;
            } else {
                $qualityApprover = null;
            }
            $technicalReviewer = User::find($consSubFamily->consSubFam_technicalReviewerId);
            if ($technicalReviewer != null){
                $technicalReviewer = strtoupper($technicalReviewer->user_lastName) . ' ' . $technicalReviewer->user_firstName;
            } else {
                $technicalReviewer = null;
            }
            $obj = [
                'id' => $consSubFamily->id,
                'reference' => $consSubFamily->consSubFam_ref,
                'designation' => $consSubFamily->consSubFam_design,
                'drawingPath' => $consSubFamily->consSubFam_drawingPath,
                'version' => $consSubFamily->consSubFam_version,
                'nbrVersion' => $consSubFamily->consSubFam_nbrVersion,
                'validate' => $consSubFamily->consSubFam_validate,
                'active' => $consSubFamily->consSubFam_active,
                'purchasedBy' => $purchaseBy,
                'qualityApproverId' => $consSubFamily->consSubFam_qualityApproverId,
                'qualityApproverName' => $qualityApprover,
                'technicalReviewerId' => $consSubFamily->consSubFam_technicalReviewerId,
                'technicalReviewerName' => $technicalReviewer,
                'signatureDate' => $consSubFamily->consSubFam_signatureDate,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    /**
     * Function call by ArticleUpdate.vue when the form is submitted for update with the route :/cons/family/update/{id} (post)
     * Update an enregistrement of cons family in the data base with the informations entered in the form
     * The id parameter correspond to the id of the cons family we want to update
     * */
    public function update_consSubFamily(Request $request, $id) {
        $consSubFamily = ConsSubFamily::findOrfail($id);
        if ($consSubFamily->consSubFam_signatureDate != null) {
            $consSubFamily->update([
                'consSubFam_nbrVersion' => $consSubFamily->consSubFam_nbrVersion + 1,
            ]);
        }
        $enum=NULL;
        if ($request->artSubFam_purchasedBy!="" && $request->artSubFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artSubFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }
        $consSubFamily->update([
            'consSubFam_ref' => $request->artSubFam_ref,
            'consSubFam_design' => $request->artSubFam_design,
            'consSubFam_drawingPath' => $request->artSubFam_drawingPath,
            'consSubFam_version' => $request->artSubFam_version,
            'consSubFam_qualityApproverId' => null,
            'consSubFam_technicalReviewerId' => null,
            'consSubFam_signatureDate' => null,
            'consSubFam_validate' => $request->artSubFam_validate,
            'consSubFam_active' => $request->artSubFam_active,
            'enumPurchasedBy_id' => $enum,
        ]);
        return response()->json($consSubFamily);
    }
}
