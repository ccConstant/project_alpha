<?php

/*
* Filename : AspectTestController.php
* Creation date : 10 Jul 2023
* Update date : 11 Jul 2023
* This file is used to link the view files and the database that concern the aspect test table.
* For example : add an aspect test in the data base, update an aspect test...
*/

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\AspectTest;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\PurchaseSpecification;
use App\Models\SW03\RawFamily;
use Illuminate\Http\Request;

class AspectTestController extends Controller
{

    /**
     * Function call by AspTestIDForm.vue when the form is submitted for check data with the route : /incmgInsp/aspTest/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_aspectTest(Request $request) {
        $this->validate(
            $request,
            [
                'aspTest_expectedAspect' => 'required|string|min:2|max:255',
                'aspTest_name' => 'required|string|min:2|max:255',
                'aspTest_sampling' => 'required',
                'aspTest_specDoc' => 'required|min:2|max:255',
            ],
            [
                'aspTest_expectedAspect.required' => 'You must enter an expected aspect',
                'aspTest_expectedAspect.string' => 'The expected aspect must be a string',
                'aspTest_expectedAspect.min' => 'You must enter at least two characters',
                'aspTest_expectedAspect.max' => 'You must enter a maximum of 255 characters',

                'aspTest_name.required' => 'You must enter a name',
                'aspTest_name.string' => 'The name must be a string',
                'aspTest_name.min' => 'You must enter at least two characters',
                'aspTest_name.max' => 'You must enter a maximum of 255 characters',

                'aspTest_sampling.required' => 'You must enter a sampling',

                'aspTest_specDoc.required' => 'You must enter a specification document',
                'aspTest_specDoc.min' => 'You must enter at least two characters',
                'aspTest_specDoc.max' => 'You must enter a maximum of 255 characters',
            ]
        );
        if ($request->aspTest_sampling === 'Statistics') {
            $this->validate(
                $request,
                [
                    'aspTest_severityLevel' => 'required',
                    'aspTest_levelOfControl' => 'required',
                ],
                [
                    'aspTest_severityLevel.required' => 'You must enter a severity level',

                    'aspTest_levelOfControl.required' => 'You must enter a level of control',
                ]
            );
        }
        if ($request->aspTest_sampling === 'Other') {
            $this->validate(
                $request,
                [
                    'aspTest_desc' => 'required|string|min:2|max:255',
                ],
                [
                    'aspTest_desc.required' => 'You must enter a description',
                    'aspTest_desc.string' => 'The description must be a string',
                    'aspTest_desc.min' => 'You must enter at least two characters',
                    'aspTest_desc.max' => 'You must enter a maximum of 255 characters',
                ]
            );
        }
        /*if ($request->purSpe_id !== null) {
            $purSpe = null;
            if ($request->aspTest_articleType === 'comp') {
                $purSpe = PurchaseSpecification::all()->where('compFam_id', '==', $request->article_id);
            } else if ($request->aspTest_articleType === 'raw') {
                $purSpe = PurchaseSpecification::all()->where('rawFam_id', '==', $request->article_id);
            } else if ($request->aspTest_articleType === 'cons') {
                $purSpe = PurchaseSpecification::all()->where('consFam_id', '==', $request->article_id);
            }
            $val = [];
            foreach ($purSpe as $pur) {
                array_push($val, $pur->id);
            }
            $find = AspectTest::all()->where('aspTest_name', '==', $request->aspTest_name)
                ->whereIn('purSpe_id', $val)
                ->where('id', '<>', $request->id)
                ->count();
            if ($find !== 0) {
                return response()->json([
                    'aspTest_name' => 'This aspect test already exists',
                ], 429);
            }
        }else{
            $insp = null;
            if ($request->aspTest_articleType === 'comp') {
                $insp = IncomingInspection::all()->where('incmgInsp_compFam_id', '==', $request->article_id);
            } else if ($request->aspTest_articleType === 'raw') {
                $insp = IncomingInspection::all()->where('incmgInsp_rawFam_id', '==', $request->article_id);
            } else if ($request->aspTest_articleType === 'cons') {
                $insp = IncomingInspection::all()->where('incmgInsp_consFam_id', '==', $request->article_id);
            }
            $val = [];
            foreach ($insp as $in) {
                array_push($val, $in->id);
            }
            $find = AspectTest::all()->where('aspTest_name', '==', $request->aspTest_name)
                ->whereIn('incmgInsp_id', $val)
                ->where('id', '<>', $request->id)
                ->count();
            if ($find !== 0) {
                return response()->json([
                    'aspTest_name' => 'This aspect test already exists',
                ], 429);
            }
        }*/

    }

    /**
     * Function call by AspTestIDForm.vue when the form is submitted for insert with the route : /incmgInsp/aspTest/add (post)
     * Add a new enregistrement of a aspect control in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new aspect test
     */
    public function add_aspectTest(Request $request) {
        $cons=null;
        $raw=null;
        $comp=null;
        $subComp=null;
        $subCons=null;
        $subRaw=null;
        if ($request->article_id!=null && $request->incmgInsp_id==null){
            if ($request->article_type=='cons'){
                $cons=$request->article_id;
            }
            else if ($request->article_type=='raw'){
                $raw=$request->article_id;
            }
            else if ($request->article_type=='comp'){
                $comp=$request->article_id;
            }
        }else{
            if ($request->incmgInsp_id==null){
                if ($request->article_type=='cons'){
                    $subCons=$request->subFam_id;
                }
                else if ($request->article_type=='raw'){
                    $subRaw=$request->subFam_id;
                }
                else if ($request->article_type=='comp'){
                    $subComp=$request->subFam_id;
                }
            }
        }
        $aspTest = AspectTest::create([
            'aspTest_severityLevel' => $request->aspTest_severityLevel,
            'aspTest_levelOfControl' => $request->aspTest_levelOfControl,
            'aspTest_expectedAspect' => $request->aspTest_expectedAspect,
            'aspTest_name' => $request->aspTest_name,
            'aspTest_sampling' => $request->aspTest_sampling,
            'aspTest_desc' => $request->aspTest_desc,
            'incmgInsp_id' => $request->incmgInsp_id,
            'rawFam_id' => $raw,
            'consFam_id' => $cons,
            'compFam_id' => $comp,
            'rawSubFam_id' => $subRaw,
            'consSubFam_id' => $subCons,
            'compSubFam_id' => $subComp,
            'aspTest_specDoc' => $request->aspTest_specDoc,
        ]);
        return response()->json($aspTest);
    }

    /**
     * Function call by ReferenceAnAspTest.vue with the route : /incmgInsp/aspTest/sendFromIncmgInsp/{id} (get)
     * Get all the aspect test corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_aspectTestFromIncmgInsp($id) {
        $aspTest = AspectTest::all()->where('incmgInsp_id', '==', $id);
        $array = [];
        foreach ($aspTest as $asp) {
            $obj = [
                'id' => $asp->id,
                'aspTest_severityLevel' => $asp->aspTest_severityLevel,
                'aspTest_levelOfControl' => $asp->aspTest_levelOfControl,
                'aspTest_expectedAspect' => $asp->aspTest_expectedAspect,
                'aspTest_name' => $asp->aspTest_name,
                'aspTest_sampling' => $asp->aspTest_sampling,
                'aspTest_desc' => $asp->aspTest_desc,
                'incmgInsp_id' => $asp->incmgInsp_id,
                'aspTest_specDoc' => $asp->aspTest_specDoc,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    /**
     * Function call by ReferenceAnAspTest.vue with the route : /incmgInsp/aspTest/sendFromFamily/{type}/{id} (get)
     * Get all the aspect test corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_aspectTestFromFamily($type,$id) {
        if ($type=="comp"){
            $aspTest = AspectTest::all()->where('compFam_id', "==", $id)->all();
        }
        else if ($type=="raw"){
            $aspTest = AspectTest::all()->where('rawFam_id', "==", $id)->all();
        }
        else if ($type=="cons"){
            $aspTest = AspectTest::all()->where('consFam_id', "==", $id)->all();
        }
        $array = [];
        foreach ($aspTest as $asp) {
            $obj = [
                'id' => $asp->id,
                'aspTest_severityLevel' => $asp->aspTest_severityLevel,
                'aspTest_levelOfControl' => $asp->aspTest_levelOfControl,
                'aspTest_expectedAspect' => $asp->aspTest_expectedAspect,
                'aspTest_name' => $asp->aspTest_name,
                'aspTest_sampling' => $asp->aspTest_sampling,
                'aspTest_desc' => $asp->aspTest_desc,
                'aspTest_specDoc' => $asp->aspTest_specDoc,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    /**
     * Function call by ReferenceAnAspTest.vue with the route : /incmgInsp/aspTest/sendFromFamily/{type}/{id} (get)
     * Get all the aspect test corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_aspectTestFromSubFamily($type,$id) {
        if ($type=="comp"){
            $aspTest = AspectTest::all()->where('compSubFam_id', "==", $id)->all();
        }
        else if ($type=="raw"){
            $aspTest = AspectTest::all()->where('rawSubFam_id', "==", $id)->all();
        }
        else if ($type=="cons"){
            $aspTest = AspectTest::all()->where('consSubFam_id', "==", $id)->all();
        }
        $array = [];
        foreach ($aspTest as $asp) {
            $obj = [
                'id' => $asp->id,
                'aspTest_severityLevel' => $asp->aspTest_severityLevel,
                'aspTest_levelOfControl' => $asp->aspTest_levelOfControl,
                'aspTest_expectedAspect' => $asp->aspTest_expectedAspect,
                'aspTest_name' => $asp->aspTest_name,
                'aspTest_sampling' => $asp->aspTest_sampling,
                'aspTest_desc' => $asp->aspTest_desc,
                'aspTest_specDoc' => $asp->aspTest_specDoc,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    /**
     * Function call by ArticleUpdate.vue when the form is submitted for update with the route : /incmgInsp/aspTest/update/{id} (post)
     * Update an enregistrement of an aspect test in the data base with the informations entered in the form
     * The id parameter correspond to the id of the aspect test we want to update
     * */
    public function update_aspectTest(Request $request, $id) {
        $aspTest = AspectTest::all()->where('id', '==', $id)->first();
        if ($aspTest == null) {
            return response()->json([
                'message' => 'Aspect test not found',
            ], 404);
        };
        if ($aspTest->incmgInsp_id!=null){
            $incmgInsp = IncomingInspection::all()->where('id', '==', $asptest->incmgInsp_id)->first();
            if ($incmgInsp === null) {
                return response()->json([
                    'message' => 'Incoming inspection not found'
                ], 404);
            };
            $incmgInsp->update([
                'incmgInsp_qualityApproverId' => null,
                'incmgInsp_technicalReviewerId' => null,
                'incmgInsp_signatureDate' => null,
            ]);
        }
        if ($asptest->purSpe_id!=null){
            $purSpe = PurchaseSpecification::all()->where('id', '==', $asptest->purSpe_id)->first();
            if ($purSpe === null) {
                return response()->json([
                    'message' => 'purchase spec not found'
                ], 404);
            };
            $purSpe->update([
                'incmgInsp_qualityApproverId' => null,
                'incmgInsp_technicalReviewerId' => null,
                'incmgInsp_signatureDate' => null,
            ]);
        }
        /*$article = null;
        if ($request->aspTest_articleType === 'cons') {
            $article = ConsFamily::all()->where('id', '==', $incmgInsp->incmgInsp_consFam_id)->first();
            $signed = $article->consFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'consFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($request->aspTest_articleType === 'raw') {
            $article = RawFamily::all()->where('id', '==', $incmgInsp->incmgInsp_rawFam_id)->first();
            $signed = $article->rawFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'rawFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($request->aspTest_articleType === 'comp') {
            $article = CompFamily::all()->where('id', '==', $incmgInsp->incmgInsp_compFam_id)->first();
            $signed = $article->compFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'compFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        }
        $article->update([
            $request->aspTest_articleType.'Fam_signatureDate' => null,
            $request->aspTest_articleType.'Fam_qualityApproverId' => null,
            $request->aspTest_articleType.'Fam_technicalReviewerId' => null,
        ]);
        $incmgInsp->update([
            'incmgInsp_qualityApproverId' => null,
            'incmgInsp_technicalReviewerId' => null,
            'incmgInsp_signatureDate' => null,
        ]);*/
        $aspTest->update([
            'aspTest_severityLevel' => $request->aspTest_severityLevel,
            'aspTest_levelOfControl' => $request->aspTest_levelOfControl,
            'aspTest_expectedAspect' => $request->aspTest_expectedAspect,
            'aspTest_name' => $request->aspTest_name,
            'aspTest_sampling' => $request->aspTest_sampling,
            'aspTest_desc' => $request->aspTest_desc,
            'aspTest_specDoc' => $request->aspTest_specDoc,
        ]);
        return response()->json($aspTest);
    }
}
