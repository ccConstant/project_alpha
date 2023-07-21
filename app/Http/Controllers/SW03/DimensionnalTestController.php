<?php

/*
* Filename : DimensionnalTestController.php
* Creation date : 25 Apr 2023
* Update date : 11 Jul 2023
* This file is used to link the view files and the database that concern the dimensional test table.
* For example : add a dimensional test in the data base, update a dimensional test...
*/


namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\DimensionalTest;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\PurchaseSpecification;
use App\Models\SW03\RawFamily;
use Illuminate\Http\Request;

class DimensionnalTestController extends Controller
{
     /**
     * Function call by DimTestIDForm.vue when the form is submitted for verif with the route : /incmgInsp/funcTest/verif (post)
     * Verify that the informations entered by the user are correct 
     */
    public function verif_dimTest(Request $request) {
        $this->validate(
            $request,
            [
                'dimTest_sampling' => 'required',
            ],
            [
                'dimTest_sampling.required' => 'You must enter a sampling',
            ]
        );
        if ($request->dimTest_sampling === 'Statistics') {
            $this->validate(
                $request,
                [
                    'dimTest_severityLevel' => 'required',
                ],
                [
                    'dimTest_severityLevel.required' => 'You must enter a severity level',
                ]
            );
        }
        if ($request->dimTest_sampling === 'Other') {
            $this->validate(
                $request,
                [
                    'dimTest_desc' => 'required|string|min:2|max:255',
                ],
                [
                    'dimTest_desc.required' => 'You must enter a description',
                    'dimTest_desc.string' => 'The description must be a string',
                    'dimTest_desc.min' => 'You must enter at least two characters',
                    'dimTest_desc.max' => 'You must enter a maximum of 255 characters',
                ]
            );
        }
        if ($request->dimTest_articleType === 'raw' || $request->dimTest_articleType === 'comp') {
            $this->validate(
                $request,
                [
                    'dimTest_expectedMethod' => 'required|string|min:2|max:255',
                    'dimTest_expectedValue' => 'required|string',
                    'dimTest_name' => 'required|string|min:2|max:255',
                ],
                [
                    'dimTest_expectedMethod.required' => 'You must enter an expected method',
                    'dimTest_expectedMethod.string' => 'The expected method must be a string',
                    'dimTest_expectedMethod.min' => 'You must enter at least two characters',
                    'dimTest_expectedMethod.max' => 'You must enter a maximum of 255 characters',

                    'dimTest_expectedValue.required' => 'You must enter an expected value',
                    'dimTest_expectedValue.string' => 'The expected value must be a string',

                    'dimTest_name.required' => 'You must enter a name of control',
                    'dimTest_name.string' => 'The name of control must be a string',
                    'dimTest_name.min' => 'You must enter at least two characters',
                    'dimTest_name.max' => 'You must enter a maximum of 255 characters',
                ]
            );
        }
        /*if ($request->purSpe_id !== null) {
            $purSpe = null;
            if ($request->dimTest_articleType === 'comp') {
                $purSpe = PurchaseSpecification::all()->where('compFam_id', '==', $request->article_id);
            } else if ($request->dimTest_articleType === 'raw') {
                $purSpe = PurchaseSpecification::all()->where('rawFam_id', '==', $request->article_id);
            } else if ($request->dimTest_articleType === 'cons') {
                $purSpe = PurchaseSpecification::all()->where('consFam_id', '==', $request->article_id);
            }
            $val = [];
            foreach ($purSpe as $pur) {
                array_push($val, $pur->id);
            }
            $find = DimensionalTest::all()->where('dimTest_name', '==', $request->dimTest_name)
                ->whereIn('purSpe_id', $val)
                ->where('id', '<>', $request->id)
                ->count();
            if ($find !== 0) {
                return response()->json([
                    'dimTest_name' => 'This aspect test already exists',
                ], 429);
            }
        }else{
            $insp = null;
            if ($request->dimTest_articleType === 'comp') {
                $insp = IncomingInspection::all()->where('incmgInsp_compFam_id', '==', $request->article_id);
            } else if ($request->dimTest_articleType === 'raw') {
                $insp = IncomingInspection::all()->where('incmgInsp_rawFam_id', '==', $request->article_id);
            } else if ($request->dimTest_articleType === 'cons') {
                $insp = IncomingInspection::all()->where('incmgInsp_consFam_id', '==', $request->article_id);
            }
            $val = [];
            foreach ($insp as $in) {
                array_push($val, $in->id);
            }
            $find = DimensionalTest::all()->where('dimTest_name', '==', $request->dimTest_name)
                ->whereIn('incmgInsp_id', $val)
                ->where('id', '<>', $request->id)
                ->count();
            if ($find !== 0) {
                return response()->json([
                    'dimTest_name' => 'This dimensional test already exists',
                ], 429);
            }
        }*/
    
    }

