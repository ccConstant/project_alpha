<?php

namespace Tests\Feature;

use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\PreventiveMaintenanceOperation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreventiveMaintenanceOperationTest extends TestCase
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

    public function create_equipment($name, $validated = 'drafted')
    {
        $user_id = $this->create_user('test');

        if (EnumEquipmentMassUnit::all()->where('value', '=', $name)->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => $name,
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', $name)->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => $name,
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $response = $this->post('/equipment/verif', [
            'eq_validate' => $validated,
            'eq_internalReference' => $name,
            'eq_externalReference' => $name,
            'eq_name' => $name,
            'eq_serialNumber' => $name,
            'eq_constructor' => $name,
            'eq_mass' => 1234,
            'eq_remarks' => $name,
            'eq_set' => $name,
            'eq_location' => $name,
            'eq_type' => $name,
            'eq_massUnit' => $name,
            'createdBy_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
            'eq_validate' => $validated,
            'eq_internalReference' => $name,
            'eq_externalReference' => $name,
            'eq_name' => $name,
            'eq_serialNumber' => $name,
            'eq_constructor' => $name,
            'eq_mass' => 1234,
            'eq_remarks' => $name,
            'eq_set' => $name,
            'eq_location' => $name,
            'eq_type' => $name,
            'eq_massUnit' => $name,
            'eq_mobility' => '0'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => '1',
            'eqTemp_location' => $name,
            'eqTemp_validate' => $validated,
            'eqTemp_lifeSheetCreated' => '0',
            'eqTemp_mass' => '1234',
            'eqTemp_remarks' => $name,
            'eqTemp_mobility' => '0',
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', $name)->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', $name)->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 1
     * Add new preventive maintenance as drafted with no values
     * Description: /
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a description for your preventive maintenance operation"
     * @returns void
     */
    public function test_add_preventive_maintenance_drafted_no_values()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You must enter a description for your preventive maintenance operation',
        ]);
    }

    /**
     * Test Conception Number: 2
     * Add new preventive maintenance as drafted with too short description
     * Description: "in"
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least three characters"
     * @returns void
     */
    public function test_add_preventive_maintenance_drafted_too_short_desc()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'in',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You must enter at least three characters',
        ]);
    }

    /**
     * Test Conception Number: 3
     * Add new preventive maintenance as drafted with too long description
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_preventive_maintenance_drafted_too_long_desc()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 4
     * Add new preventive maintenance as drafted with too long periodicty
     * Description: "three"
     * Protocol: /
     * Periodicity: 12345
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 4 characters"
     * @returns void
     */
    public function test_add_preventive_maintenance_drafted_too_long_period()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_periodicity' => 12345,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_periodicity' => 'You must enter a maximum of 4 characters',
        ]);
    }

    /**
     * Test Conception Number: 5
     * Add new preventive maintenance as drafted with correct values
     * Description: "three"
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: The preventive maintenance is added to the database
     * @returns void
     */
    public function test_add_preventive_maintenance_drafted_correct_values()
    {
        $this->create_equipment("three");
        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'drafted',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_description' => 'three',
        ]);
    }

    /**
     * Test Conception Number: 6
     * Add new preventive maintenance as to be validated with no values
     * Description: /
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a description for your preventive maintenance operation"
     * @returns void
     */
    public function test_add_preventive_maintenance_to_be_validated_no_values()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'to_be_validated',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You must enter a description for your preventive maintenance operation',
        ]);
    }

    /**
     * Test Conception Number: 7
     * Add new preventive maintenance as to be validated with too short description
     * Description: "in"
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least three characters"
     * @returns void
     */
    public function test_add_preventive_maintenance_to_be_validated_too_short_desc()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'to_be_validated',
            'prvMtnOp_description' => 'in',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You must enter at least three characters',
        ]);
    }

    /**
     * Test Conception Number: 8
     * Add new preventive maintenance as to be validated with too long description
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_preventive_maintenance_to_be_validated_too_long_desc()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'to_be_validated',
            'prvMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 9
     * Add new preventive maintenance as to be validated with too long periodicity
     * Description: "three"
     * Protocol: /
     * Periodicity: 12345
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 4 characters"
     * @returns void
     */
    public function test_add_preventive_maintenance_to_be_validated_too_long_periodicity()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'to_be_validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_periodicity' => 12345,
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_periodicity' => 'You must enter a maximum of 4 characters',
        ]);
    }

    /**
     * Test Conception Number: 10
     * Add new preventive maintenance as to be validated with correct values
     * Description: "three"
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: The preventive maintenance is added to the database
     * @returns void
     */
    public function test_add_preventive_maintenance_to_be_validated_correct_values()
    {
        $this->create_equipment("three");
        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'to_be_validated',
            'prvMtnOp_description' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'to_be_validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_description' => 'three',
        ]);
    }

    /**
     * Test Conception Number: 11
     * Add new preventive maintenance as validated with no values
     * Description: /
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a description for your preventive maintenance operation"
     *                                      "You must enter a protocol for your preventive maintenance operation"
     * @returns void
     */
    public function test_add_preventive_maintenance_validated_no_values()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You must enter a description for your preventive maintenance operation',
            'prvMtnOp_protocol' => 'You must enter a protocol for your preventive maintenance operation',
        ]);
    }

    /**
     * Test Conception Number: 12
     * Add new preventive maintenance as validated with too short description
     * Description: "in"
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least three characters"
     *                                      "You must enter a protocol for your preventive maintenance operation"
     * @returns void
     */
    public function test_add_preventive_maintenance_validated_too_short_desc()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'in',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You must enter at least three characters',
            'prvMtnOp_protocol' => 'You must enter a protocol for your preventive maintenance operation',
        ]);
    }

    /**
     * Test Conception Number: 13
     * Add new preventive maintenance as validated with too long description
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     *                                      "You must enter a protocol for your preventive maintenance operation"
     * @returns void
     */
    public function test_add_preventive_maintenance_validated_too_long_desc()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You must enter a maximum of 255 characters',
            'prvMtnOp_protocol' => 'You must enter a protocol for your preventive maintenance operation',
        ]);
    }

    /**
     * Test Conception Number: 14
     * Add new preventive maintenance as validated with too short protocol
     * Description: "three"
     * Protocol: "in"
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     * @returns void
     */
    public function test_add_preventive_maintenance_validated_too_short_protocol()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'in',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_protocol' => 'You must enter at least three characters',
        ]);
    }

    /**
     * Test Conception Number: 15
     * Add new preventive maintenance as validated with too long protocol
     * Description: "three"
     * Protocol: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_preventive_maintenance_validated_too_long_protocol()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_protocol' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 16
     * Add new preventive maintenance as validated with too long periodicity
     * Description: "three"
     * Protocol: "three"
     * Periodicity: 12345
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 4 characters"
     * @returns void
     */
    public function test_add_preventive_maintenance_validated_too_long_periodicity()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 12345,
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_periodicity' => 'You must enter a maximum of 4 characters',
        ]);
    }

    /**
     * Test Conception Number: 17
     * Add new preventive maintenance as validated with correct values
     * Description: "three"
     * Protocol: "three"
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: The preventive maintenance is added to the database
     * @returns void
     */
    public function test_add_preventive_maintenance_validated_correct_values()
    {
        $this->create_equipment("three");
        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => false,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
        ]);
    }

    /**
     * Test Conception Number: 18
     * Add new preventive maintenance as validated with no values
     * Description: /
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a description for your preventive maintenance operation"
     *                                      "You must enter a protocol for your preventive maintenance operation"
     *                                      "You must enter a periodicity for your preventive maintenance operation"
     *                                      "You must enter a periodicity symbol for your preventive maintenance operation"
     * @returns void
     */
    public function test_add_redundant_preventive_maintenance_validated_no_values()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You must enter a description for your preventive maintenance operation',
            'prvMtnOp_protocol' => 'You must enter a protocol for your preventive maintenance operation',
            'prvMtnOp_periodicity' => 'You must enter a periodicity for your preventive maintenance operation',
            'prvMtnOp_symbolPeriodicity' => 'You must enter a periodicity symbol for your preventive maintenance operation',
        ]);
    }

    /**
     * Test Conception Number: 19
     * Add new preventive maintenance as validated with too short description
     * Description: "in"
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least three characters"
     *                                      "You must enter a protocol for your preventive maintenance operation"
     *                                      "You must enter a periodicity for your preventive maintenance operation"
     *                                      "You must enter a periodicity symbol for your preventive maintenance operation"
     * @returns void
     */
    public function test_add_redundant_preventive_maintenance_validated_too_short_desc()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'in',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You must enter at least three characters',
            'prvMtnOp_protocol' => 'You must enter a protocol for your preventive maintenance operation',
            'prvMtnOp_periodicity' => 'You must enter a periodicity for your preventive maintenance operation',
            'prvMtnOp_symbolPeriodicity' => 'You must enter a periodicity symbol for your preventive maintenance operation',
        ]);
    }

    /**
     * Test Conception Number: 20
     * Add new preventive maintenance as validated with too long description
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Protocol: /
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     *                                      "You must enter a protocol for your preventive maintenance operation"
     *                                      "You must enter a periodicity for your preventive maintenance operation"
     *                                      "You must enter a periodicity symbol for your preventive maintenance operation"
     * @returns void
     */
    public function test_add_redundant_preventive_maintenance_validated_too_long_desc()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You must enter a maximum of 255 characters',
            'prvMtnOp_protocol' => 'You must enter a protocol for your preventive maintenance operation',
            'prvMtnOp_periodicity' => 'You must enter a periodicity for your preventive maintenance operation',
            'prvMtnOp_symbolPeriodicity' => 'You must enter a periodicity symbol for your preventive maintenance operation',
        ]);
    }

    /**
     * Test Conception Number: 21
     * Add new preventive maintenance as validated with too short protocol
     * Description: "three"
     * Protocol: "in"
     * Periodicity: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     *                                      "You must enter a periodicity for your preventive maintenance operation"
     *                                      "You must enter a periodicity symbol for your preventive maintenance operation"
     * @returns void
     */
    public function test_add_redundant_preventive_maintenance_validated_too_short_protocol()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'in',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_protocol' => 'You must enter at least three characters',
            'prvMtnOp_periodicity' => 'You must enter a periodicity for your preventive maintenance operation',
            'prvMtnOp_symbolPeriodicity' => 'You must enter a periodicity symbol for your preventive maintenance operation',
        ]);
    }

    /**
     * Test Conception Number: 22
     * Add new preventive maintenance as validated with too long periodicity
     * Description: "three"
     * Protocol: "three"
     * Periodicity: 12345
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 4 characters"
     *                                      "You must enter a periodicity symbol for your preventive maintenance operation"
     * @returns void
     */
    public function test_add_redundant_preventive_maintenance_validated_too_long_periodicity()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 12345,
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOp_periodicity' => 'You must enter a maximum of 4 characters',
            'prvMtnOp_symbolPeriodicity' => 'You must enter a periodicity symbol for your preventive maintenance operation',
        ]);
    }

    /**
     * Test Conception Number: 23
     * Add new preventive maintenance as validated with correct description, protocol, periodicity and periodicity symbol
     * Description: "three"
     * Protocol: "three"
     * Periodicity: 7
     * Symbol Periodicity: "D"
     * Expected Result: The preventive maintenance is added to the database
     * @returns void
     */
    public function test_add_redundant_preventive_maintenance_validated_correct()
    {
        $this->create_equipment("three");

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '72hours',
            'prvMtnOp_protocol' => '72hours',
            'prvMtnOp_periodicity' => 72,
            'prvMtnOp_symbolPeriodicity' => 'H',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '72hours',
            'prvMtnOp_protocol' => '72hours',
            'prvMtnOp_periodicity' => 72,
            'prvMtnOp_symbolPeriodicity' => 'H',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => '72hours',
            'prvMtnOp_protocol' => '72hours',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 72,
            'prvMtnOp_symbolPeriodicity' => 'H',
            'prvMtnOp_nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addHours(72)->format('Y-m-d H:i:s')
        ]);

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '7days',
            'prvMtnOp_protocol' => '7days',
            'prvMtnOp_periodicity' => 7,
            'prvMtnOp_symbolPeriodicity' => 'D',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '7days',
            'prvMtnOp_protocol' => '7days',
            'prvMtnOp_periodicity' => 7,
            'prvMtnOp_symbolPeriodicity' => 'D',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => '7days',
            'prvMtnOp_protocol' => '7days',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 7,
            'prvMtnOp_symbolPeriodicity' => 'D',
            'prvMtnOp_nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addDays(7)->format('Y-m-d H:i:s')
        ]);

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '1month',
            'prvMtnOp_protocol' => '1month',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '1month',
            'prvMtnOp_protocol' => '1month',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => '1month',
            'prvMtnOp_protocol' => '1month',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addMonth()->format('Y-m-d H:i:s')
        ]);

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '1year',
            'prvMtnOp_protocol' => '1year',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'Y',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '1year',
            'prvMtnOp_protocol' => '1year',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'Y',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => '1year',
            'prvMtnOp_protocol' => '1year',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'Y',
            'prvMtnOp_nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addYear()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Test Conception Number: 24
     * Add new preventive maintenance with a too high value of periodicity and with a periodicity symbol equal to "D"
     * Description: "three"
     * Protocol: "three"
     * Periodicity: 5999
     * Symbol Periodicity: "D"
     * Expected Result: The preventive maintenance is added to the database
     * @returns void
     */
    public function test_add_redundant_preventive_maintenance_validated_too_high_periodicity_day()
    {
        $this->create_equipment("three");
        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 5999,
            'prvMtnOp_symbolPeriodicity' => 'D',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_periodicity' => 'You can\'t enter a periodicity higher than 5475 days',
        ]);
    }

    /**
     * Test Conception Number: 25
     * Add new preventive maintenance with a too high value of periodicity and with a periodicity symbol equal to "M"
     * Description: "three"
     * Protocol: "three"
     * Periodicity: 199
     * Symbol Periodicity: "M"
     * Expected Result: The preventive maintenance is added to the database
     * @returns void
     */
    public function test_add_redundant_preventive_maintenance_validated_too_high_periodicity_month()
    {
        $this->create_equipment("three");
        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 199,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_periodicity' => 'You can\'t enter a periodicity higher than 180 months',
        ]);
    }

    /**
     * Test Conception Number: 26
     * Add new preventive maintenance with a too high value of periodicity and with a periodicity symbol equal to "Y"
     * Description: "three"
     * Protocol: "three"
     * Periodicity: 20
     * Symbol Periodicity: "Y"
     * Expected Result: The preventive maintenance is added to the database
     * @returns void
     */
    public function test_add_redundant_preventive_maintenance_validated_too_high_periodicity_year()
    {
        $this->create_equipment("three");
        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 20,
            'prvMtnOp_symbolPeriodicity' => 'Y',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_periodicity' => 'You can\'t enter a periodicity higher than 15 years',
        ]);
    }

    /**
     * Test Conception Number: 27
     * Add new preventive maintenance with correct values to a signed equipment
     * Description: "signed"
     * Protocol: "signed"
     * Periodicity: 1
     * Symbol Periodicity: "M"
     * Expected Result: The preventive maintenance is added to the database and the equipment is no more signed
     * @returns void
     */
    public function test_add_preventive_maintenance_validated_to_signed_equipment()
    {
        $this->create_equipment("three", 'validated');

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

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'signed',
            'prvMtnOp_protocol' => 'signed',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'signed',
            'prvMtnOp_protocol' => 'signed',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'signed',
            'prvMtnOp_protocol' => 'signed',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 28
     * Update a preventive maintenance with correct values
     * Description: "signed"
     * Protocol: "signed"
     * Periodicity: 1
     * Symbol Periodicity: "M"
     * Expected Result: The preventive maintenance is updated in the database
     * @returns void
     */
    public function test_update_preventive_maintenance_validated()
    {
        $this->create_equipment("three");
        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
        ]);
        $response = $this->post('/equipment/update/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '72hours',
            'prvMtnOp_protocol' => '72hours',
            'prvMtnOp_periodicity' => 72,
            'prvMtnOp_symbolPeriodicity' => 'H',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '72hours',
            'prvMtnOp_protocol' => '72hours',
            'prvMtnOp_periodicity' => 72,
            'prvMtnOp_symbolPeriodicity' => 'H',
            'prvMtnOp_preventiveOperation' => true,
        ]);

        $response = $this->post('/equipment/update/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '7days',
            'prvMtnOp_protocol' => '7days',
            'prvMtnOp_periodicity' => 7,
            'prvMtnOp_symbolPeriodicity' => 'D',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '7days',
            'prvMtnOp_protocol' => '7days',
            'prvMtnOp_periodicity' => 7,
            'prvMtnOp_symbolPeriodicity' => 'D',
            'prvMtnOp_preventiveOperation' => true,
        ]);

        $response = $this->post('/equipment/update/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '1month',
            'prvMtnOp_protocol' => '1month',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '1month',
            'prvMtnOp_protocol' => '1month',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
        ]);

        $response = $this->post('/equipment/update/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '1year',
            'prvMtnOp_protocol' => '1year',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'Y',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '1year',
            'prvMtnOp_protocol' => '1year',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'Y',
            'prvMtnOp_preventiveOperation' => true,
        ]);
    }

    /**
     * Test Conception Number: 29
     * Update a preventive maintenance with correct values of a signed equipment
     * Description: "signed"
     * Protocol: "signed"
     * Periodicity: 1
     * Symbol Periodicity: "M"
     * Expected Result: The preventive maintenance is updated in the database
     * @returns void
     */
    public function test_update_preventive_maintenance_validated_to_signed_equipment()
    {
        $this->create_equipment("three", 'validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
        ]);

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
        $response = $this->post('/equipment/update/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'prvMtnOp_preventiveOperation' => true,
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'signed',
            'prvMtnOp_protocol' => 'signed',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 2,
            'prvMtnOp_symbolPeriodicity' => 'M',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'signed',
            'prvMtnOp_protocol' => 'signed',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 2,
            'prvMtnOp_symbolPeriodicity' => 'M',
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 30
     * Delete a preventive maintenance
     * Expected Result: The preventive maintenance is deleted from the database
     * @returns void
     */
    public function test_delete_preventive_maintenance_validated()
    {
        $this->create_equipment("three");

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
        ]);
        $response = $this->post('/equipment/delete/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test Conception Number: 31
     * Delete a preventive maintenance from signed equipment
     * Expected Result: The preventive maintenance is updated in the database
     * @returns void
     */
    public function test_delete_preventive_maintenance_validated_to_signed_equipment()
    {
        $this->create_equipment("three", 'validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
        ]);

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
        $response = $this->post('/equipment/delete/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 32
     * Delete the first preventive maintenance of equipment
     * Expected Result: The preventive maintenance is deleted from the database
     * @returns void
     */
    public function test_delete_first_preventive_maintenance()
    {
        $this->create_equipment("three");

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 1,
        ]);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'other',
            'prvMtnOp_protocol' => 'other',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'other',
            'prvMtnOp_protocol' => 'other',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 2,
        ]);
        $id = PreventiveMaintenanceOperation::all()->where('equipmentTemp_id', '=', EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->last()->id)->where('prvMtnOp_number', '=', 1)->last()->id;
        $response = $this->post('/equipment/delete/prvMtnOp/' . $id, [
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'other',
            'prvMtnOp_protocol' => 'other',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 1,
        ]);
    }

    /**
     * Test Conception Number: 33
     * Send a list of preventive maintenance operations linked to the equipment (for the life sheet)
     * Expected Result: The preventive maintenance list is correctly sent
     * @returns void
     */
    public function test_send_preventive_maintenance_operations_life_sheet()
    {
        $this->create_equipment("three");

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_puttingIntoService' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_puttingIntoService' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 1,
            'prvMtnOp_puttingIntoService' => true,
        ]);

        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'other',
            'prvMtnOp_protocol' => 'other',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => false,
            'prvMtnOp_puttingIntoService' => false,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => false,
            'prvMtnOp_description' => 'other',
            'prvMtnOp_protocol' => 'other',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 2,
            'prvMtnOp_puttingIntoService' => false,
        ]);
        $response = $this->get('/prvMtnOps/send/lifesheet/' . Equipment::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'Number' => '1',
                'Description' => 'three',
                'Periodicity' => '1',
                'Symbol' => 'M',
                'Protocol' => 'three',
                'Risk' => 'no',
                'PuttingIntoService' => 'yes',
                'PreventiveOperation' => 'yes',
                'Reformed' => 'no',
                'typeValidation' => null
            ],
            '1' => [
                'Number' => '2',
                'Description' => 'other',
                'Periodicity' => '1',
                'Symbol' => 'M',
                'Protocol' => 'other',
                'Risk' => 'no',
                'PuttingIntoService' => 'no',
                'PreventiveOperation' => 'no',
                'Reformed' => 'no',
                'typeValidation' => null
            ],
        ]);
    }

    /**
     * Test Conception Number: 34
     * Send a list of preventive maintenance operations linked to the equipment (by equipment id)
     * Expected Result: The preventive maintenance list is correctly sent
     * @returns void
     */
    public function test_send_preventive_maintenance_operations_by_equipment_id()
    {
        $this->create_equipment("three");

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_puttingIntoService' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_puttingIntoService' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 1,
            'prvMtnOp_puttingIntoService' => true,
        ]);

        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'other',
            'prvMtnOp_protocol' => 'other',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => false,
            'prvMtnOp_puttingIntoService' => false,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => false,
            'prvMtnOp_description' => 'other',
            'prvMtnOp_protocol' => 'other',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 2,
            'prvMtnOp_puttingIntoService' => false,
        ]);
        $response = $this->get('/prvMtnOps/send/' . Equipment::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'prvMtnOp_number' => '1',
                'prvMtnOp_description' => 'three',
                'prvMtnOp_periodicity' => '1',
                'prvMtnOp_symbolPeriodicity' => 'M',
                'prvMtnOp_protocol' => 'three',
                'prvMtnOp_puttingIntoService' => true,
                'prvMtnOp_preventiveOperation' => true,
            ],
            '1' => [
                'prvMtnOp_number' => '2',
                'prvMtnOp_description' => 'other',
                'prvMtnOp_periodicity' => '1',
                'prvMtnOp_symbolPeriodicity' => 'M',
                'prvMtnOp_protocol' => 'other',
                'prvMtnOp_puttingIntoService' => false,
                'prvMtnOp_preventiveOperation' => false,
            ],
        ]);
    }

    /**
     * Test Conception Number: 35
     * Send a list of preventive maintenance operations linked to the equipment (by id)
     * Expected Result: The preventive maintenance list is correctly sent
     * @returns void
     */
    public function test_send_preventive_maintenance_operations_by_id()
    {
        $this->create_equipment("three");
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_puttingIntoService' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 1,
            'prvMtnOp_puttingIntoService' => true,
        ]);

        $response = $this->get('/prvMtnOp/send/' . PreventiveMaintenanceOperation::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'prvMtnOp_number' => '1',
                'prvMtnOp_description' => 'three',
                'prvMtnOp_periodicity' => '1',
                'prvMtnOp_symbolPeriodicity' => 'M',
                'prvMtnOp_protocol' => 'three',
                'prvMtnOp_puttingIntoService' => true,
                'prvMtnOp_preventiveOperation' => true,
            ]
        ]);
    }

    /**
     * Test Conception Number: 36
     * Send a list of preventive maintenance operations linked to validated equipment (by id)
     * Expected Result: The preventive maintenance list is correctly sent
     * @returns void
     */
    public function test_send_preventive_maintenance_operations_by_id_validated()
    {
        $this->create_equipment("three", 'validated');
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_puttingIntoService' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 1,
            'prvMtnOp_puttingIntoService' => true,
        ]);

        $response = $this->get('/prvMtnOp/send/validated/' . Equipment::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'prvMtnOp_number' => '1',
                'prvMtnOp_description' => 'three',
                'prvMtnOp_protocol' => 'three',
            ]
        ]);
    }

    /**
     * Test Conception Number: 37
     * Reform a preventive maintenance operations linked to equipment with a date before the start date
     * Expected Result: Receiving an error:
     *                                      "You must entered a reformDate that is after the startDate"
     * @returns void
     */
    public function test_reform_preventive_maintenance_operation_before_start()
    {
        $this->create_equipment("three", 'validated');
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_puttingIntoService' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 1,
            'prvMtnOp_puttingIntoService' => true,
        ]);

        $date = Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->subDays(1)->format('Y-m-d H:i:s');
        $response = $this->post('/equipment/reform/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'prvMtnOp_reformDate' => $date,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_reformDate' => 'You must entered a reformDate that is after the startDate'
        ]);
    }

    /**
     * Test Conception Number: 38
     * Reform a preventive maintenance operations linked to equipment with a date two months ago
     * Expected Result: Receiving an error:
     *                                      "You can\'t enter a date that is older than one month"
     * @returns void
     */
    public function test_reform_preventive_maintenance_operation_two_month_ago()
    {
        $this->create_equipment("three", 'validated');
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_puttingIntoService' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 1,
            'prvMtnOp_puttingIntoService' => true,
        ]);
        PreventiveMaintenanceOperation::all()->last()->update([
            'prvMtnOp_startDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->subYear()->format('Y-m-d H:i:s'),
        ]);

        $date = Carbon::now()->subMonth()->subMonth()->format('Y-m-d H:i:s');
        $response = $this->post('/equipment/reform/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'prvMtnOp_reformDate' => $date,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_reformDate' => 'You can\'t enter a date that is older than one month'
        ]);
        /*$this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 1,
            'prvMtnOp_puttingIntoService' => true,
            'prvMtnOp_reformDate' => $date,
        ]);*/
    }

    /**
     * Test Conception Number: 38
     * Reform a preventive maintenance operations linked to equipment with correct data
     * Expected Result: The preventive maintenance operation is reformed
     * @returns void
     */
    public function test_reform_preventive_maintenance_operation()
    {
        $this->create_equipment("three", 'validated');
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_puttingIntoService' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 1,
            'prvMtnOp_puttingIntoService' => true,
        ]);
        PreventiveMaintenanceOperation::all()->last()->update([
            'prvMtnOp_startDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->subYear()->format('Y-m-d H:i:s'),
        ]);

        $date = Carbon::now()->format('Y-m-d');
        $response = $this->post('/equipment/reform/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'prvMtnOp_reformDate' => $date,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_number' => 1,
            'prvMtnOp_puttingIntoService' => true,
            'prvMtnOp_reformDate' => $date,
        ]);
    }
}
