<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\FunctionalTest;
use Illuminate\Http\Request;

class FunctionnalTestController extends Controller
{
    public function verif_funcTest(Request $request) {
        $this->validate(
            $request,
            [
                'funcTest_sampling' => 'required',
                'incmgInsp_id' => 'required|integer',
            ],
            [
                'funcTest_sampling.required' => 'You must enter a sampling',

                'incmgInsp_id.required' => 'You must enter an incoming inspection id',
                'incmgInsp_id.integer' => 'The incoming inspection id must be an integer',
            ]
        );
        if ($request->funcTest_sampling === 'sampling') {
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
        if ($request->funcTest_articleType === 'comp') {
            $this->validate(
                $request,
                [
                    'funcTest_expectedMethod' => 'required|string|min:2|max:255',
                    'funcTest_expectedValue' => 'required|string|min:1|max:50',
                    'funcTest_name' => 'required|string|min:2|max:255',
                    'funcTest_unitValue' => 'required|string|min:1|max:10',
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

                    'funcTest_unitValue.required' => 'You must enter a unit value',
                    'funcTest_unitValue.string' => 'The unit value must be a string',
                    'funcTest_unitValue.min' => 'You must enter at least one character',
                    'funcTest_unitValue.max' => 'You must enter a maximum of 10 characters',
                ]
            );
        }
    }

    public function add_funcTest(Request $request) {
        $funcTest = FunctionalTest::create([
            'funcTest_severityLevel' => $request->funcTest_severityLevel,
            'funcTest_levelOfControl' => $request->funcTest_levelOfControl,
            'funcTest_expectedMethod' => $request->funcTest_expectedMethod,
            'funcTest_expectedValue' => $request->funcTest_expectedValue,
            'funcTest_name' => $request->funcTest_name,
            'funcTest_unitValue' => $request->funcTest_unitValue,
            'funcTest_sampling' => $request->funcTest_sampling,
            'incmgInsp_id' => $request->incmgInsp_id,
        ]);
        return $funcTest;
    }

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
                'funcTest_unitValue' => $funcTest->funcTest_unitValue,
                'funcTest_sampling' => $funcTest->funcTest_sampling,
                'incmgInsp_id' => $funcTest->incmgInsp_id,
            ];
        }
        return response()->json($array);
    }

    public function send_funcTest($id) {
        $funcTest = FunctionalTest::all()->find($id);
        return response()->json([
            'id' => $funcTest->id,
            'funcTest_severityLevel' => $funcTest->funcTest_severityLevel,
            'funcTest_levelOfControl' => $funcTest->funcTest_levelOfControl,
            'funcTest_expectedMethod' => $funcTest->funcTest_expectedMethod,
            'funcTest_expectedValue' => $funcTest->funcTest_expectedValue,
            'funcTest_name' => $funcTest->funcTest_name,
            'funcTest_unitValue' => $funcTest->funcTest_unitValue,
            'funcTest_sampling' => $funcTest->funcTest_sampling,
            'incmgInsp_id' => $funcTest->incmgInsp_id,
        ]);
    }

    public function update_funcTest(Request $request, $id) {
        $funcTest = FunctionalTest::all()->find($id);
        $funcTest->update([
            'funcTest_severityLevel' => $request->funcTest_severityLevel,
            'funcTest_levelOfControl' => $request->funcTest_levelOfControl,
            'funcTest_expectedMethod' => $request->funcTest_expectedMethod,
            'funcTest_expectedValue' => $request->funcTest_expectedValue,
            'funcTest_name' => $request->funcTest_name,
            'funcTest_unitValue' => $request->funcTest_unitValue,
            'funcTest_sampling' => $request->funcTest_sampling,
            'incmgInsp_id' => $request->incmgInsp_id,
        ]);
        return response()->json($funcTest);
    }
}
