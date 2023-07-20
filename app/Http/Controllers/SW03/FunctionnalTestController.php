<?php

/*
* Filename : FunctionnalTestController.php
* Creation date : 25 Apr 2023
* Update date : 10 Jul 2023
* This file is used to link the view files and the database that concern the functionnal test table.
* For example : add a functionnal test in the data base, update a functionnal test...
*/

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\FunctionalTest;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\PurchaseSpecification;
use App\Models\SW03\RawFamily;
use Illuminate\Http\Request;

class FunctionnalTestController extends Controller
{
    /**
     * Function call by FuncTestIdForm.vue when the form is submitted for verif with the route : /incmgInsp/dimTest/verif (post)
     * Verify that the informations entered by the user are correct 
     */
    public function verif_funcTest(Request $request) {
        $this->validate(
            $request,
            [
                'funcTest_sampling' => 'required',
            ],
            [
                'funcTest_sampling.required' => 'You must enter a sampling',

            ]
        );
        if ($request->funcTest_sampling === 'Statistics') {
            $this->validate(
                $request,
                [
                    'funcTest_severityLevel' => 'required',
                    'funcTest_levelOfControl' => 'required',
                ],
                [
                    'funcTest_severityLevel.required' => 'You must enter a severity level',

                    'funcTest_levelOfControl.required' => 'You must enter a level of control',
                ]
            );
        }
        if ($request->funcTest_sampling === 'Other') {
            $this->validate(
                $request,
                [
                    'funcTest_desc' => 'required|string|min:2|max:255',
                ],
                [
                    'funcTest_desc.required' => 'You must enter a description',
                    'funcTest_desc.string' => 'The description must be a string',
                    'funcTest_desc.min' => 'You must enter at least two characters',
                    'funcTest_desc.max' => 'You must enter a maximum of 255 characters',
                ]
            );
        }
        if ($request->funcTest_articleType === 'comp') {
            $this->validate(
                $request,
                [
                    'funcTest_expectedMethod' => 'required|string|min:2|max:255',
                    'funcTest_expectedValue' => 'required|string|min:1|max:50',
                    'funcTest_name' => 'required|string|min:2|max:255',
                    'funcTest_specDoc' => 'required|string|min:2|max:255',
                ],
                [
                    'funcTest_expectedMethod.required' => 'You must enter an expected method',
                    'funcTest_expectedMethod.string' => 'The expected method must be a string',
                    'funcTest_expectedMethod.min' => 'You must enter at least two characters',
                    'funcTest_expectedMethod.max' => 'You must enter a maximum of 255 characters',

                    'funcTest_expectedValue.required' => 'You must enter an expected value',
                    'funcTest_expectedValue.string' => 'The expected value must be a string',
                    'funcTest_expectedValue.min' => 'You must enter at least one character',
                    'funcTest_expectedValue.max' => 'You must enter a maximum of 50 characters',

                    'funcTest_name.required' => 'You must enter a name',
                    'funcTest_name.string' => 'The name must be a string',
                    'funcTest_name.min' => 'You must enter at least two characters',
                    'funcTest_name.max' => 'You must enter a maximum of 255 characters',

                    'funcTest_specDoc.required' => 'You must enter a specification document',
                    'funcTest_specDoc.string' => 'The specification document must be a string',
                    'funcTest_specDoc.min' => 'You must enter at least two characters',
                    'funcTest_specDoc.max' => 'You must enter a maximum of 255 characters',
                ]
            );
        }
        /*if ($request->purSpe_id !== null) {
            $purSpe = null;
            if ($request->funcTest_articleType === 'comp') {
                $purSpe = PurchaseSpecification::all()->where('compFam_id', '==', $request->article_id);
            } else if ($request->funcTest_articleType === 'raw') {
                $purSpe = PurchaseSpecification::all()->where('rawFam_id', '==', $request->article_id);
            } else if ($request->funcTest_articleType === 'cons') {
                $purSpe = PurchaseSpecification::all()->where('consFam_id', '==', $request->article_id);
            }
            $val = [];
            foreach ($purSpe as $pur) {
                array_push($val, $pur->id);
            }
            $find = FunctionalTest::all()->where('funcTest_name', '==', $request->funcTest_name)
                ->whereIn('purSpe_id', $val)
                ->where('id', '<>', $request->id)
                ->count();
            if ($find !== 0) {
                return response()->json([
                    'funcTest_name' => 'This aspect test already exists',
                ], 429);
            }
        }else{
            $insp = null;
            if ($request->funcTest_articleType === 'comp') {
                $insp = IncomingInspection::all()->where('incmgInsp_compFam_id', '==', $request->article_id);
            } else if ($request->funcTest_articleType === 'raw') {
                $insp = IncomingInspection::all()->where('incmgInsp_rawFam_id', '==', $request->article_id);
            } else if ($request->funcTest_articleType === 'cons') {
                $insp = IncomingInspection::all()->where('incmgInsp_consFam_id', '==', $request->article_id);
            }
            $val = [];
            foreach ($insp as $in) {
                array_push($val, $in->id);
            }
            $find = FunctionalTest::all()->where('funcTest_name', '==', $request->funcTest_name)
                ->whereIn('incmgInsp_id', $val)
                ->where('id', '<>', $request->id)
                ->count();
            if ($find !== 0) {
                return response()->json([
                    'funcTest_name' => 'This functional test already exists',
                ], 429);
            }
        }*/
        
    }

