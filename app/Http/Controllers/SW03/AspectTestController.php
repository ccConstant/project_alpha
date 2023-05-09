<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\AspectTest;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\RawFamily;
use Illuminate\Http\Request;

class AspectTestController extends Controller
{
    public function verif_aspectTest(Request $request) {
        $this->validate(
            $request,
            [
                'aspTest_expectedAspect' => 'required|string|min:2|max:255',
                'aspTest_name' => 'required|string|min:2|max:255',
                'aspTest_sampling' => 'required',
                'incmgInsp_id' => 'required|integer',
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

                'incmgInsp_id.required' => 'You must enter an incoming inspection id',
                'incmgInsp_id.integer' => 'The incoming inspection id must be an integer',
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

    }

    public function add_aspectTest(Request $request) {
        $aspTest = AspectTest::create([
            'aspTest_severityLevel' => $request->aspTest_severityLevel,
            'aspTest_levelOfControl' => $request->aspTest_levelOfControl,
            'aspTest_expectedAspect' => $request->aspTest_expectedAspect,
            'aspTest_name' => $request->aspTest_name,
            'aspTest_sampling' => $request->aspTest_sampling,
            'aspTest_desc' => $request->aspTest_desc,
            'incmgInsp_id' => $request->incmgInsp_id,
        ]);
        return response()->json($aspTest);
    }

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
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    public function send_aspectTest($id) {
        $aspTest = AspectTest::find($id);
        return response()->json([
            'id' => $aspTest->id,
            'aspTest_severityLevel' => $aspTest->aspTest_severityLevel,
            'aspTest_levelOfControl' => $aspTest->aspTest_levelOfControl,
            'aspTest_expectedAspect' => $aspTest->aspTest_expectedAspect,
            'aspTest_name' => $aspTest->aspTest_name,
            'aspTest_sampling' => $aspTest->aspTest_sampling,
            'aspTest_desc' => $aspTest->aspTest_desc,
            'incmgInsp_id' => $aspTest->incmgInsp_id,
        ]);
    }

    public function update_aspectTest(Request $request, $id) {
        $aspTest = AspectTest::all()->where('id', '==', $id)->first();
        if ($aspTest == null) {
            return response()->json([
                'message' => 'Aspect test not found',
            ], 404);
        };
        $incmgInsp = IncomingInspection::all()->where('id', '==', $aspTest->incmgInsp_id)->first();
        if ($incmgInsp == null) {
            return response()->json([
                'message' => 'Incoming inspection not found',
            ], 404);
        };
        $article = null;
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
        ]);
        $aspTest->update([
            'aspTest_severityLevel' => $request->aspTest_severityLevel,
            'aspTest_levelOfControl' => $request->aspTest_levelOfControl,
            'aspTest_expectedAspect' => $request->aspTest_expectedAspect,
            'aspTest_name' => $request->aspTest_name,
            'aspTest_sampling' => $request->aspTest_sampling,
            'aspTest_desc' => $request->aspTest_desc,
        ]);
        return response()->json($aspTest);
    }
}
