<?php

/**
 * Filename : HistoryTest.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file is used to test the history table.
 * Coverage: 100%
 */

namespace Tests\Feature;

use App\Models\File;
use App\Models\History;
use App\Models\SW01\EnumPowerType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\Mme;
use App\Models\SW01\MmeTemp;
use App\Models\SW01\Power;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    public function create_user($name) {
        if (User::all()->count() == 0) {
            $countUser = User::all()->count();
            $response = $this->post('register', [
                'user_firstName' => $name,
                'user_lastName' => $name,
                'user_pseudo' => $name,
                'user_password' => 'VerifierVerifier',
                'user_confirmation_password' => 'VerifierVerifier',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countUser + 1, User::all());

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
        return User::all()->last()->id;
    }

    /*
     * Test Conception Number: 1
     * Add a new history for an equipment
     * Reason: "Update"
     * Expected result: The history is added to the database
     * @returns void
    */
    public function test_add_hist_equipment()
    {
        $user_id = $this->create_user('test');
        // Add a mass unit
        $response = $this->post('/equipment/enum/massUnit/add', [
            'value' => 'g'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the mass unit doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_massUnit' => 'The value of the field for the new equipment mass unit already exist in the data base'
            ]);
        }
        // Add an equipment type
        $response = $this->post('/equipment/enum/type/add', [
            'value' => 'internal'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the equipment type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_type' => 'The value of the field for the new equipment type already exist in the data base'
            ]);
        }
        // Add a new equipment
        $response = $this->post('equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'HistAddTest',
            'eq_externalReference' => 'HistAddTest',
            'eq_name' => 'HistAddTest',
            'eq_serialNumber' => 'HistAddTest',
            'eq_constructor' => 'HistAddTest',
            'eq_set' => 'HistAddTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'HistAddTest',
            'eq_mobility' => true,
            'eq_type' => 'internal',
            'eq_location' => 'HistAddTest',
            'createdBy_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response = $this->post('equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'HistAddTest',
            'eq_externalReference' => 'HistAddTest',
            'eq_name' => 'HistAddTest',
            'eq_serialNumber' => 'HistAddTest',
            'eq_constructor' => 'HistAddTest',
            'eq_set' => 'HistAddTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'HistAddTest',
            'eq_mobility' => true,
            'eq_type' => 'internal',
            'eq_location' => 'HistAddTest',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        // Add the power type
        $response = $this->post('/power/enum/type/add', [
            'value' => 'Electric',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response = $this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower + 1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->last()->id
        ]);
        // Technical and quality verification
        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        // Add a new power type
        $response = $this->post('/power/enum/type/add', [
            'value' => 'Example'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Example'
        ]);
        $actualVersion = EquipmentTemp::all()->where('eqTemp_remarks', '==', 'HistAddTest')->last()->eqTemp_version;
        $this->assertEquals($user_id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'HistAddTest')->last()->qualityVerifier_id);
        $this->assertEquals($user_id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'HistAddTest')->last()->technicalVerifier_id);
        // Update the power type
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Example',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response = $this->post('/equipment/update/pow/' . Power::all()->last()->id, [
            'pow_validate' => 'validated',
            'pow_type' => 'Example',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        // Verification on database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->last()->id
        ]);
        $this->assertEquals($countPower, Power::all()->count());
        $this->assertEquals($actualVersion + 1, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'HistAddTest')->last()->eqTemp_version);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'HistAddTest')->last()->qualityVerifier_id);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'HistAddTest')->last()->technicalVerifier_id);
        $this->assertEquals(0, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'HistAddTest')->last()->eqTemp_lifeSheetCreated);
        $countHistory = History::all()->count();
        $response = $this->post('/history/add/equipment/' . Equipment::all()->last()->id, [
            'history_reasonUpdate' => 'Update'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countHistory + 1, History::all()->count());
    }

    /**
     * Test Conception Number: 2
     * Add a new history for a mme
     * Reason: "Update"
     * Expected result: The history is added to the database
     * @returns void
     */
    public function test_add_hist_mme()
    {
        $user_id = $this->create_user('test');
        $countMme = Mme::all()->count();
        $response = $this->post('mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'HistAddTestM',
            'mme_externalReference' => 'HistAddTestM',
            'mme_name' => 'HistAddTestM',
            'mme_serialNumber' => 'HistAddTestM',
            'mme_constructor' => 'HistAddTestM',
            'mme_remarks' => 'HistAddTestM',
            'mme_set' => 'HistAddTestM',
            'mme_location' => 'HistAddTestM',
            'createdBy_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response = $this->post('/mme/add', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'HistAddTestM',
            'mme_externalReference' => 'HistAddTestM',
            'mme_name' => 'HistAddTestM',
            'mme_serialNumber' => 'HistAddTestM',
            'mme_constructor' => 'HistAddTestM',
            'mme_remarks' => 'HistAddTestM',
            'mme_set' => 'HistAddTestM',
            'mme_location' => 'HistAddTestM',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countMme + 1, Mme::all()->count());
        $Mme = Mme::all()->where('mme_internalReference', '==', 'HistAddTestM')->last();
        // validation
        $response = $this->post('/mme/validation/' . $Mme->id, [
            'reason' => 'technical',
            'enteredBy_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/validation/' . $Mme->id, [
            'reason' => 'quality',
            'enteredBy_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $this->assertEquals($user_id, MmeTemp::all()->where('mme_id', '==', $Mme->id)->last()->qualityVerifier_id);
        $this->assertEquals($user_id, MmeTemp::all()->where('mme_id', '==', $Mme->id)->last()->technicalVerifier_id);
        // Add a file to the equipment
        $response = $this->post('file/verif', [
            'file_validate' => 'drafted',
            'file_name' => 'File',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/mme/add/file/', [
            'file_validate' => 'drafted',
            'mme_id' => $Mme->id,
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile + 1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_validate' => 'drafted',
            'mmeTemp_id' => $Mme->id
        ]);
        // Test
        $response->assertStatus(200);
        print MmeTemp::all()->where('mme_id', '==', $Mme->id)->last()->mmeTemp_version;
        $countHistory = History::all()->count();
        $response = $this->post('/history/add/mme/' . Mme::all()->last()->id, [
            'history_reasonUpdate' => 'Update'
        ]);
        $this->assertEquals($countHistory + 1, History::all()->count());
    }

    /**
     * Test Conception Number: 3
     * Consult the list of history of the previous equipment
     * Expected result: The history list is correct
     * @returns void
     */
    public function test_consult_hist()
    {
        $this->test_add_hist_equipment();
        $response = $this->get('/history/send/equipment/' . Equipment::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'history_numVersion' => 1,
                'history_reasonUpdate' => 'Update',
                'history_date' => Carbon::now()->format('d M o')
            ]
        ]);
    }

    /**
     * Test Conception Number: 4
     * Consult the list of history of the previous mme
     * Expected result: The history list is correct
     * @returns void
     */
    public function test_consult_hist_mme()
    {
        $this->test_add_hist_mme();
        $response = $this->get('/history/send/mme/' . Mme::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'history_numVersion' => 1,
                'history_reasonUpdate' => 'Update',
                'history_date' => Carbon::now()->format('d M o')
            ]
        ]);
    }
}