    /**
     * Function call by FuncTestIdForm.vue when the form is submitted for insert with the route : /incmgInsp/funcTest/add (post)
     * Add a new enregistrement of Functional test in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new Functional test
     */
    public function add_funcTest(Request $request) {
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
        $funcTest = FunctionalTest::create([
            'funcTest_severityLevel' => $request->funcTest_severityLevel,
            'funcTest_levelOfControl' => $request->funcTest_levelOfControl,
            'funcTest_expectedMethod' => $request->funcTest_expectedMethod,
            'funcTest_expectedValue' => $request->funcTest_expectedValue,
            'funcTest_name' => $request->funcTest_name,
            'funcTest_sampling' => $request->funcTest_sampling,
            'incmgInsp_id' => $request->incmgInsp_id,
            'rawFam_id' => $raw,
            'consFam_id' => $cons,
            'compFam_id' => $comp,
            'rawSubFam_id' => $subRaw,
            'consSubFam_id' => $subCons,
            'compSubFam_id' => $subComp,
            'funcTest_desc' => $request->funcTest_desc,
            'funcTest_specDoc' => $request->funcTest_specDoc,
        ]);
        return $funcTest;
    }


    /**
     * Function call by ListOfArticle.vue when the form is submitted with the route :/incmgInsp/funcTest/send/{id}(post)
     * Get all the functionnal test of the incoming inspection with the id in parameter
     * @return \Illuminate\Http\Response
     */
    public function send_funcTestFromIncmgInsp($id) {
        $funcTest = FunctionalTest::all()->where('incmgInsp_id', '==', $id);
        $array = [];
        foreach ($funcTest as $funcTest) {
            $array[] = [
                'id' => $funcTest->id,
                'funcTest_severityLevel' => $funcTest->funcTest_severityLevel,
                'funcTest_levelOfControl' => $funcTest->funcTest_levelOfControl,
                'funcTest_expectedMethod' => $funcTest->funcTest_expectedMethod,
                'funcTest_expectedValue' => $funcTest->funcTest_expectedValue,
                'funcTest_name' => $funcTest->funcTest_name,
                'funcTest_sampling' => $funcTest->funcTest_sampling,
                'incmgInsp_id' => $funcTest->incmgInsp_id,
                'funcTest_desc' => $funcTest->funcTest_desc,
                'funcTest_specDoc' => $funcTest->funcTest_specDoc,
            ];
        }
        return response()->json($array);
    }

