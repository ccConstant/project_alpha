<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\DimensionalTest;
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
        if ($request->dimTest_sampling === 'sampling') {
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
        ]);
    }

    public function update_dimTest(Request $request, $id) {
        $dimTest = DimensionalTest::all()->find($id);
        $dimTest->update([
            'dimTest_sampling' => $request->dimTest_sampling,
            'dimTest_severityLevel' => $request->dimTest_severityLevel,
            'dimTest_levelOfControl' => $request->dimTest_levelOfControl,
            'dimTest_expectedMethod' => $request->dimTest_expectedMethod,
            'dimTest_expectedValue' => $request->dimTest_expectedValue,
            'dimTest_name' => $request->dimTest_name,
            'dimTest_unitValue' => $request->dimTest_unitValue,
            'incmgInsp_id' => $request->incmgInsp_id,
        ]);
        return response()->json($dimTest);
    }
}
