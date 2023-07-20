<?php

/*
* Filename : PurchaseSpecificationController.php
* Creation date : 2 May 2023
* Update date : 2 May 2023
* This file is used to link the view files and the database that concern the purchase specification  table.
* For example : add a purchase specification in the data base, update a purchase specification...
*/

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\CompSubFamily;
use App\Models\SW03\ConsSubFamily;
use App\Models\SW03\RawSubFamily;
use App\Models\SW03\PurchaseSpecification;
use App\Models\SW03\RawFamily;
use App\Models\SW03\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PurchaseSpecificationController extends Controller
{
    /**
     * Function call by ArticlePurchaseSpecificationForm.vue when the form is submitted for verif with the route : '/purSpe/verif (post)
     * Verify that the informations entered by the user are correct
     */
    public function verif_purSpe(Request $request)
    {
        $this->validate(
            $request,
            [
                'purSpe_supplier_id' => 'required',
            ],
            [

                'purSpe_supplier_id.required' => 'You must enter a supplier id',
            ]
        );
        if ($request->purSpe_supplier_id != "Alpha") {
            $this->validate(
                $request,
                [
                    'purSpe_supplier_ref' => 'required|max:255',
                ],
                [
                    'purSpe_supplier_ref.required' => 'You must enter a supplier reference',
                    'purSpe_supplier_ref.max' => 'The supplier reference must not exceed 255 characters',
                ]
            );
        }
    }

    /**
     * Function call by ArticlePurchaseSpecificationForm.vue when the form is submitted for insert with the route : '/purSpe/add (post)
     * Add a new enregistrement of purchase specification in the data base with the informations entered in the form
     * @return Response : id of the new purchase specification
     */
    public function add_purSpe(Request $request, $id)
    {
        //Creation of a new purchase specification
        $consFam_id = null;
        $rawFam_id = null;
        $compFam_id = null;
        $supplier = Supplier::all()->where('supplr_name', '==', $request->purSpe_supplier_id)->first();
        if ($supplier === null) {
            return response()->json('error', 429);
        }
        if ($request->artFam_type == "COMP") {
            $compFam_id = $id;
        }
        if ($request->artFam_type == "RAW") {
            $rawFam_id = $id;
        }
        if ($request->artFam_type == "CONS") {
            $consFam_id = $id;
        }
        $purSpe = PurchaseSpecification::create([
            'consFam_id' => $consFam_id,
            'rawFam_id' => $rawFam_id,
            'compFam_id' => $compFam_id,
            'purSpe_validate' => $request->purSpe_validate
        ]);
        if ($request->artFam_type == "COMP") {
            $comp = CompFamily::all()->where('id', '==', $id)->first();
            $comp->suppliers()->attach(
                $supplier,
                [
                    'supplr_ref' => $request->purSpe_supplier_ref,
                    'purSpec_id' => $purSpe->id,
                    'remark' => $request->purSpe_remark,
                    'documentsRequest' => $request->purSpe_documentsRequest,
                    'specification' => $request->purSpe_specification,
                ]
            );
        }
        if ($request->artFam_type == "RAW") {
            $raw = RawFamily::all()->where('id', '==', $id)->first();
            $raw->suppliers()->attach(
                $supplier,
                [
                    'supplr_ref' => $request->purSpe_supplier_ref,
                    'purSpec_id' => $purSpe->id,
                    'remark' => $request->purSpe_remark,
                    'documentsRequest' => $request->purSpe_documentsRequest,
                    'specification' => $request->purSpe_specification,
                ]
            );
        }
        if ($request->artFam_type == "CONS") {
            $cons = ConsFamily::all()->where('id', '==', $id)->first();
            $cons->suppliers()->attach(
                $supplier,
                [
                    'supplr_ref' => $request->purSpe_supplier_ref,
                    'purSpec_id' => $purSpe->id,
                    'remark' => $request->purSpe_remark,
                    'documentsRequest' => $request->purSpe_documentsRequest,
                    'specification' => $request->purSpe_specification,
                ]
            );
        }
        $purSpe_id = $purSpe->id;
        return response()->json($purSpe_id);
    }

    /**
     * Function call by ArticlePurchaseSpecificationForm.vue when the form is submitted for insert with the route : /artFam/purSpe/add (post)
     * Add a new enregistrement of purchase specification in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new purchase specification
     */
    public function add_purSpe_subFam(Request $request, $id) {
        $compSubFam_id = null;
        $consSubFam_id = null;
        $rawSubFam_id = null;

        $supplier = Supplier::all()->where('supplr_name', '==', $request->purSpe_supplier_id)->first();
        if ($supplier === null) {
            return response()->json('error', 429);
        }
        if ($request->artFam_type === 'COMP') {
            $compSubFam_id = $id;
        } else if ($request->artFam_type === 'CONS') {
            $consSubFam_id = $id;
        } else if ($request->artFam_type === 'RAW') {
            $rawSubFam_id = $id;
        }
        $purSpe = PurchaseSpecification::create([
            'consSubFam_id' => $consSubFam_id,
            'rawSubFam_id' => $rawSubFam_id,
            'compSubFam_id' => $compSubFam_id,
            'purSpe_validate' => $request->purSpe_validate
        ]);
        if ($request->artFam_type == "COMP") {
            $subComp = CompSubFamily::all()->where('id', '==', $id)->first();
            $subComp->suppliers()->attach(
                $supplier,
                [
                    'supplr_ref' => $request->purSpe_supplier_ref,
                    'purSpec_id' => $purSpe->id,
                    'remark' => $request->purSpe_remark,
                    'documentsRequest' => $request->purSpe_documentsRequest,
                    'specification' => $request->purSpe_specification,
                ]
            );
        }
        if ($request->artFam_type == "RAW") {
            $subRaw = RawSubFamily::all()->where('id', '==', $id)->first();
            $subRaw->suppliers()->attach(
                $supplier,
                [
                    'supplr_ref' => $request->purSpe_supplier_ref,
                    'purSpec_id' => $purSpe->id,
                    'remark' => $request->purSpe_remark,
                    'documentsRequest' => $request->purSpe_documentsRequest,
                    'specification' => $request->purSpe_specification,
                ]
            );
        }
        if ($request->artFam_type == "CONS") {
            $subCons = ConsSubFamily::all()->where('id', '==', $id)->first();
            $subCons->suppliers()->attach(
                $supplier,
                [
                    'supplr_ref' => $request->purSpe_supplier_ref,
                    'purSpec_id' => $purSpe->id,
                    'remark' => $request->purSpe_remark,
                    'documentsRequest' => $request->purSpe_documentsRequest,
                    'specification' => $request->purSpe_specification,
                ]
            );
        }
        $purSpe_id = $purSpe->id;
        return response()->json($purSpe_id);
        
    }

    public function send_purSpes($type, $id)
    {
        $array = [];
        $pivot = [];
        $supplier = null;
        if ($type === 'cons') {
            $purSpecs = PurchaseSpecification::all()->where('consFam_id', '==', $id);
            $supplier = ConsFamily::all()->where('id', '==', $id)->first()->suppliers;
            foreach ($supplier as $sup) {
                array_push($pivot, DB::table('pivot_cons_fam_supplr')->where('consFam_id', $sup->pivot->consFam_id)
                    ->where('supplr_id', $sup->pivot->supplr_id)->first());
            }
        } else if ($type === 'raw') {
            $purSpecs = PurchaseSpecification::all()->where('rawFam_id', '==', $id);
            $supplier = RawFamily::all()->where('id', '==', $id)->first()->suppliers;
            foreach ($supplier as $sup) {
                array_push($pivot, DB::table('pivot_raw_fam_supplr')->where('rawFam_id', $sup->pivot->rawFam_id)
                    ->where('supplr_id', $sup->pivot->supplr_id)->first());
            }
        } else if ($type === 'comp') {
            $purSpecs = PurchaseSpecification::all()->where('compFam_id', '==', $id);
            $supplier = CompFamily::all()->where('id', '==', $id)->first()->suppliers;
            foreach ($supplier as $sup) {
                array_push($pivot, DB::table('pivot_comp_fam_supplr')->where('compFam_id', $sup->pivot->compFam_id)
                    ->where('supplr_id', $sup->pivot->supplr_id)->first());
            }
        }
        foreach ($purSpecs as $purSpec) {
            $supp = null;
            foreach ($pivot as $piv) {
                if ($piv->purSpec_id === $purSpec->id) {
                    $supp = $piv;
                }
            }
            array_push($array, [
                'id' => $purSpec->id,
                'purSpe_validate' => $purSpec->purSpe_validate,
                'consFam_id' => $purSpec->consFam_id,
                'rawFam_id' => $purSpec->rawFam_id,
                'compFam_id' => $purSpec->compFam_id,
                'purSpe_supplier_id' => Supplier::all()->where('id', '==', $supp->supplr_id)->first()->supplr_name,
                'purSpe_supplier_ref' => $supp->supplr_ref,
                'purSpe_remark' => $supp->remark,
                'purSpe_documentsRequest' => $supp->documentsRequest,
                'purSpe_specification' => $supp->specification,
            ]);
        }
        return response()->json($array);
    }


    public function send_purSpes_subFam($type, $id)
    {
        $array = [];
        $pivot = [];
        $supplier = null;
        if ($type === 'cons') {
            $purSpecs = PurchaseSpecification::all()->where('consSubFam_id', '==', $id);
            $supplier = ConsSubFamily::all()->where('id', '==', $id)->first()->suppliers;
            foreach ($supplier as $sup) {
                array_push($pivot, DB::table('pivot_cons_sub_fam_supplr')->where('consSubFam_id', $sup->pivot->consSubFam_id)
                    ->where('supplr_id', $sup->pivot->supplr_id)->first());
            }
        } else if ($type === 'raw') {
            $purSpecs = PurchaseSpecification::all()->where('rawSubFam_id', '==', $id);
            $supplier = RawSubFamily::all()->where('id', '==', $id)->first()->suppliers;
            foreach ($supplier as $sup) {
                array_push($pivot, DB::table('pivot_raw_sub_fam_supplr')->where('rawSubFam_id', $sup->pivot->rawSubFam_id)
                    ->where('supplr_id', $sup->pivot->supplr_id)->first());
            }
        } else if ($type === 'comp') {
            $purSpecs = PurchaseSpecification::all()->where('compSubFam_id', '==', $id);
            $supplier = CompSubFamily::all()->where('id', '==', $id)->first()->suppliers;
            foreach ($supplier as $sup) {
                array_push($pivot, DB::table('pivot_comp_sub_fam_supplr')->where('compSubFam_id', $sup->pivot->compSubFam_id)
                    ->where('supplr_id', $sup->pivot->supplr_id)->first());
            }
        }
        foreach ($purSpecs as $purSpec) {
            $supp = null;
            foreach ($pivot as $piv) {
                if ($piv->purSpec_id === $purSpec->id) {
                    $supp = $piv;
                }
            }
            array_push($array, [
                'id' => $purSpec->id,
                'purSpe_validate' => $purSpec->purSpe_validate,
                'purSpe_supplier_id' => Supplier::all()->where('id', '==', $supp->supplr_id)->first()->supplr_name,
                'purSpe_supplier_ref' => $supp->supplr_ref,
                'purSpe_remark' => $supp->remark,
                'purSpe_documentsRequest' => $supp->purSpe_documentsRequest,
                'purSpe_specification' => $supp->purSpe_specification,
            ]);
        }
        return response()->json($array);
    }
    

    public function update_purSpe(Request $request, $type, $id)
    {
        $purSpec = PurchaseSpecification::all()->where('id', '==', $id)->first();
        $supplier = Supplier::all()->where('supplr_name', '==', $request->purSpe_supplier_id)->first();
        if ($type === 'cons') {
            $article = ConsFamily::all()->where('id', '==', $purSpec->consFam_id)->first();
            $signed = $article->consFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'consFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($type === 'raw') {
            $article = RawFamily::all()->where('id', '==', $purSpec->rawFam_id)->first();
            $signed = $article->rawFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'rawFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($type === 'comp') {
            $article = CompFamily::all()->where('id', '==', $purSpec->compFam_id)->first();
            $signed = $article->compFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'compFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else {
            return response()->json('error', 429);
        }
        $article->update([
            $type . 'Fam_signatureDate' => null,
            $type . 'Fam_qualityApproverId' => null,
            $type . 'Fam_technicalReviewerId' => null,
        ]);
        $purSpec->update([
            'purSpe_validate' => $request->purSpe_validate,
            'purSpe_supplier_ref' => $request->purSpe_supplier_ref,
            'purSpe_supplier_id' => $request->purSpe_supplier_id,
            'purSpe_qualityApproverId' => null,
            'purSpe_technicalReviewerId' => null,
            'purSpe_signatureDate' => null,
        ]);
        $article->suppliers()->detach($supplier);
        $article->suppliers()->attach(
            $supplier,
            [
                'supplr_ref' => $request->purSpe_supplier_ref,
                'purSpec_id' => $purSpec->id,
                'remark' => $request->purSpe_remark,
                'documentsRequest' => $request->purSpe_documentsRequest,
                'specification' => $request->purSpe_specification,
            ]
        );
        return response()->json($purSpec);
    }
}
