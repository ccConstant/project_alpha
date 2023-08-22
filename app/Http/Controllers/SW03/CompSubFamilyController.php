<?php

/*
* Filename : CompSubFamilyController.php
* Creation date : 4 Jul 2023
* Update date : 5 Jul 2023
* This file is used to link the view files and the database that concern the comp sub family table.
* For example : add a comp family in the data base, update a comp sub family...
*/

namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SW03\CompSubFamily;
use App\Models\SW03\CompFamily;
use App\Models\SW03\CompFamilyMember;
use Illuminate\Support\Facades\DB;
use App\Models\SW03\EnumPurchasedBy;
use App\Http\Controllers\Controller;
use App\Models\SW03\DimensionalTest;
use App\Models\SW03\FunctionalTest;
use App\Models\SW03\AdminControl;
use App\Models\SW03\AspectTest;
use App\Models\SW03\DocumentaryControl;
use App\Models\SW03\PurchaseSpecification;
use App\Models\SW03\EnumStorageCondition;
use App\Models\SW03\Criticality;
use App\Models\SW03\IncomingInspection;

class CompSubFamilyController extends Controller
{

    /**
     * Function call by ArticleSubFamilyForm.vue when the form is submitted for check data with the route : /comp/subFam/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_compSubFamily(Request $request){

        //-----CASE compSubFam->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his compSubFam, so he must enter all the attributes
        if ($request->artSubFam_validate=='validated'){
            $this->validate(
                $request,
                [
                    'artSubFam_ref' => 'required|max:255 ',
                    'artSubFam_design' => 'required|max:255 ',
                    'artSubFam_drawingPath' => 'required|max:255 ',
                    'artSubFam_version' => 'required|max:255 ',
                    'artSubFam_materials' => 'max:255 ',
                ],
                [

                    'artSubFam_ref.required' => 'You must enter a reference for your comp sub family ',
                    'artSubFam_ref.max' => 'You must enter less than 255 characters ',
                    'artSubFam_ref.string' => 'You must enter a string ',

                    'artSubFam_materials.max' => 'You must enter less than 255 characters ',
                    'artSubFam_materials.string' => 'You must enter a string ',

                    'artSubFam_design.required' => 'You must enter a design for your comp sub family ',
                    'artSubFam_design.max' => 'You must enter less than 255 characters ',
                    'artSubFam_design.string' => 'You must enter a string ',

                    'artSubFam_drawingPath.required' => 'You must enter a drawing path for your comp sub family ',
                    'artSubFam_drawingPath.max' => 'You must enter less than 255 characters ',
                    'artSubFam_drawingPath.string' => 'You must enter a string ',

                    'artSubFam_version.required' => 'You must enter a version for your comp sub family ',
                    'artSubFam_version.max' => 'You must enter less than 255 characters ',
                    'artSubFam_version.string' => 'You must enter a string ',
                ]
            );

            if ($request->artSubFam_purchasedBy==NULL || $request->artSubFam_purchasedBy==""){
                return response()->json([
                    'errors' => [
                        'artSubFam_purchasedBy' => ['You must reference who purchased this comp sub family'],
                    ],
                ], 422);
            }
        }else{
             //-----CASE artSubFam->validate=drafted or artSubFam->validate=to be validated----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations
            $this->validate(
                $request,
                [
                    'artSubFam_ref' => 'required|max:255 ',
                    'artSubFam_design' => 'required|max:255 ',
                    'artSubFam_drawingPath' => 'max:255',
                    'artSubFam_version' => 'max:255',
                    'artSubFam_materials' => 'max:255 ',
                ],
                [
                    'artSubFam_ref.required' => 'You must enter a reference for your comp sub family ',
                    'artSubFam_ref.max' => 'You must enter a maximum of 255 characters',
                    'artSubFam_ref.string' => 'You must enter a string ',

                    'artSubFam_materials.max' => 'You must enter less than 255 characters ',
                    'artSubFam_materials.string' => 'You must enter a string ',

                    'artSubFam_design.required' => 'You must enter a designation for your comp sub family ',
                    'artSubFam_design.max' => 'You must enter a maximum of 255 characters',
                    'artSubFam_design.string' => 'You must enter a string ',

                    'artSubFam_drawingPath.max' => 'You must enter a maximum of 255 characters',
                    'artSubFam_drawingPath.string' => 'You must enter a string ',

                    'artSubFam_version.max' => 'You must enter a maximum of 255 characters',
                    'artSubFam_version.string' => 'You must enter a string ',

                ]
            );
        }
        
        if ($request->reason=="add"){
            //we checked if the reference entered is already used for another component sub family
            $component_already_exist=CompSubFamily::where('compSubFam_ref', '=', $request->artSubFam_ref)->first();
            if ($component_already_exist!=null){
                return response()->json([
                    'errors' => [
                        'artSubFam_ref' => ["This reference is already use for another comp sub family"]
                    ]
                ], 429);
            }
        }else{
            if ($request->reason=="update"){
                //we checked if the reference entered is already used for another component family
                $component_already_exist=CompSubFamily::where('compSubFam_ref', '=', $request->artSubFam_ref, 'and')->where('id', '<>', $request->artSubFam_id)->first();
                if ($component_already_exist!=null){
                    return response()->json([
                        'errors' => [
                            'artSubFam_ref' => ["This reference is already use for another comp sub family"]
                        ]
                    ], 429);
                }
            }
        }
    }

    /**
     * Function call by ArticleSubFamilyForm.vue when the form is submitted for insert with the route : /comp/subFam/add (post)
     * Add a new enregistrement of comp sub family in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new comp sub family
     */
    public function add_compSubFamily(Request $request){
        $enum=NULL;
        if ($request->artSubFam_purchasedBy!="" && $request->artSubFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artSubFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }

        //Creation of a new compSubFam
        $compSubFamily=compSubFamily::create([
            'compSubFam_ref' => $request->artSubFam_ref,
            'compSubFam_design' => $request->artSubFam_design,
            'compSubFam_drawingPath'=> $request->artSubFam_drawingPath,
            'enumPurchasedBy_id' => $enum,
            'compSubFam_materials' => $request->artSubFam_materials,
            'compSubFam_validate' => $request->artSubFam_validate,
            'compSubFam_version' => $request->artSubFam_version,
            'compSubFam_active' => $request->artSubFam_active,
            'compFam_id' => $request->artFam_id,
        ]) ;

        $compSubFamily_id=$compSubFamily->id ;

        return response()->json($compSubFamily_id) ;
    }

