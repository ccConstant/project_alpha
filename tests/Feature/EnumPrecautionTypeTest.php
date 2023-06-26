<?php

namespace Tests\Feature;

use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\EnumPrecautionType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\Precaution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EnumPrecautionTypeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test Conception Number: 1
     * Try to add a non-existent type in the database
     * Type: Type
     * Expected result: The type is correctly added to the database
     * @return void
     */
    public function test_add_non_existent_type() {
        $oldCOunt = EnumPrecautionType::all()->count();
        $response = $this->post('/precaution/enum/type/add', [
            'value' => 'Type'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumPrecautionType::all()->count(), $oldCOunt+1);
    }

    /**
     * Test Conception Number: 2
     * Try to add two time the same type in the database
     * Type: Exist
     * Expected result: Receiving an error:
     *                                      "The value of the field for the new precaution type already exist in the data base"
     * @return void
     */
    public function test_add_two_time_same_type() {
        $oldCOunt = EnumPrecautionType::all()->count();
        $response = $this->post('/precaution/enum/type/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumPrecautionType::all()->count(), $oldCOunt+1);
        $response = $this->post('/precaution/enum/type/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_prctn_type' => [
                "The value of the field for the new precaution type already exist in the data base"
            ]
        ]);
        $this->assertEquals(EnumPrecautionType::all()->count(), $oldCOunt+1);
    }

    public function requiredForTest() {
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'g')->count() === 0) {
            $countEqMassUnit=EnumEquipmentMassUnit::all()->count();
            $response=$this->post('/equipment/enum/massUnit/add', [
                'value' => 'g',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit+1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'Balance')->count() === 0) {
            $countEqType=EnumEquipmentType::all()->count();
            $response=$this->post('/equipment/enum/type/add', [
                'value' => 'Balance',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType+1, EnumEquipmentType::all());
        }
        if (EnumPrecautionType::all()->where('value', '=', 'Type')->count() === 0) {
            $countPrecautionType=EnumPrecautionType::all()->count();
            $response=$this->post('/precaution/enum/type/add', [
                'value' => 'Type',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countPrecautionType+1, EnumPrecautionType::all());
        }
        if (EnumPrecautionType::all()->where('value', '=', 'Exist')->count() === 0) {
            $countPrecautionType=EnumPrecautionType::all()->count();
            $response=$this->post('/precaution/enum/type/add', [
                'value' => 'Exist',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countPrecautionType+1, EnumPrecautionType::all());
        }
        // Add the user to validate the equipment
        if (User::all()->where('user_firstName', '=', 'Verifier')->count() === 0) {
            $countUser=User::all()->count();
            $response=$this->post('register', [
                'user_firstName' => 'Verifier',
                'user_lastName' => 'Verifier',
                'user_pseudo' => 'Verifier',
                'user_password' => 'VerifierVerifier',
                'user_confirmation_password' => 'VerifierVerifier',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countUser+1, User::all());
            User::all()->last()->update([
                'user_menuUserAcessRight' => 1,
                'user_resetUserPasswordRight' => 1,
                'user_updateDataInDraftRight' => 1,
                'user_validateDescriptiveLifeSheetDataRight' => 1,
                'user_validateOtherDataRight' => 1,
                'user_updateDataValidatedButNotSignedRight' => 1,
                'user_updateDescriptiveLifeSheetDataSignedRight' => 1,
                'user_makeQualityValidationRight' => 1,
                'user_makeTechnicalValidationRight' => 1,
                'user_deleteDataNotValidatedLinkedToEqOrMmeRight' => 1,
                'user_deleteDataValidatedLinkedToEqOrMmeRight' => 1,
                'user_deleteDataSignedLinkedToEqOrMmeRight' => 1,
                'user_deleteEqOrMmeRight' => 1,
                'user_makeReformRight' => 1,
                'user_declareNewStateRight' => 1,
                'user_updateEnumRight' => 1,
                'user_deleteEnumRight' => 1,
                'user_addEnumRight' => 1,
                'user_updateInformationRight' => 1,
                'user_makeEqOpValidationRight' => 1,
                'user_personTrainedToGeneralPrinciplesOfEqManagementRight' => 1,
                'user_makeEqRespValidationRight' => 1,
                'user_personTrainedToGeneralPrinciplesOfMMEManagementRight' => 1,
                'user_makeMmeOpValidationRight' => 1,
                'user_makeMmeRespValidationRight' => 1,
            ]);
        }
    }
}
