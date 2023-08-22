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
                ]
            );
        }
        $purSpe_id = $purSpe->id;
        return response()->json($purSpe_id);
    }



     /**
     * Function call by ArticlePurchaseSpecificationForm.vue when the form is submitted for insert with the route : '/purSpe/addCommon/ (post)
     * Add a new enregistrement of purchase specification in the data base with the informations entered in the form
     */
    public function add_purSpe_common(Request $request, $type, $id)
    {
        //Creation of a new purchase specification
        $consFam_id = null;
        $rawFam_id = null;
        $compFam_id = null;
        if ($type == "comp") {
            $compFamily=CompFamily::all()->where('id', '==', $id)->first();
            $compFamily->update([
                'compFam_specifications' => $request->purSpe_specification,
                'compFam_documentsRequested' => $request->purSpe_documentsRequest,
            ]);
        }
        if ($type == "raw") {
            $rawFamily=RawFamily::all()->where('id', '==', $id)->first();
            $rawFamily->update([
                'rawFam_specifications' => $request->purSpe_specification,
                'rawFam_documentsRequested' => $request->purSpe_documentsRequest,
            ]);
        }
        if ($type == "cons") {
            $consFamily=ConsFamily::all()->where('id', '==', $id)->first();
            $consFamily->update([
                'consFam_specifications' => $request->purSpe_specification,
                'consFam_documentsRequested' => $request->purSpe_documentsRequest,
            ]);
        }
    }


     /**
     * Function call by ArticlePurchaseSpecificationForm.vue when the form is submitted for insert with the route : '/purSpe/addCommon/ (post)
     * Add a new enregistrement of purchase specification in the data base with the informations entered in the form
     */
    public function add_purSpe_common_subFam(Request $request, $type, $id)
    {
        if ($type == "comp") {
            $compSubFamily=CompSubFamily::all()->where('id', '==', $id)->first();
            $compSubFamily->update([
                'compSubFam_specifications' => $request->purSpe_specification,
                'compSubFam_documentsRequested' => $request->purSpe_documentsRequest,
            ]);
        }
        if ($type == "raw") {
            $rawSubFamily=RawSubFamily::all()->where('id', '==', $id)->first();
            $rawSubFamily->update([
                'rawSubFam_specifications' => $request->purSpe_specification,
                'rawSubFam_documentsRequested' => $request->purSpe_documentsRequest,
            ]);
        }
        if ($type == "cons") {
            $consSubFamily=ConsSubFamily::all()->where('id', '==', $id)->first();
            $consSubFamily->update([
                'consSubFam_specifications' => $request->purSpe_specification,
                'consSubFam_documentsRequested' => $request->purSpe_documentsRequest,
            ]);
        }
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
            $supplier_id=Supplier::all()->where('id', '==', $supp->supplr_id)->first();
            $supplier_name="" ; 
            $supplier_ref=$supp->supplr_ref;;
            if ($supplier_id!=null){
              $supplier_name=$supplier_id->supplr_name;
            }
            array_push($array, [
                'id' => $purSpec->id,
                'purSpe_validate' => $purSpec->purSpe_validate,
                'consFam_id' => $purSpec->consFam_id,
                'rawFam_id' => $purSpec->rawFam_id,
                'compFam_id' => $purSpec->compFam_id,
                'purSpe_supplier_id' => $supplier_name,
                'purSpe_supplier_ref' => $supplier_ref,
                'purSpe_remark' => $supp->remark,
            ]);
        }
        return response()->json($array);
    }


    public function send_purSpes_common($type, $id)
    {
        $specifications="";
        $documentsRequested="";
        $compFam_id = null;
        $consFam_id = null;
        $rawFam_id = null;
        $array = [];
        if ($type === 'cons') {
            $consFamily=ConsFamily::all()->where('id', '==', $id)->first();
            $specifications=$consFamily->consFam_specifications;
            $documentsRequested=$consFamily->consFam_documentsRequested;
            $consFam_id=$id;
        } else if ($type === 'raw') {
            $rawFamily=RawFamily::all()->where('id', '==', $id)->first();
            $specifications=$rawFamily->rawFam_specifications;
            $documentsRequested=$rawFamily->rawFam_documentsRequested;
            $rawFam_id=$id;
        } else if ($type === 'comp') {
            $compFamily=CompFamily::all()->where('id', '==', $id)->first();
            $specifications=$compFamily->compFam_specifications;
            $documentsRequested=$compFamily->compFam_documentsRequested;
            $compFam_id=$id;
        }
        array_push($array, [
            'consFam_id' => $consFam_id,
            'rawFam_id' => $rawFam_id,
            'compFam_id' => $compFam_id,
            'purSpe_documentsRequested' => $documentsRequested,
            'purSpe_specifications' => $specifications,
            'id' => $id,
        ]);
        return response()->json($array);
    }


    public function send_purSpes_common_subFam($type, $id)
    {
        $specifications="";
        $documentsRequested="";
        $compSubFam_id = null;
        $consSubFam_id = null;
        $rawSubFam_id = null;
        $array = [];
        if ($type === 'cons') {
            $consSubFamily=ConsSubFamily::all()->where('id', '==', $id)->first();
            $specifications=$consSubFamily->consSubFam_specifications;
            $documentsRequested=$consSubFamily->consSubFam_documentsRequested;
            $consSubFam_id=$id;
        } else if ($type === 'raw') {
            $rawSubFamily=RawSubFamily::all()->where('id', '==', $id)->first();
            $specifications=$rawSubFamily->rawSubFam_specifications;
            $documentsRequested=$rawSubFamily->rawSubFam_documentsRequested;
            $rawFam_id=$id;
        } else if ($type === 'comp') {
            $compSubFamily=CompSubFamily::all()->where('id', '==', $id)->first();
            $specifications=$compSubFamily->compSubFam_specifications;
            $documentsRequested=$compSubFamily->compSubFam_documentsRequested;
            $compFam_id=$id;
        }
        array_push($array, [
            'consSubFam_id' => $consSubFam_id,
            'rawSubFam_id' => $rawSubFam_id,
            'compSubFam_id' => $compSubFam_id,
            'purSpe_documentsRequested' => $documentsRequested,
            'purSpe_specifications' => $specifications,
            'id' => $id,
        ]);
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
                    'rawFam_nbrVersion' => $article->rawFam_nbrVersion + 1,
                ]);
            }
        } else if ($type === 'comp') {
            $article = CompFamily::all()->where('id', '==', $purSpec->compFam_id)->first();
            $signed = $article->compFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'compFam_nbrVersion' => $article->compFam_nbrVersion + 1,
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
            ]
        );
    }


    public function update_purSpe_subFam(Request $request, $type, $id)
    {
        $purSpec = PurchaseSpecification::all()->where('id', '==', $id)->first();
        $supplier = Supplier::all()->where('supplr_name', '==', $request->purSpe_supplier_id)->first();
        if ($type === 'cons') {
            $article = ConsSubFamily::all()->where('id', '==', $purSpec->consSubFam_id)->first();
            $signed = $article->consSubFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'consSubFam_nbrVersion' => $article->consSubFam_nbrVersion + 1,
                ]);
            }
        } else if ($type === 'raw') {
            $article = RawSubFamily::all()->where('id', '==', $purSpec->rawSubFam_id)->first();
            $signed = $article->rawSubFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'rawSubFam_nbrVersion' => $article->rawSubFam_nbrVersion + 1,
                ]);
            }
        } else if ($type === 'comp') {
            $article = CompSubFamily::all()->where('id', '==', $purSpec->compSubFam_id)->first();
            $signed = $article->compSubFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'compSubFam_nbrVersion' => $article->compSubFam_nbrVersion + 1,
                ]);
            }
        } else {
            return response()->json('error', 429);
        }
        $article->update([
            $type . 'SubFam_signatureDate' => null,
            $type . 'SubFam_qualityApproverId' => null,
            $type . 'SubFam_technicalReviewerId' => null,
        ]);
        $purSpec->update([
            'purSpe_validate' => $request->purSpe_validate,
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
            ]
        );
        return response()->json($purSpec);
    }


    public function update_purSpe_common(Request $request, $type, $id)
    {
        if ($type=='cons'){
            $article = ConsFamily::all()->where('id', '==', $id)->first();
            $signed = $article->consFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'consFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($type === 'raw') {
            $article = RawFamily::all()->where('id', '==', $id)->first();
            $signed = $article->rawFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'rawFam_nbrVersion' => $article->rawFam_nbrVersion + 1,
                ]);
            }
        } else if ($type === 'comp') {
            $article = CompFamily::all()->where('id', '==', $id)->first();
            $signed = $article->compFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'compFam_nbrVersion' => $article->compFam_nbrVersion + 1,
                ]);
            }
        } else {
            return response()->json('error', 429);
        }
        $article->update([
            $type . 'Fam_signatureDate' => null,
            $type . 'Fam_qualityApproverId' => null,
            $type . 'Fam_technicalReviewerId' => null,
            $type . 'Fam_specifications' => $request->purSpe_specification,
            $type . 'Fam_documentsRequested' => $request->purSpe_documentsRequest,
        ]);
        return response()->json($article);
    }
    
    public function update_purSpe_common_subFam(Request $request, $type, $id)
    {
        if ($type=='cons'){
            $article = ConsSubFamily::all()->where('id', '==', $id)->first();
            $signed = $article->consSubFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'consSubFam_nbrVersion' => $article->consSubFam_nbrVersion + 1,
                ]);
            }
        } else if ($type === 'raw') {
            $article = RawSubFamily::all()->where('id', '==', $id)->first();
            $signed = $article->rawSubFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'rawSubFam_nbrVersion' => $article->rawSubFam_nbrVersion + 1,
                ]);
            }
        } else if ($type === 'comp') {
            $article = CompSubFamily::all()->where('id', '==', $id)->first();
            $signed = $article->compSubFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'compSubFam_nbrVersion' => $article->compSubFam_nbrVersion + 1,
                ]);
            }
        } else {
            return response()->json('error', 429);
        }
        $article->update([
            $type . 'SubFam_signatureDate' => null,
            $type . 'SubFam_qualityApproverId' => null,
            $type . 'SubFam_technicalReviewerId' => null,
            $type . 'SubFam_specifications' => $request->purSpe_specification,
            $type . 'SubFam_documentsRequested' => $request->purSpe_documentsRequest,
        ]);
        return response()->json($article);
    }

    public function delete_purSpe($id){
        $purSpe=PurchaseSpecification::findOrFail($id);
        $purSpe->delete();
    }

    public function delete_purSpe_common($id, $type){
        if ($type=="comp"){
            $compFamily=CompFamily::findOrFail($id);
            $compFamily->update([
                'compFam_specifications' => null,
                'compFam_documentsRequested' => null,
            ]);
        }
        if ($type=="raw"){
            $rawFamily=RawFamily::findOrFail($id);
            $rawFamily->update([
                'rawFam_specifications' => null,
                'rawFam_documentsRequested' => null,
            ]);
        }
        if ($type=="cons"){
            $consFamily=ConsFamily::findOrFail($id);
            $consFamily->update([
                'consFam_specifications' => null,
                'consFam_documentsRequested' => null,
            ]);
        }
    }

    public function delete_purSpe_common_subFam($id, $type){
        if ($type=="comp"){
            $compSubFamily=CompSubFamily::findOrFail($id);
            $compSubFamily->update([
                'compSubFam_specifications' => null,
                'compSubFam_documentsRequested' => null,
            ]);
        }
        if ($type=="raw"){
            $rawSubFamily=RawSubFamily::findOrFail($id);
            $rawSubFamily->update([
                'rawSubFam_specifications' => null,
                'rawSubFam_documentsRequested' => null,
            ]);
        }
        if ($type=="cons"){
            $consSubFamily=ConsSubFamily::findOrFail($id);
            $consSubFamily->update([
                'consSubFam_specifications' => null,
                'consSubFam_documentsRequested' => null,
            ]);
        }
    }
}