    /**
     * Function call by ListOfArticle.vue with the route : /comp/subFam/send (post)
     * Get all the family comp corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_compSubFamilies($id){
        $compSubFamilies = CompSubFamily::where('compFam_id', '=', $id)->get();
        $array = [];
        foreach ($compSubFamilies as $compSubFamily) {
            $purchaseBy = $compSubFamily->purchased_by;
            if ($purchaseBy != null) {
                $purchaseBy = $purchaseBy->value;
            } else {
                $purchaseBy = null;
            }
            $qualityApprover = User::find($compSubFamily->compSubFam_qualityApproverId);
            if ($qualityApprover != null){
                $qualityApprover = strtoupper($qualityApprover->user_lastName) . ' ' . $qualityApprover->user_firstName;
            } else {
                $qualityApprover = null;
            }
            $technicalReviewer = User::find($compSubFamily->compSubFam_technicalReviewerId);
            if ($technicalReviewer != null){
                $technicalReviewer = strtoupper($technicalReviewer->user_lastName) . ' ' . $technicalReviewer->user_firstName;
            } else {
                $technicalReviewer = null;
            }
            $obj = [
                'id' => $compSubFamily->id,
                'reference' => $compSubFamily->compSubFam_ref,
                'designation' => $compSubFamily->compSubFam_design,
                'drawingPath' => $compSubFamily->compSubFam_drawingPath,
                'version' => $compSubFamily->compSubFam_version,
                'nbrVersion' => $compSubFamily->compSubFam_nbrVersion,
                'validate' => $compSubFamily->compSubFam_validate,
                'active' => $compSubFamily->compSubFam_active,
                'purchasedBy' => $purchaseBy,
                'materials' => $compSubFamily->compSubFam_materials,
                'qualityApproverId' => $compSubFamily->compSubFam_qualityApproverId,
                'qualityApproverName' => $qualityApprover,
                'technicalReviewerId' => $compSubFamily->compSubFam_technicalReviewerId,
                'technicalReviewerName' => $technicalReviewer,
                'signatureDate' => $compSubFamily->compSubFam_signatureDate,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    /**
     * Function call by ArticleUpdate.vue when the form is submitted for update with the route :/comp/family/update/{id} (post)
     * Update an enregistrement of comp family in the data base with the informations entered in the form
     * The id parameter correspond to the id of the comp family we want to update
     * */
    public function update_compSubFamily(Request $request, $id) {
        $compSubFamily = CompSubFamily::findOrfail($id);
        if ($compSubFamily->compSubFam_signatureDate != null) {
            $compSubFamily->update([
                'compSubFam_nbrVersion' => $compSubFamily->compSubFam_nbrVersion + 1,
            ]);
        }
        $enum=NULL;
        if ($request->artSubFam_purchasedBy!="" && $request->artSubFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artSubFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }
        $compSubFamily->update([
            'compSubFam_ref' => $request->artSubFam_ref,
            'compSubFam_design' => $request->artSubFam_design,
            'compSubFam_drawingPath' => $request->artSubFam_drawingPath,
            'compSubFam_version' => $request->artSubFam_version,
            'compSubFam_qualityApproverId' => null,
            'compSubFam_technicalReviewerId' => null,
            'compSubFam_materials' => $request->artSubFam_materials,
            'compSubFam_signatureDate' => null,
            'compSubFam_validate' => $request->artSubFam_validate,
            'compSubFam_active' => $request->artSubFam_active,
            'enumPurchasedBy_id' => $enum,
        ]);
        return response()->json($compSubFamily);
    }

