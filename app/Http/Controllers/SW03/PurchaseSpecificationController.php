<?php

/*
* Filename : PurchaseSpecificationController.php
* Creation date : 2 May 2023
* Update date : 2 May 2023
* This file is used to link the view files and the database that concern the purchase specification  table.
* For example : add a purchase specification in the data base, update a purchase specification...
*/

namespace App\Http\Controllers\SW03;

use App\Models\SW03\ConsFamily;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SW03\PurchaseSpecification;
use Illuminate\Support\Facades\DB;

class PurchaseSpecificationController extends Controller
{
    public function verif_purSpe(Request $request){
        $this->validate(
            $request,
            [
                'purSpe_requiredDoc' => 'max:255|String',
            ],
            [

                'purSpe_requiredDoc.max' => 'You must enter less than 255 characters ',
                'purSpe_requiredDoc.String' => 'You must enter a string ',
            ]
        );
    }

    /**
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for insert with the route : /cons/mb/add (post)
     * Add a new enregistrement of cons family member in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new cons family member
     */
    public function add_purSpe(Request $request, $id){
        //Creation of a new purchase specification
        $consFam_id=null ;
        $rawFam_id=null ;
        $compFam_id=null ;
        if ($request->artFam_type=="COMP"){
            $compFam_id=$id ;
        }
        if($request->artFam_type=="RAW"){
            $rawFam_id=$id ;
        }
        if($request->artFam_type=="CONS"){
                $consFam_id=$id ;
        }
        $purSpe=PurchaseSpecification::create([
            'purSpe_requiredDoc' => $request->purSpe_requiredDoc,
            'consFam_id' => $consFam_id,
            'rawFam_id' => $rawFam_id,
            'compFam_id' => $compFam_id,
            'purSpe_validate' => $request->purSpe_validate
        ]) ;

        $purSpe_id=$purSpe->id ;

        return response()->json($purSpe_id) ;
    }

    public function send_purSpes($type, $id) {
        $array = [];
        if ($type === 'cons') {
            $purSpecs = PurchaseSpecification::all()->where('consFam_id', '==', $id);
        } else if ($type === 'raw') {
            $purSpecs = PurchaseSpecification::all()->where('rawFam_id', '==', $id);
        } else if ($type === 'comp') {
            $purSpecs = PurchaseSpecification::all()->where('compFam_id', '==', $id);
        }
        foreach ($purSpecs as $purSpec) {
            array_push($array, [
                'id' => $purSpec->id,
                'purSpe_requiredDoc' => $purSpec->purSpe_requiredDoc,
                'purSpe_validate' => $purSpec->purSpe_validate,
                'consFam_id' => $purSpec->consFam_id,
                'rawFam_id' => $purSpec->rawFam_id,
                'compFam_id' => $purSpec->compFam_id,
            ]);
        }
        return response()->json($array);
    }

    public function update_purSpe(Request $request, $type, $id) {
        $purSpec = PurchaseSpecification::findOrfail($id);
        $article = null;
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
        }
        $article->update([
            $type.'Fam_signatureDate' => null,
            $type.'Fam_qualityApproverId' => null,
            $type.'Fam_technicalReviewerId' => null,
        ]);
        $purSpec->update([
            'purSpe_requiredDoc' => $request->purSpe_requiredDoc,
            'purSpe_validate' => $request->purSpe_validate,
            'purSpe_qualityApproverId' => null,
            'purSpe_technicalReviewerId' => null,
            'purSpe_signatureDate' => null,
        ]);
        return response()->json($purSpec);
    }
}
