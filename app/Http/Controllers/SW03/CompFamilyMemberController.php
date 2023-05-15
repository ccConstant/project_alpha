<?php


/*
* Filename : CompFamilyMemberController.php
* Creation date : 2 May 2023
* Update date : 2 May 2023
* This file is used to link the view files and the database that concern the comp family member table.
* For example : add a comp family member in the data base, update a comp family member...
*/

namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\CompFamilyMember;
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
                'artMb_dimension' => 'required|max:50|String',
                'artMb_sameValues' => 'required'
            ],
            [

                'artMb_dimension.required' => 'You must enter a dimension for your comp family member',
                'artMb_dimension.max' => 'You must enter less than 50 characters ',
                'artMb_dimension.String' => 'You must enter a string ',

                'artMb_sameValues.required' => 'You must enter a boolean for your comp family member',
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
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for insert with the route : /comp/mb/add (post)
     * Add a new enregistrement of comp family member in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new comp family member
     */
    public function add_compFamilyMember(Request $request, $id){
        //Creation of a new compFamMember
        $compFamilyMember=compFamilyMember::create([
            'compMb_dimension' => $request->artMb_dimension,
            'compFam_id' => $id,
            'compMb_sameValues' => $request->artMb_sameValues,
            'compMb_design' => $request->artMb_designation,
        ]) ;

        $compFamilyMember_id=$compFamilyMember->id ;

        return response()->json($compFamilyMember_id) ;
    }

    public function send_compFamilyMember($id) {
        $members = CompFamilyMember::all()->where('compFam_id', '==', $id);
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

    public function update_compFamilyMember(Request $request, $id) {
        $member = CompFamilyMember::all()->where('id', '==', $id)->first();
        $member->update([
            'compMb_dimension' => $request->artMb_dimension,
            'compMb_sameValues' => $request->artMb_sameValues,
            'compMb_design' => $request->artMb_designation,
        ]);
        return response()->json($member);
    }
}