    public function delete_compSubFamily($id){
        $compSubFamily = CompSubFamily::all()->where('id', '==', $id)->first();
        $mbs=CompFamilyMember::all()->where('compSubFam_id', '==', $id);
        foreach ($mbs as $mb){
            $mb->delete();
        }
        $incmgs=IncomingInspection::all()->where('compSubFam_id', '==', $id);
        foreach ($incmgs as $incmg){
            $adminTest=AdminControl::all()->where('incmgInsp_id', '==', $incmg->id);
            foreach($adminTest as $adm){
                $adm->delete();
            }
            $aspTest=AspectTest::all()->where('incmgInsp_id', '==', $incmg->id);
            foreach($aspTest as $asp){
                $asp->delete();
            }
            $docControl=DocumentaryControl::all()->where('incmgInsp_id', '==', $incmg->id);
            foreach($docControl as $doc){
                $doc->delete();
            }
            $funcTest=FunctionalTest::all()->where('incmgInsp_id', '==', $incmg->id);
            foreach($funcTest as $func){
                $func->delete();
            }
            $dimTest=DimensionalTest::all()->where('incmgInsp_id', '==', $incmg->id);
            foreach($dimTest as $dim){
                $dim->delete();
            }
            $incmg->delete();
        }
        $purs=PurchaseSpecification::all()->where('compSubFam_id', '==', $id);
        foreach ($purs as $pur){
           $pur->delete();
        }
        $stoConds=$compSubFamily->storage_conditions;
        foreach ($stoConds as $stoCond){
            $enum=EnumStorageCondition::find($stoCond->pivot->storageCondition_id);
            $enum->compSubFamily()->detach($compSubFamily);
        }
        $crits=Criticality::all()->where('compSubFam_id', '==', $id);
        foreach ($crits as $crit){
            $crit->delete();
        }
        $compSubFamily->delete();
    }
}
