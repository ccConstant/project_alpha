<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\ComplementaryTest;
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
        if ($request->compTest_sampling === 'sampling') {
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
        ]);
    }

    public function update_compTest(Request $request, $id) {
        $compTest = ComplementaryTest::all()->find($id);
        $compTest->update([
            'compTest_sampling' => $request->compTest_sampling,
            'compTest_severityLevel' => $request->compTest_severityLevel,
            'compTest_levelOfControl' => $request->compTest_levelOfControl,
            'incmgInsp_id' => $request->incmgInsp_id,
            'compTest_name' => $request->compTest_name,
            'compTest_unitValue' => $request->compTest_unitValue,
            'compTest_expectedValue' => $request->compTest_expectedValue,
            'compTest_expectedMethod' => $request->compTest_expectedMethod,
        ]);
        return response()->json($compTest);
    }
}
