<?php

namespace Tests\Feature;

use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\EnumRiskFor;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\PreventiveMaintenanceOperation;
use App\Models\SW01\Risk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiskForTest extends TestCase
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

    public function requiredForTest()
    {
        $user_id = $this->create_user('test');
        // Add the different enum of the risk if they didn't already exist in the database
        if (EnumRiskFor::all()->where('value', '=', 'Risk')->count() === 0) {
            $countDimName = EnumRiskFor::all()->count();
            $response = $this->post('/risk/enum/riskfor/add', [
                'value' => 'Risk',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimName + 1, EnumRiskFor::all());
        }
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'g')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'g',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'Balance')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'Balance',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        return $user_id;
    }

    public function add_eq($name, $validate)
    {
        $user_id = $this->requiredForTest();

        $response = $this->post('/equipment/verif', [
            'eq_validate' => $validate,
            'eq_internalReference' => $name,
            'eq_externalReference' => $name,
            'eq_name' => $name,
            'eq_serialNumber' => $name,
            'eq_constructor' => $name,
            'eq_mass' => 1234,
            'eq_remarks' => $name,
            'eq_set' => $name,
            'eq_location' => $name,
            'eq_type' => 'Balance',
            'eq_massUnit' => 'g',
            'createdBy_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
            'eq_validate' => $validate,
            'eq_internalReference' => $name,
            'eq_externalReference' => $name,
            'eq_name' => $name,
            'eq_serialNumber' => $name,
            'eq_constructor' => $name,
            'eq_mass' => 1234,
            'eq_remarks' => $name,
            'eq_set' => $name,
            'eq_location' => $name,
            'eq_type' => 'Balance',
            'eq_massUnit' => 'g'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_location' => $name,
            'eqTemp_validate' => $validate,
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_mass' => 1234,
            'eqTemp_remarks' => $name,
            'eqTemp_mobility' => null,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'Balance')->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'g')->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        return Equipment::all()->last()->id;
    }

    /**
     * Test Conception Number: 1
     * Saved a risk as drafted from add menu with no value
     * Remarks: /
     * Way Of Control: /
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must enter a remark for your risk"
     * @return void
     */
    public function test_add_risk_drafted_no_values()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_remarks' => 'You must enter a remark for your risk'
        ]);
    }

    /**
     * Test Conception Number: 2
     * Saved a risk as drafted from add menu with too short remarks
     * Remarks: "in"
     * Way Of Control: /
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must enter at least three characters"
     * @return void
     */
    public function test_add_risk_drafted_too_short_remarks()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'in',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_remarks' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 3
     * Saved a risk as drafted from add menu with too long remarks
     * Remarks: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Way Of Control: /
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must enter less than 255 characters"
     * @return void
     */
    public function test_add_risk_drafted_too_long_remarks()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_remarks' => 'You must enter less than 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 4
     * Saved a risk as drafted from add menu with too long way of control
     * Remarks: "Remarks"
     * Way Of Control: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must enter less than 255 characters"
     * @return void
     */
    public function test_add_risk_drafted_too_long_wayOfControl()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Remarks',
            'risk_wayOfControl' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_wayOfControl' => 'You must enter less than 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 5
     * Saved a risk as drafted from add menu with correct values
     * Remarks: "Remarks"
     * Way Of Control: "Control"
     * Risk For: /
     * Expected result: The risk is correctly saved
     * @return void
     */
    public function test_add_risk_drafted_correct_values()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'drafted');
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Remarks',
            'risk_wayOfControl' => 'Control',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/risk', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Remarks',
            'risk_wayOfControl' => 'Control',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'Remarks',
            'risk_wayOfControl' => 'Control',
            'risk_validate' => 'drafted',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', $eq_id)->last()->id
        ]);
    }

    /**
     * Test Conception Number: 6
     * Saved a risk as to be validated from add menu with no value
     * Remarks: /
     * Way Of Control: /
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must enter a remark for your risk"
     * @return void
     */
    public function test_add_risk_to_be_validated_no_values()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'to_be_validated',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_remarks' => 'You must enter a remark for your risk'
        ]);
    }

    /**
     * Test Conception Number: 7
     * Saved a risk as to be validated from add menu with too short remarks
     * Remarks: "in"
     * Way Of Control: /
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must enter at least three characters"
     * @return void
     */
    public function test_add_risk_to_be_validated_too_short_remarks()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'to_be_validated',
            'risk_remarks' => 'in',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_remarks' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Saved a risk as to be validated from add menu with too long remarks
     * Remarks: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Way Of Control: /
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must enter less than 255 characters"
     * @return void
     */
    public function test_add_risk_to_be_validated_too_long_remarks()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'to_be_validated',
            'risk_remarks' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_remarks' => 'You must enter less than 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 9
     * Saved a risk as to be validated from add menu with too long way of control
     * Remarks: "Remarks"
     * Way Of Control: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must enter less than 255 characters"
     * @return void
     */
    public function test_add_risk_to_be_validated_too_long_wayOfControl()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'to_be_validated',
            'risk_remarks' => 'Remarks',
            'risk_wayOfControl' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_wayOfControl' => 'You must enter less than 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 10
     * Saved a risk as to be validated from add menu with correct values
     * Remarks: "Remarks"
     * Way Of Control: "Control"
     * Risk For: /
     * Expected result: The risk is correctly saved
     * @return void
     */
    public function test_add_risk_to_be_validated_correct_values()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'drafted');
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'to_be_validated',
            'risk_remarks' => 'Remarks',
            'risk_wayOfControl' => 'Control',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/risk', [
            'risk_validate' => 'to_be_validated',
            'risk_remarks' => 'Remarks',
            'risk_wayOfControl' => 'Control',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'Remarks',
            'risk_wayOfControl' => 'Control',
            'risk_validate' => 'to_be_validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', $eq_id)->last()->id
        ]);
    }

    /**
     * Test Conception Number: 11
     * Saved a risk as validated from add menu with no values
     * Remarks: /
     * Way Of Control: /
     * Risk For: /
     * Expected result: Receiving an error:
     *                                      "You must enter a remark for your risk"
     *                                      "You must enter a way of control for your risk"
     * @return void
     */
    public function test_add_risk_validated_no_values()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_remarks' => 'You must enter a remark for your risk',
            'risk_wayOfControl' => 'You must enter a way of control for your risk'
        ]);
    }

    /**
     * Test Conception Number: 12
     * Saved a risk as validated from add menu with too short remarks
     * Remarks: "in"
     * Way Of Control: /
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must enter at least three characters"
     *                                      "You must enter a way of control for your risk"
     * @return void
     */
    public function test_add_risk_validated_too_short_remarks()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'in',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_remarks' => 'You must enter at least three characters',
            'risk_wayOfControl' => 'You must enter a way of control for your risk'
        ]);
    }

    /**
     * Test Conception Number: 13
     * Saved a risk as validated from add menu with too long remarks
     * Remarks: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Way Of Control: /
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must enter less than 255 characters"
     *                                      "You must enter a way of control for your risk"
     * @return void
     */
    public function test_add_risk_validated_too_long_remarks()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_remarks' => 'You must enter less than 255 characters',
            'risk_wayOfControl' => 'You must enter a way of control for your risk'
        ]);
    }

    /**
     * Test Conception Number: 14
     * Saved a risk as validated from add menu with too short way of control
     * Remarks: "three"
     * Way Of Control: "in"
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must enter at least three characters"
     * @return void
     */
    public function test_add_risk_validated_too_short_wayOfControl()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'in',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_wayOfControl' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 15
     * Saved a risk as validated from add menu with too long way of control
     * Remarks: "three"
     * Way Of Control: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must enter less than 255 characters"
     * @return void
     */
    public function test_add_risk_validated_too_long_wayOfControl()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'risk_wayOfControl' => 'You must enter less than 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 16
     * Saved a risk as validated from add menu with no risk for
     * Remarks: "three"
     * Way Of Control: "three"
     * Risk For: /
     * Expected result: Receiving an error
     *                                      "You must choose a target for your risk"
     * @return void
     */
    public function test_add_risk_validated_no_riskFor()
    {
        $user_id = $this->requiredForTest();
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'risk_for' => 'You must choose a target for your risk'
        ]);
    }

    /**
     * Test Conception Number: 17
     * Saved a risk as validated from add menu with correct values
     * Remarks: "three"
     * Way Of Control: "three"
     * Risk For: "Risk"
     * Expected result: The risk is saved in the database
     * @return void
     */
    public function test_add_risk_validated_correct_values()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'drafted');
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->last()->id
        ]);
    }

    /**
     * Test Conception Number: 18
     * Saved a risk as validated from add menu with correct values to a signed equipment
     * Remarks: "three"
     * Way Of Control: "three"
     * Risk For: "Risk"
     * Expected result: The risk is saved in the database and the equipment is no longer signed
     * @return void
     */
    public function test_add_risk_validated_correct_values_signed()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'validated');
        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => $user_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => $eq_id,
            'eqTemp_version' => 1,
            'qualityVerifier_id' => $user_id,
            'technicalVerifier_id' => $user_id,
        ]);
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->last()->id
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => $eq_id,
            'eqTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 19
     * Update a risk linked to equipment
     * Remarks: "other"
     * Way Of Control: "other"
     * Risk For: "Risk"
     * Expected result: The risk is update in the database
     * @return void
     */
    public function test_update_risk_correct_values()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'drafted');
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->last()->id
        ]);
        $response = $this->post('/equipment/update/risk/' . Risk::all()->last()->id, [
            'risk_remarks' => 'other',
            'risk_wayOfControl' => 'other',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id,
            'risk_validate' => 'validated'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'other',
            'risk_wayOfControl' => 'other',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->last()->id
        ]);
    }

    /**
     * Test Conception Number: 20
     * Update a risk linked to signed equipment
     * Remarks: "other"
     * Way Of Control: "other"
     * Risk For: "Risk"
     * Expected result: The risk is update in the database, and the equipment is no longer signed
     * @return void
     */
    public function test_update_risk_correct_values_signed()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'validated');
        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->last()->id
        ]);
        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => $user_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/update/risk/' . Risk::all()->last()->id, [
            'risk_remarks' => 'other',
            'risk_wayOfControl' => 'other',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id,
            'risk_validate' => 'validated'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'other',
            'risk_wayOfControl' => 'other',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->last()->id
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => $eq_id,
            'eqTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 21
     * Add a risk as drafted to a preventive maintenance operation with correct values
     * Remarks: "three"
     * Way Of Control: "three"
     * Risk For: "Risk"
     * Expected result: The risk is added in the database
     * @return void
     */
    public function test_add_risk_correct_values_preventive_maintenance_drafted()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
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

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp/risk', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'drafted',
            'preventiveMaintenanceOperation_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
    }

    /**
     * Test Conception Number: 22
     * Add a risk as to be validated to a preventive maintenance operation with correct values
     * Remarks: "three"
     * Way Of Control: "three"
     * Risk For: "Risk"
     * Expected result: The risk is added in the database
     * @return void
     */
    public function test_add_risk_correct_values_preventive_maintenance_to_be_validated()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
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

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'to_be_validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp/risk', [
            'risk_validate' => 'to_be_validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'to_be_validated',
            'preventiveMaintenanceOperation_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
    }

    /**
     * Test Conception Number: 23
     * Add a risk as validated to a preventive maintenance operation with correct values
     * Remarks: "three"
     * Way Of Control: "three"
     * Risk For: "Risk"
     * Expected result: The risk is added in the database
     * @return void
     */
    public function test_add_risk_correct_values_preventive_maintenance_validated()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
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

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'preventiveMaintenanceOperation_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
    }

    /**
     * Test Conception Number: 24
     * Add a risk to a preventive maintenance operation linked to signed equipment
     * Remarks: "three"
     * Way Of Control: "three"
     * Risk For: "Risk"
     * Expected result: The risk is added in the database and the equipment is no longer signed
     * @return void
     */
    public function test_add_risk_correct_values_preventive_maintenance_signed()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
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
            'enteredBy_id' => $user_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => $user_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'preventiveMaintenanceOperation_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => $eq_id,
            'eqTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 25
     * Update a risk linked to a preventive maintenance operation
     * Remarks: "other"
     * Way Of Control: "other"
     * Risk For: "Risk"
     * Expected result: The risk is update in the database
     * @return void
     */
    public function test_update_risk_preventive_maintenance()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'drafted');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
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

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'preventiveMaintenanceOperation_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
        $response = $this->post('/equipment/update/prvMtnOp/risk/' . Risk::all()->last()->id, [
            'risk_remarks' => 'other',
            'risk_wayOfControl' => 'other',
            'risk_for' => 'Risk',
            'risk_validate' => 'validated',
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'other',
            'risk_wayOfControl' => 'other',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'preventiveMaintenanceOperation_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
    }

    /**
     * Test Conception Number: 26
     * Update a risk linked to a preventive maintenance and to signed equipment
     * Remarks: "other"
     * Way Of Control: "other"
     * Risk For: "Risk"
     * Expected result: The risk is update in the database, and the equipment is no longer signed
     * @return void
     */
    public function test_update_risk_preventive_maintenance_signed()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
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

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'preventiveMaintenanceOperation_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => $user_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/update/prvMtnOp/risk/' . Risk::all()->last()->id, [
            'risk_remarks' => 'other',
            'risk_wayOfControl' => 'other',
            'risk_for' => 'Risk',
            'risk_validate' => 'validated',
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'other',
            'risk_wayOfControl' => 'other',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'preventiveMaintenanceOperation_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => $eq_id,
            'eqTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 27
     * Delete a risk linked to equipment
     * Expected result: The risk is deleted from the database
     * @return void
     */
    public function test_delete_risk_equipment()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'validated');

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->last()->id
        ]);
        $response = $this->post('/equipment/delete/risk/' . Risk::all()->last()->id, [
            'eq_id' => $eq_id,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->last()->id
        ]);
    }

    /**
     * Test Conception Number: 28
     * Delete a risk linked to signed equipment
     * Expected result: The risk is deleted from the database, and the equipment is no longer signed
     * @return void
     */
    public function test_delete_risk_equipment_signed()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'validated');

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->last()->id
        ]);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => $user_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => $user_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/delete/risk/' . Risk::all()->last()->id, [
            'eq_id' => $eq_id,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('risks', [
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->last()->id,
            'risk_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->last()->id
        ]);
    }

    /**
     * Test Conception Number: 29
     * Send the risk list linked to equipment
     * Expected result: The data are correctly sent
     * @return void
     */
    public function test_send_risk_equipment()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'validated');

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);
        $response = $this->get('/equipment/risk/send/' . $eq_id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'risk_remarks' => 'three',
                'risk_wayOfControl' => 'three',
                'risk_for' => 'Risk',
                'risk_validate' => 'validated',
            ]
        ]);
    }

    /**
     * Test Conception Number: 29
     * Send the risk of preventive maintenance
     * Expected result: The data are correctly sent
     * @return void
     */
    public function test_send_risk_preventive()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'validated');

        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
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

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response = $this->get('/prvMtnOp/risk/send/' . PreventiveMaintenanceOperation::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'risk_remarks' => 'three',
                'risk_wayOfControl' => 'three',
                'risk_for' => 'Risk',
                'risk_validate' => 'validated',
                'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id
            ]
        ]);
    }

    /**
     * Test Conception Number: 29
     * Send the risk in the pdf format of preventive maintenance
     * Expected result: The data are correctly sent
     * @return void
     */
    public function test_send_risk_preventive_pdf()
    {
        $user_id = $this->requiredForTest();
        $eq_id = $this->add_eq('Test', 'validated');

        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => $user_id,
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

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp/risk', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response = $this->get('/prvMtnOp/risk/send/pdf/' . EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'risk_remarks' => 'three',
                'risk_wayOfControl' => 'three',
                'risk_validate' => 'validated',
            ]
        ]);
    }
}
