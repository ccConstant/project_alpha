<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\DimensionalTest;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\RawFamily;
use Illuminate\Http\Request;

class DimensionnalTestController extends Controller
{
    public function verif_dimTest(Request $request) {
        $this->validate(
            $request,
            [
                'dimTest_sampling' => 'required',
                'incmgInsp_id' => 'required',
            ],
            [
                'dimTest_sampling.required' => 'You must enter a sampling',
                'incmgInsp_id.required' => 'You must enter an incoming inspection',
            ]
        );
        if ($request->dimTest_sampling === 'statistics') {
            $this->validate(
                $request,
                [
                    'dimTest_severityLevel' => 'required',
                    'dimTest_levelOfControl' => 'required',
                ],
                [
                    'dimTest_severityLevel.required' => 'You must enter a severity level',
                    'dimTest_levelOfControl.required' => 'You must enter a level of control',
                ]
            );
        }
        if ($request->dimTest_sampling === 'other') {
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
                    'dimTest_expectedValue' => 'required|integer',
                    'dimTest_name' => 'required|string|min:2|max:255',
                    'dimTest_unitValue' => 'required|string|min:1|max:10',
                ],
                [
                    'dimTest_expectedMethod.required' => 'You must enter an expected method',
                    'dimTest_expectedMethod.string' => 'The expected method must be a string',
                    'dimTest_expectedMethod.min' => 'You must enter at least two characters',
                    'dimTest_expectedMethod.max' => 'You must enter a maximum of 255 characters',

                    'dimTest_expectedValue.required' => 'You must enter an expected value',
                    'dimTest_expectedValue.integer' => 'The expected value must be a integer',

                    'dimTest_name.required' => 'You must enter a name of control',
                    'dimTest_name.string' => 'The name of control must be a string',
                    'dimTest_name.min' => 'You must enter at least two characters',
                    'dimTest_name.max' => 'You must enter a maximum of 255 characters',

                    'dimTest_unitValue.required' => 'You must enter a unit value',
                    'dimTest_unitValue.string' => 'The unit value must be a string',
                    'dimTest_unitValue.min' => 'You must enter at least one character',
                    'dimTest_unitValue.max' => 'You must enter a maximum of 10 characters',
                ]
            );
        }
    }

    public function add_dimTest(Request $request) {
        $dimTest = DimensionalTest::create([
            'dimTest_sampling' => $request->dimTest_sampling,
            'dimTest_severityLevel' => $request->dimTest_severityLevel,
            'dimTest_levelOfControl' => $request->dimTest_levelOfControl,
            'dimTest_expectedMethod' => $request->dimTest_expectedMethod,
            'dimTest_expectedValue' => $request->dimTest_expectedValue,
            'dimTest_name' => $request->dimTest_name,
            'dimTest_unitValue' => $request->dimTest_unitValue,
            'incmgInsp_id' => $request->incmgInsp_id,
            'dimTest_desc' => $request->dimTest_desc,
        ]);
        return response()->json($dimTest);
    }

    public function send_dimTestFromIncmgInsp($id) {
        $dimTest = DimensionalTest::all()->where('incmgInsp_id', $id);
        $array = [];
        foreach ($dimTest as $item) {
            $obj = [
                'id' => $item->id,
                'dimTest_sampling' => $item->dimTest_sampling,
                'dimTest_severityLevel' => $item->dimTest_severityLevel,
                'dimTest_levelOfControl' => $item->dimTest_levelOfControl,
                'dimTest_expectedMethod' => $item->dimTest_expectedMethod,
                'dimTest_expectedValue' => $item->dimTest_expectedValue,
                'dimTest_name' => $item->dimTest_name,
                'dimTest_unitValue' => $item->dimTest_unitValue,
                'incmgInsp_id' => $item->incmgInsp_id,
                'dimTest_desc' => $item->dimTest_desc,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    public function send_dimTest($id) {
        $dimTest = DimensionalTest::all()->find($id);
        return response()->json([
            'id' => $dimTest->id,
            'dimTest_sampling' => $dimTest->dimTest_sampling,
            'dimTest_severityLevel' => $dimTest->dimTest_severityLevel,
            'dimTest_levelOfControl' => $dimTest->dimTest_levelOfControl,
            'dimTest_expectedMethod' => $dimTest->dimTest_expectedMethod,
            'dimTest_expectedValue' => $dimTest->dimTest_expectedValue,
            'dimTest_name' => $dimTest->dimTest_name,
            'dimTest_unitValue' => $dimTest->dimTest_unitValue,
            'incmgInsp_id' => $dimTest->incmgInsp_id,
            'dimTest_desc' => $dimTest->dimTest_desc,
        ]);
    }

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
        $article = null;
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
        ]);
        $incmgInsp->update([
            'incmgInsp_qualityApproverId' => null,
            'incmgInsp_technicalReviewerId' => null,
            'incmgInsp_signatureDate' => null,
        ]);
        $dimTest->update([
            'dimTest_sampling' => $request->dimTest_sampling,
            'dimTest_severityLevel' => $request->dimTest_severityLevel,
            'dimTest_levelOfControl' => $request->dimTest_levelOfControl,
            'dimTest_expectedMethod' => $request->dimTest_expectedMethod,
            'dimTest_expectedValue' => $request->dimTest_expectedValue,
            'dimTest_name' => $request->dimTest_name,
            'dimTest_unitValue' => $request->dimTest_unitValue,
            'incmgInsp_id' => $request->incmgInsp_id,
            'dimTest_desc' => $request->dimTest_desc,
        ]);
        return response()->json($dimTest);
    }
}
