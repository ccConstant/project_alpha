<?php

/*
* Filename : RawFamilyMemberController.php
* Creation date : 2 May 2023
* Update date : 6 Jul 2023
* This file is used to link the view files and the database that concern the raw family member table.
* For example : add a raw family member in the data base, update a raw family member...
*/

namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SW03\RawFamily;
use App\Models\SW03\RawFamilyMember;
use Illuminate\Support\Facades\DB;

class RawFamilyMemberController extends Controller
{
    /**
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for check data with the route : /raw/mb/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_rawFamilyMember(Request $request){

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
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for insert with the route : /raw/mb/add (post)
     * Add a new enregistrement of raw family member in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new raw family member
     */
    public function add_rawFamilyMember(Request $request, $id){
        //Creation of a new rawFamMember
        $rawFamilyMember=rawFamilyMember::create([
            'rawMb_ref' => $request->artMb_ref,
            'rawSubFam_id' => $id,
            'rawMb_design' => $request->artMb_designation,
            'rawMb_validate' => $request->artMb_validate,
        ]) ;

        $rawFamilyMember_id=$rawFamilyMember->id ;

        return response()->json($rawFamilyMember_id) ;
    }

    /**
     * Function call by ArticleSubFamilyForm.vue with the route : /raw/mb/send (post)
     * Get all the family raw member corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_rawFamilyMember($id) {
        $members = RawFamilyMember::all()->where('rawSubFam_id', '==', $id);
        $array = [];
        foreach ($members as $member) {
            array_push($array, [
                'id' => $member->id,
                'reference' => $member->rawMb_ref,
                'designation' => $member->rawMb_design,
            ]);
        }
        return response()->json($array);
    }

    /**
     * Function call by ArticleUpdate.vue when the form is submitted for update with the route :/raw/mb/update/{id} (post)
     * Update an enregistrement of raw family member in the data base with the informations entered in the form
     * The id parameter correspond to the id of the raw family member we want to update
     * */
    public function update_rawFamilyMember(Request $request, $id) {
        $member = RawFamilyMember::all()->where('id', '==', $id)->first();
        $member->update([
            'rawMb_ref' => $request->artMb_ref,
            'rawMb_design' => $request->artMb_designation,
            'rawMb_validate' => $request->artMb_validate,
        ]);
        /*$fam = RawFamily::all()->where('id', '==', $member->rawFam_id)->first();
        if ($fam->rawFam_signatureDate != null) {
            $fam->update([
                'rawFam_nbrVersion' => $fam->rawFam_nbrVersion + 1,
            ]);
        }
        $fam->update([
            'rawFam_signatureDate' => null,
            'rawFam_qualityApproverId' => null,
            'rawFam_technicalReviewerId' => null,
        ]);*/
        return response()->json($member);
    }

    public function delete_rawFamilyMember($id)
    {
        $member = RawFamilyMember::all()->where('id', '==', $id)->first();
        $member->delete();
    }
}