    /**
     * Function call by ReferenceAFuncTest.vue with the route : /incmgInsp/funcTest/sendFromFamily/{type}/{id} (get)
     * Get all the doc control corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_funcTestFromFamily($type,$id) {
        if ($type=="comp"){
            $funcTest = FunctionalTest::all()->where('compFam_id', '==', $id)->all();
        }
        else if ($type=="raw"){
            $funcTest = FunctionalTest::all()->where('rawFam_id', '==', $id)->all();
        }
        else if ($type=="cons"){
            $funcTest = FunctionalTest::all()->where('consFam_id', '==', $id)->all();
        }
        $array = [];
        foreach ($funcTest as $funcTest) {
            $array[] = [
                'id' => $funcTest->id,
                'funcTest_severityLevel' => $funcTest->funcTest_severityLevel,
                'funcTest_levelOfControl' => $funcTest->funcTest_levelOfControl,
                'funcTest_expectedMethod' => $funcTest->funcTest_expectedMethod,
                'funcTest_expectedValue' => $funcTest->funcTest_expectedValue,
                'funcTest_name' => $funcTest->funcTest_name,
                'funcTest_sampling' => $funcTest->funcTest_sampling,
                'incmgInsp_id' => $funcTest->incmgInsp_id,
                'funcTest_desc' => $funcTest->funcTest_desc,
                'funcTest_specDoc' => $funcTest->funcTest_specDoc,
            ];
        }
        return response()->json($array);
    }

    /**
     * Function call by ReferenceAFuncTest.vue with the route : /incmgInsp/funcTest/sendFromSubFamily/{type}/{id} (get)
     * Get all the doc control corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_funcTestFromSubFamily($type,$id) {
        if ($type=="comp"){
            $funcTest = FunctionalTest::all()->where('compSubFam_id', '==', $id)->all();
        }
        else if ($type=="raw"){
            $funcTest= FunctionalTest::all()->where('rawSubFam_id', '==', $id)->all();
        }
        else if ($type=="cons"){
            $funcTest= FunctionalTest::all()->where('consSubFam_id', '==', $id)->all();
        }
        $array = [];
        foreach ($funcTest as $funcTest) {
            $array[] = [
                'id' => $funcTest->id,
                'funcTest_severityLevel' => $funcTest->funcTest_severityLevel,
                'funcTest_levelOfControl' => $funcTest->funcTest_levelOfControl,
                'funcTest_expectedMethod' => $funcTest->funcTest_expectedMethod,
                'funcTest_expectedValue' => $funcTest->funcTest_expectedValue,
                'funcTest_name' => $funcTest->funcTest_name,
                'funcTest_sampling' => $funcTest->funcTest_sampling,
                'incmgInsp_id' => $funcTest->incmgInsp_id,
                'funcTest_desc' => $funcTest->funcTest_desc,
                'funcTest_specDoc' => $funcTest->funcTest_specDoc,
            ];
        }
        return response()->json($array);
    }

     /**
     * Function call by ArticleUpdate.vue when the form is submitted for update with the route :/incmgInsp/funcTest/update/{id} (post)
     * Update an enregistrement of functional test in the data base with the informations entered in the form
     * The id parameter correspond to the id of the functional test we want to update
     * */
    public function update_funcTest(Request $request, $id) {
        $funcTest = FunctionalTest::all()->where('id', '==', $id)->first();
        if ($funcTest == null) {
            return response()->json([
                'message' => 'Functional test not found',
            ], 404);
        };
        $incmgInsp = IncomingInspection::all()->where('id', '==', $funcTest->incmgInsp_id)->first();
        if ($incmgInsp == null) {
            return response()->json([
                'message' => 'Incoming inspection not found',
            ], 404);
        };
        /*$article = null;
        if ($request->funcTest_articleType === 'cons') {
            $article = ConsFamily::all()->where('id', '==', $incmgInsp->incmgInsp_consFam_id)->first();
            $signed = $article->consFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'consFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($request->funcTest_articleType === 'raw') {
            $article = RawFamily::all()->where('id', '==', $incmgInsp->incmgInsp_rawFam_id)->first();
            $signed = $article->rawFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'rawFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($request->funcTest_articleType === 'comp') {
            $article = CompFamily::all()->where('id', '==', $incmgInsp->incmgInsp_compFam_id)->first();
            $signed = $article->compFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'compFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        }
        $article->update([
            $request->funcTest_articleType.'Fam_signatureDate' => null,
            $request->funcTest_articleType.'Fam_qualityApproverId' => null,
            $request->funcTest_articleType.'Fam_technicalReviewerId' => null,
        ]);*/
        $incmgInsp->update([
            'incmgInsp_qualityApproverId' => null,
            'incmgInsp_technicalReviewerId' => null,
            'incmgInsp_signatureDate' => null,
        ]);
        $funcTest->update([
            'funcTest_severityLevel' => $request->funcTest_severityLevel,
            'funcTest_levelOfControl' => $request->funcTest_levelOfControl,
            'funcTest_expectedMethod' => $request->funcTest_expectedMethod,
            'funcTest_expectedValue' => $request->funcTest_expectedValue,
            'funcTest_name' => $request->funcTest_name,
            'funcTest_sampling' => $request->funcTest_sampling,
            'funcTest_desc' => $request->funcTest_desc,
            'funcTest_specDoc' => $request->funcTest_specDoc,
        ]);
        return response()->json($funcTest);
    }

/**
     * Function call by FuncTestIDForm.vue when we want to delete a doc control with the route : /incmgInsp/delete/funcTest/{id}(post)
     * Delete a func test thanks to the id given in parameter
     * The id parameter correspond to the id of the func test we want to delete
     * */
    public function delete_funcTest($id){
        $func=FunctionalTest::findOrFail($id);
        $func->delete();
    }

}