    /**
     * Function call by FuncTestIdForm.vue when the form is submitted for insert with the route : /incmgInsp/dimTest/add (post)
     * Add a new enregistrement of dimensional test in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new dimensional test
     */
    public function add_dimTest(Request $request) {
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
        $dimTest = DimensionalTest::create([
            'dimTest_sampling' => $request->dimTest_sampling,
            'dimTest_severityLevel' => $request->dimTest_severityLevel,
            'dimTest_expectedMethod' => $request->dimTest_expectedMethod,
            'dimTest_expectedValue' => $request->dimTest_expectedValue,
            'dimTest_name' => $request->dimTest_name,
            'incmgInsp_id' => $request->incmgInsp_id,
            'rawFam_id' => $raw,
            'consFam_id' => $cons,
            'compFam_id' => $comp,
            'rawSubFam_id' => $subRaw,
            'consSubFam_id' => $subCons,
            'compSubFam_id' => $subComp,
            'dimTest_desc' => $request->dimTest_desc,
        ]);
        return response()->json($dimTest);
    }

    /**
     * Function call by ListOfArticle.vue when the form is submitted with the route /incmgInsp/dimTest/sendFromIncmgInsp/{id} (get)
     * Get all the dimensional test of the incoming inspection with the id in parameter
     * @return \Illuminate\Http\Response
     */
    public function send_dimTestFromIncmgInsp($id) {
        $dimTest = DimensionalTest::all()->where('incmgInsp_id', $id);
        $array = [];
        foreach ($dimTest as $item) {
            $obj = [
                'id' => $item->id,
                'dimTest_sampling' => $item->dimTest_sampling,
                'dimTest_severityLevel' => $item->dimTest_severityLevel,
                'dimTest_expectedMethod' => $item->dimTest_expectedMethod,
                'dimTest_expectedValue' => $item->dimTest_expectedValue,
                'dimTest_name' => $item->dimTest_name,
                'incmgInsp_id' => $item->incmgInsp_id,
                'dimTest_desc' => $item->dimTest_desc,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    /**
     * Function call by ReferenceAnAspTest.vue with the route : /incmgInsp/adminControl/sendFromFamily/{type}/{id} (get)
     * Get all the aspect test corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_dimTestFromFamily($type,$id) {
        if ($type=="comp"){
            $dimTest=DimensionalTest::all()->where('compFam_id', "==", $id)->all();
        }
        else if ($type=="raw"){
            $dimTest=DimensionalTest::all()->where('rawFam_id', "==", $id)->all();
        }
        else if ($type=="cons"){
            $dimTest=DimensionalTest::all()->where('consFam_id', "==", $id)->all();
        }
        $array = [];
        foreach ($dimTest as $item) {
            $obj = [
                'id' => $item->id,
                'dimTest_sampling' => $item->dimTest_sampling,
                'dimTest_severityLevel' => $item->dimTest_severityLevel,
                'dimTest_expectedMethod' => $item->dimTest_expectedMethod,
                'dimTest_expectedValue' => $item->dimTest_expectedValue,
                'dimTest_name' => $item->dimTest_name,
                'incmgInsp_id' => $item->incmgInsp_id,
                'dimTest_desc' => $item->dimTest_desc,
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
    public function send_dimTestFromSubFamily($type,$id) {
        if ($type=="comp"){
            $dimTest=DimensionalTest::all()->where('compSubFam_id', "==", $id)->all();
        }
        else if ($type=="raw"){
            $dimTest=DimensionalTest::all()->where('rawSubFam_id', "==", $id)->all();
        }
        else if ($type=="cons"){
            $dimTest=DimensionalTest::all()->where('consSubFam_id', "==", $id)->all();
        }
        $array = [];
        foreach ($dimTest as $item) {
            $obj = [
                'id' => $item->id,
                'dimTest_sampling' => $item->dimTest_sampling,
                'dimTest_severityLevel' => $item->dimTest_severityLevel,
                'dimTest_expectedMethod' => $item->dimTest_expectedMethod,
                'dimTest_expectedValue' => $item->dimTest_expectedValue,
                'dimTest_name' => $item->dimTest_name,
                'dimTest_desc' => $item->dimTest_desc,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    /**
     * Function call by ArticleUpdate.vue when the form is submitted with the route /incmgInsp/dimTest/update/{id} (get)
     * Update an enregistrement of dimensional test in the data base with the informations entered in the form
     * The id parameter correspond to the id of the dimensional test we want to update
     */
    public function update_dimTest(Request $request, $id) {
        $dimTest = DimensionalTest::all()->where('id', '==', $id)->first();
        if ($dimTest == null) {
            return response()->json([
                'message' => 'Dimensional test not found',
            ], 404);
        };
        $incmgInsp = IncomingInspection::all()->where('id', '==', $dimTest->incmgInsp_id)->first();
        if ($incmgInsp == null) {
            return response()->json([
                'message' => 'Incoming inspection not found',
            ], 404);
        };
        /*$article = null;
        if ($request->dimTest_articleType === 'cons') {
            $article = ConsFamily::all()->where('id', '==', $incmgInsp->incmgInsp_consFam_id)->first();
            $signed = $article->consFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'consFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($request->dimTest_articleType === 'raw') {
            $article = RawFamily::all()->where('id', '==', $incmgInsp->incmgInsp_rawFam_id)->first();
            $signed = $article->rawFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'rawFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($request->dimTest_articleType === 'comp') {
            $article = CompFamily::all()->where('id', '==', $incmgInsp->incmgInsp_compFam_id)->first();
            $signed = $article->compFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'compFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        }
        $article->update([
            $request->dimTest_articleType.'Fam_signatureDate' => null,
            $request->dimTest_articleType.'Fam_qualityApproverId' => null,
            $request->dimTest_articleType.'Fam_technicalReviewerId' => null,
        ]);*/
        $incmgInsp->update([
            'incmgInsp_qualityApproverId' => null,
            'incmgInsp_technicalReviewerId' => null,
            'incmgInsp_signatureDate' => null,
        ]);
        $dimTest->update([
            'dimTest_sampling' => $request->dimTest_sampling,
            'dimTest_severityLevel' => $request->dimTest_severityLevel,
            'dimTest_expectedMethod' => $request->dimTest_expectedMethod,
            'dimTest_expectedValue' => $request->dimTest_expectedValue,
            'dimTest_name' => $request->dimTest_name,
            'incmgInsp_id' => $request->incmgInsp_id,
            'dimTest_desc' => $request->dimTest_desc,
        ]);
        return response()->json($dimTest);
    }

    /**
     * Function call by DimTestIDForm.vue when we want to delete a doc control with the route : /incmgInsp/delete/dimTest/{id}(post)
     * Delete a dim test thanks to the id given in parameter
     * The id parameter correspond to the id of the dim test we want to delete
     * */
    public function delete_dimTest($id){
        $dim=DimensionalTest::findOrFail($id);
        $dim->delete();
    }
}
