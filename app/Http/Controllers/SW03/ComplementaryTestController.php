<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ComplementaryTest;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\RawFamily;
use Illuminate\Http\Request;

class ComplementaryTestController extends Controller
{
    public function verif_compTest(Request $request) {
        $this->validate(
            $request,
            [
                'compTest_sampling' => 'required|integer',
                'incmgInsp_id' => 'required',
            ],
            [
                'compTest_sampling.required' => 'You must enter a sampling',
                'compTest_sampling.integer' => 'The sampling must be an integer'
            ]
        );
        if ($request->compTest_sampling === 'statistics') {
            $this->validate(
                $request,
                [
                    'compTest_severityLevel' => 'required',
                    'compTest_levelOfControl' => 'required',
                ],
                [
                    'compTest_severityLevel.required' => 'You must enter a severity level',
                    'compTest_levelOfControl.required' => 'You must enter a level of control',
                ]
            );
        }
        if ($request->compTest_sampling === 'other') {
            $this->validate(
                $request,
                [
                    'compTest_desc' => 'required|string|min:2|max:255',
                ],
                [
                    'compTest_desc.required' => 'You must enter a description',
                    'compTest_desc.string' => 'The description must be a string',
                    'compTest_desc.min' => 'You must enter at least two characters',
                    'compTest_desc.max' => 'You must enter a maximum of 255 characters',
                ]
            );
        }
        if ($request->compTest_articleType === 'comp') {
            $this->validate(
                $request,
                [
                    'compTest_expectedMethod' => 'required|string|min:2|max:255',
                    'compTest_expectedValue' => 'required|min:1|max:50',
                    'compTest_name' => 'required|string|min:2|max:255',
                    'compTest_unitValue' => 'required|string|min:1|max:10',
                ],
                [
                    'compTest_expectedMethod.required' => 'You must enter an expected method',
                    'compTest_expectedMethod.string' => 'The expected method must be a string',
                    'compTest_expectedMethod.min' => 'You must enter at least two characters',
                    'compTest_expectedMethod.max' => 'You must enter a maximum of 255 characters',

                    'compTest_expectedValue.required' => 'You must enter an expected value',
                    'compTest_expectedValue.min' => 'You must enter at least one character',
                    'compTest_expectedValue.max' => 'You must enter a maximum of 50 characters',

                    'compTest_name.required' => 'You must enter a name',
                    'compTest_name.string' => 'The name must be a string',
                    'compTest_name.min' => 'You must enter at least two characters',
                    'compTest_name.max' => 'You must enter a maximum of 255 characters',

                    'compTest_unitValue.required' => 'You must enter a unit value',
                    'compTest_unitValue.string' => 'The unit value must be a string',
                    'compTest_unitValue.min' => 'You must enter at least one character',
                    'compTest_unitValue.max' => 'You must enter a maximum of 10 characters',
                ]
            );
        }
    }

    public function add_compTest(Request $request) {
        $compTest = ComplementaryTest::create([
            'compTest_sampling' => $request->compTest_sampling,
            'compTest_severityLevel' => $request->compTest_severityLevel,
            'compTest_levelOfControl' => $request->compTest_levelOfControl,
            'incmgInsp_id' => $request->incmgInsp_id,
            'compTest_name' => $request->compTest_name,
            'compTest_unitValue' => $request->compTest_unitValue,
            'compTest_expectedValue' => $request->compTest_expectedValue,
            'compTest_expectedMethod' => $request->compTest_expectedMethod,
            'compTest_desc' => $request->compTest_desc,
        ]);
        return response()->json($compTest);
    }

    public function send_compTestFromIncmgInsp($id) {
        $compTests = ComplementaryTest::all()->where('incmgInsp_id', $id);
        $array = [];
        foreach ($compTests as $compTest) {
            $obj = [
                'id' => $compTest->id,
                'compTest_sampling' => $compTest->compTest_sampling,
                'compTest_severityLevel' => $compTest->compTest_severityLevel,
                'compTest_levelOfControl' => $compTest->compTest_levelOfControl,
                'incmgInsp_id' => $compTest->incmgInsp_id,
                'compTest_name' => $compTest->compTest_name,
                'compTest_unitValue' => $compTest->compTest_unitValue,
                'compTest_expectedValue' => $compTest->compTest_expectedValue,
                'compTest_expectedMethod' => $compTest->compTest_expectedMethod,
                'compTest_desc' => $compTest->compTest_desc,
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    public function send_compTest($id) {
        $compTest = ComplementaryTest::all()->find($id);
        return response()->json([
            'id' => $compTest->id,
            'compTest_sampling' => $compTest->compTest_sampling,
            'compTest_severityLevel' => $compTest->compTest_severityLevel,
            'compTest_levelOfControl' => $compTest->compTest_levelOfControl,
            'incmgInsp_id' => $compTest->incmgInsp_id,
            'compTest_name' => $compTest->compTest_name,
            'compTest_unitValue' => $compTest->compTest_unitValue,
            'compTest_expectedValue' => $compTest->compTest_expectedValue,
            'compTest_expectedMethod' => $compTest->compTest_expectedMethod,
            'compTest_desc' => $compTest->compTest_desc,
        ]);
    }

    public function update_compTest(Request $request, $id) {
        $compTest = ComplementaryTest::all()->where('id', '==', $id)->first();
        if ($compTest == null) {
            return response()->json([
                'message' => 'Complementary test not found',
            ], 404);
        };
        $incmgInsp = IncomingInspection::all()->where('id', '==', $compTest->incmgInsp_id)->first();
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
        $compTest->update([
            'compTest_sampling' => $request->compTest_sampling,
            'compTest_severityLevel' => $request->compTest_severityLevel,
            'compTest_levelOfControl' => $request->compTest_levelOfControl,
            'incmgInsp_id' => $request->incmgInsp_id,
            'compTest_name' => $request->compTest_name,
            'compTest_unitValue' => $request->compTest_unitValue,
            'compTest_expectedValue' => $request->compTest_expectedValue,
            'compTest_expectedMethod' => $request->compTest_expectedMethod,
            'compTest_desc' => $request->compTest_desc,
        ]);
        return response()->json($compTest);
    }
}
