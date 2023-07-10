<?php

/*
* Filename : ConsFamilyMemberController.php
* Creation date : 2 May 2023
* Update date : 5 Jul 2023
* This file is used to link the view files and the database that concern the cons family member table.
* For example : add a cons family member in the data base, update a cons family member...
*/

namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\ConsFamilyMember;
use Illuminate\Support\Facades\DB;

class ConsFamilyMemberController extends Controller
{
    /**
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for check data with the route : /cons/mb/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_consFamilyMember(Request $request){
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
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for insert with the route : /cons/mb/add (post)
     * Add a new enregistrement of cons family member in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new cons family member
     */
    public function add_consFamilyMember(Request $request, $id){
        //Creation of a new consFamMember
        $consFamilyMember=consFamilyMember::create([
            'consMb_ref' => $request->artMb_ref,
            'consSubFam_id' => $id,
            'consMb_design' => $request->artMb_designation,
            'consMb_validate' => $request->artMb_validate,
        ]) ;

        $consFamilyMember_id=$consFamilyMember->id ;

        return response()->json($consFamilyMember_id) ;
    }

    /**
     * Function call by ArticleSubFamilyForm.vue with the route : /cons/mb/send (post)
     * Get all the family cons member corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_consFamilyMember($id) {
        $members = ConsFamilyMember::all()->where('consSubFam_id', '==', $id);
        $array = [];
        foreach ($members as $member) {
            array_push($array, [
                'id' => $member->id,
                'reference' => $member->consMb_ref,
                'designation' => $member->consMb_design,
            ]);
        }
        return response()->json($array);
    }

    /**
     * Function call by ArticleUpdate.vue when the form is submitted for update with the route :/cons/mb/update/{id} (post)
     * Update an enregistrement of cons family member in the data base with the informations entered in the form
     * The id parameter correspond to the id of the cons family member we want to update
     * */
    public function update_consFamilyMember(Request $request, $id) {
        $member = ConsFamilyMember::all()->where('id', '==', $id)->first();
        $member->update([
            'consMb_ref' => $request->artMb_ref,
            'consMb_design' => $request->artMb_designation,
            'consMb_validate' => $request->artMb_validate,
        ]);
        /*$fam = ConsFamily::all()->where('id', '==', $member->consFam_id)->first();
        if ($fam->consFam_signatureDate != null) {
            $fam->update([
                'consFam_nbrVersion' => $fam->consFam_nbrVersion + 1,
            ]);
        }
        $fam->update([
            'consFam_signatureDate' => null,
            'consFam_qualityApproverId' => null,
            'consFam_technicalReviewerId' => null,
        ]);*/
        return response()->json($member);
    }
}
