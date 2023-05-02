<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\AspectTest;
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
        if ($request->aspTest_sampling === 'sampling') {
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
    }

    public function add_aspectTest(Request $request) {
        $aspTest = AspectTest::create([
            'aspTest_severityLevel' => $request->aspTest_severityLevel,
            'aspTest_levelOfControl' => $request->aspTest_levelOfControl,
            'aspTest_expectedAspect' => $request->aspTest_expectedAspect,
            'aspTest_name' => $request->aspTest_name,
            'aspTest_sampling' => $request->aspTest_sampling,
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
            'incmgInsp_id' => $aspTest->incmgInsp_id,
        ]);
    }

public function update_aspectTest(Request $request, $id) {
        $aspTest = AspectTest::find($id);
        $aspTest->update([
            'aspTest_severityLevel' => $request->aspTest_severityLevel,
            'aspTest_levelOfControl' => $request->aspTest_levelOfControl,
            'aspTest_expectedAspect' => $request->aspTest_expectedAspect,
            'aspTest_name' => $request->aspTest_name,
            'aspTest_sampling' => $request->aspTest_sampling,
            'incmgInsp_id' => $request->incmgInsp_id,
        ]);
        return response()->json($aspTest);
    }
}
