<?php


/*
* Filename : CompFamilyMemberController.php
* Creation date : 2 May 2023
* Update date : 5 Jul 2023
* This file is used to link the view files and the database that concern the comp family member table.
* For example : add a comp family member in the data base, update a comp family member...
*/

namespace App\Http\Controllers\SW03;

use App\Models\SW03\Criticality;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\PurchaseSpecification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\CompFamilyMember;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CompFamilyMemberController extends Controller
{
    /**
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for check data with the route : /comp/mb/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_compFamilyMember(Request $request){
        $this->validate(
            $request,
            [
                'artMb_ref' => 'required|max:50|String',
                'artMb_designation' => 'required|max:255',
            ],
            [

                'artMb_ref.required' => 'You must enter a reference for your comp family member',
                'artMb_ref.max' => 'You must enter less than 50 characters ',
                'artMb_ref.String' => 'You must enter a string ',
                'artMb_designation.required' => 'You must enter a designation for your comp family member',
                'artMb_designation.max' => 'You must enter less than 255 characters ',
            ]
        );
    }

    /**
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for insert with the route : /comp/mb/add (post)
     * Add a new enregistrement of comp family member in the data base with the informations entered in the form
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_compFamilyMember(Request $request, $id){
        //Creation of a new compFamMember
        $compFamilyMember=compFamilyMember::create([
            'compMb_ref' => $request->artMb_ref,
            'compSubFam_id' => $id,
            'compMb_design' => $request->artMb_designation,
            'compMb_validate' => $request->artMb_validate,
        ]) ;

        $compFamilyMember_id=$compFamilyMember->id ;

        return response()->json($compFamilyMember_id) ;
    }

    /**
     * Function call by ArticleSubFamilyForm.vue with the route : /comp/mb/send (post)
     * Get all the family comp member corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_compFamilyMember($id) {
        $members = CompFamilyMember::all()->where('compSubFam_id', '==', $id);
        $array = [];
        foreach ($members as $member) {
            array_push($array, [
                'id' => $member->id,
                'reference' => $member->compMb_ref,
                'designation' => $member->compMb_design,
                'validate' => $member->compMb_validate,
            ]);
        }
        return response()->json($array);
    }

    /**
     * Function call by ArticleUpdate.vue when the form is submitted for update with the route :/comp/mb/update/{id} (post)
     * Update an enregistrement of comp family member in the data base with the informations entered in the form
     * The id parameter correspond to the id of the comp family member we want to update
     * */
    public function update_compFamilyMember(Request $request, $id)
    {
        $member = CompFamilyMember::all()->where('id', '==', $id)->first();
        $member->update([
            'compMb_ref' => $request->artMb_ref,
            'compMb_design' => $request->artMb_designation,
            'compMb_validate' => $request->artMb_validate,
        ]);
        /*$fam = CompFamily::all()->where('id', '==', $member->compFam_id)->first();
        if ($fam->compFam_signatureDate != null) {
            $fam->update([
                'compFam_nbrVersion' => $fam->compFam_nbrVersion + 1,
            ]);
        }
        $fam->update([
            'compFam_signatureDate' => null,
            'compFam_qualityApproverId' => null,
            'compFam_technicalReviewerId' => null,
        ]);*/
        return response()->json($member);
    }
}
