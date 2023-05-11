<?php

/*
* Filename : ConsFamilyMemberController.php
* Creation date : 2 May 2023
* Update date : 2 May 2023
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
                'artMb_dimension' => 'required|max:50|String',
                'artMb_sameValues' => 'required'
            ],
            [

                'artMb_dimension.required' => 'You must enter a dimension for your cons family member',
                'artMb_dimension.max' => 'You must enter less than 50 characters ',
                'artMb_dimension.String' => 'You must enter a string ',

                'artMb_sameValues.required' => 'You must enter a boolean for your cons family member',
            ]
        );
        if (!$request->artMb_sameValues) {
            $this->validate(
                $request,
                [
                    'artMb_designation' => 'required|max:255',
                ],
                [
                    'artMb_designation.required' => 'You must enter a designation for your comp family member',
                    'artMb_designation.max' => 'You must enter less than 255 characters ',
                ]
            );
        }
    }

    /**
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for insert with the route : /cons/mb/add (post)
     * Add a new enregistrement of cons family member in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new cons family member
     */
    public function add_consFamilyMember(Request $request, $id){
        //Creation of a new consFamMember
        $consFamilyMember=consFamilyMember::create([
            'consMb_dimension' => $request->artMb_dimension,
            'consFam_id' => $id,
            'consMb_sameValues' => $request->artMb_sameValues,
            'consMb_design' => $request->artMb_designation,
        ]) ;

        $consFamilyMember_id=$consFamilyMember->id ;

        return response()->json($consFamilyMember_id) ;
    }

    public function send_consFamilyMember($id) {
        $members = ConsFamilyMember::all()->where('consFam_id', '==', $id);
        $array = [];
        foreach ($members as $member) {
            array_push($array, [
                'id' => $member->id,
                'dimension' => $member->compMb_dimension,
                'sameValues' => $member->compMb_sameValues,
                'designation' => $member->compMb_design,
            ]);
        }
        return response()->json($array);
    }

    public function update_consFamilyMember(Request $request, $id) {
        $member = ConsFamilyMember::all()->where('id', '==', $id)->first();
        $member->update([
            'consMb_dimension' => $request->artMb_dimension,
            'consMb_sameValues' => $request->artMb_sameValues,
            'consMb_design' => $request->artMb_designation,
        ]);
        return response()->json($member);
    }
}
