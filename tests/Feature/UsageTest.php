<?php

namespace Tests\Feature;

use App\Models\SW01\EnumDimensionName;
use App\Models\SW01\EnumDimensionType;
use App\Models\SW01\EnumDimensionUnit;
use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsageTest extends TestCase
{
    use RefreshDatabase;

    public function add_eq($validate = 'drafted', $signed = false)
    {
        $this->create_required_enum();
        $admin = $this->create_user();
        $response = $this->post('/equipment/verif', [
            'eq_validate' => $validate,
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'eq_type' => 'External',
            'eq_massUnit' => 'kg',
            'createdBy_id' => $admin
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
            'eq_validate' => $validate,
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'eq_type' => 'External',
            'eq_massUnit' => 'kg',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_location' => 'three',
            'eqTemp_validate' => $validate,
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_mass' => 1234,
            'eqTemp_remarks' => 'three',
            'eqTemp_mobility' => null,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'External')->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'kg')->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        if ($signed) {
            $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
                'reason' => 'technical',
                'enteredBy_id' => $admin,
            ]);
            $response->assertStatus(200);

            $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
                'reason' => 'quality',
                'enteredBy_id' => $admin,
            ]);
            $response->assertStatus(200);
        }
        return Equipment::all()->last()->id;
    }

    public function create_required_enum(): void
    {
        if (EnumDimensionType::all()->where('value', '=', 'External')->count() === 0) {
            $countDimType = EnumDimensionType::all()->count();
            $response = $this->post('/dimension/enum/type/add', [
                'value' => 'External',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimType + 1, EnumDimensionType::all());

        }
        if (EnumDimensionType::all()->where('value', '=', 'Internal')->count() === 0) {
            $countDimType = EnumDimensionType::all()->count();
            $response = $this->post('/dimension/enum/type/add', [
                'value' => 'Internal',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimType + 1, EnumDimensionType::all());

        }
        if (EnumDimensionName::all()->where('value', '=', 'Length')->count() === 0) {
            $countDimName = EnumDimensionName::all()->count();
            $response = $this->post('/dimension/enum/name/add', [
                'value' => 'Length',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimName + 1, EnumDimensionName::all());
        }
        if (EnumDimensionName::all()->where('value', '=', 'Width')->count() === 0) {
            $countDimName = EnumDimensionName::all()->count();
            $response = $this->post('/dimension/enum/name/add', [
                'value' => 'Width',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimName + 1, EnumDimensionName::all());
        }
        if (EnumDimensionUnit::all()->where('value', '=', 'mm')->count() === 0) {
            $countDimUnit = EnumDimensionUnit::all()->count();
            $response = $this->post('/dimension/enum/unit/add', [
                'value' => 'mm',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimUnit + 1, EnumDimensionUnit::all());
        }
        if (EnumDimensionUnit::all()->where('value', '=', 'cm')->count() === 0) {
            $countDimUnit = EnumDimensionUnit::all()->count();
            $response = $this->post('/dimension/enum/unit/add', [
                'value' => 'cm',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimUnit + 1, EnumDimensionUnit::all());
        }

        if (EnumDimensionUnit::all()->where('value', '=', 'km')->count() === 0) {
            $countDimUnit = EnumDimensionUnit::all()->count();
            $response = $this->post('/dimension/enum/unit/add', [
                'value' => 'km',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimUnit + 1, EnumDimensionUnit::all());
        }

        if (EnumEquipmentType::all()->where('value', '=', 'Internal')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'Internal',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());

        }
        if (EnumEquipmentType::all()->where('value', '=', 'External')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'External',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());

        }
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'kg')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'kg',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'g')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'g',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
    }

    public function create_user($name = 'test')
    {
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

    /**
     * Test Conception Number: 1
     * Add a usage as drafted with no value
     * Type: /
     * Precaution: /
     * Expected result: Receiving an error
     *                                      "You must enter a type for your usage"
     * @return void
     */
    public function test_add_usage_draft_no_values()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'drafted',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_type' => 'You must enter a type for your usage'
        ]);
    }

    /**
     * Test Conception Number: 2
     * Add a usage as drafted with a too short type
     * Type: "in"
     * Precaution: /
     * Expected result: Receiving an error
     *                                      "You must enter at least three characters"
     * @return void
     */
    public function test_add_usage_draft_too_short_type()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'drafted',
            'usg_type' => 'in',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_type' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 3
     * Add a usage as drafted with a too long type
     * Type: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Precaution: /
     * Expected result: Receiving an error
     *                                      "You must enter a maximum of 255 characters"
     * @return void
     */
    public function test_add_usage_draft_too_long_type()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'drafted',
            'usg_type' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_type' => 'You must enter a maximum of 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 3
     * Add a usage as drafted with a too long precaution
     * Type: "three"
     * Precaution: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected result: Receiving an error
     *                                      "You must enter a maximum of 255 characters"
     * @return void
     */
    public function test_add_usage_draft_too_long_precaution()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'drafted',
            'usg_type' => 'three',
            'usg_precaution' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_precaution' => 'You must enter a maximum of 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 4
     * Add a usage as to_be_validated with no value
     * Type: /
     * Precaution: /
     * Expected result: Receiving an error
     *                                      "You must enter a type for your usage"
     * @return void
     */
    public function test_add_usage_tbv_no_values()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'to_be_validated',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_type' => 'You must enter a type for your usage'
        ]);
    }

    /**
     * Test Conception Number: 5
     * Add a usage as to_be_validated with a too short type
     * Type: "in"
     * Precaution: /
     * Expected result: Receiving an error
     *                                      "You must enter at least three characters"
     * @return void
     */
    public function test_add_usage_tbv_too_short_type()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'to_be_validated',
            'usg_type' => 'in',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_type' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 6
     * Add a usage as to_be_validated with a too long type
     * Type: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Precaution: /
     * Expected result: Receiving an error
     *                                      "You must enter a maximum of 255 characters"
     * @return void
     */
    public function test_add_usage_tbv_too_long_type()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'to_be_validated',
            'usg_type' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_type' => 'You must enter a maximum of 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 7
     * Add a usage as to_be_validated with a too long precaution
     * Type: "three"
     * Precaution: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected result: Receiving an error
     *                                      "You must enter a maximum of 255 characters"
     * @return void
     */
    public function test_add_usage_tbv_too_long_precaution()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'to_be_validated',
            'usg_type' => 'three',
            'usg_precaution' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_precaution' => 'You must enter a maximum of 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Add a usage as validated with no value
     * Type: /
     * Precaution: /
     * Expected result: Receiving an error
     *                                      "You must enter a type for your usage"
     *                                      "You must enter a precaution for your usage"
     * @return void
     */
    public function test_add_usage_validated_no_values()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'validated',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_type' => 'You must enter a type for your usage',
            'usg_precaution' => 'You must enter a precaution for your usage',
        ]);
    }

    /**
     * Test Conception Number: 9
     * Add a usage as validated with a too short type
     * Type: "in"
     * Precaution: /
     * Expected result: Receiving an error
     *                                      "You must enter at least three characters"
     *                                      "You must enter a precaution for your usage"
     * @return void
     */
    public function test_add_usage_validated_too_short_type()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'validated',
            'usg_type' => 'in',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_type' => 'You must enter at least three characters',
            'usg_precaution' => 'You must enter a precaution for your usage',
        ]);
    }

    /**
     * Test Conception Number: 10
     * Add a usage as validated with a too long type
     * Type: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Precaution: /
     * Expected result: Receiving an error
     *                                      "You must enter a maximum of 255 characters"
     *                                      "You must enter a precaution for your usage"
     * @return void
     */
    public function test_add_usage_validated_too_long_type()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'validated',
            'usg_type' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_type' => 'You must enter a maximum of 255 characters',
            'usg_precaution' => 'You must enter a precaution for your usage',
        ]);
    }

    /**
     * Test Conception Number: 11
     * Add a usage as validated with a too short precaution
     * Type: "three"
     * Precaution: "in"
     * Expected result: Receiving an error
     *                                      "You must enter at least three characters"
     * @return void
     */
    public function test_add_usage_validated_too_short_precaution()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'validated',
            'usg_type' => 'three',
            'usg_precaution' => 'in',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_precaution' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 12
     * Add a usage as validated with a too long precaution
     * Type: "three"
     * Precaution: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected result: Receiving an error
     *                                      "You must enter a maximum of 255 characters"
     * @return void
     */
    public function test_add_usage_validated_too_long_precaution()
    {
        $user_id = $this->create_user();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'validated',
            'usg_type' => 'three',
            'usg_precaution' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'usg_precaution' => 'You must enter a maximum of 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 13
     * Add a usage as drafted with correct values
     * Type: "three"
     * Precaution: "three"
     * Expected result: The usage is added to the database
     * @return void
     */
    public function test_add_usage_draft_correct_values()
    {
        $user_id = $this->create_user();
        $eq_id = $this->add_eq();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'drafted',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/usg', [
            'usg_validate' => 'drafted',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => $user_id,
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('usages', [
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'usg_validate' => 'drafted',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->first()->id,
        ]);
    }

    /**
     * Test Conception Number: 14
     * Add a usage as to_be_validated with correct values
     * Type: "three"
     * Precaution: "three"
     * Expected result: The usage is added to the database
     * @return void
     */
    public function test_add_usage_to_be_validated_correct_values()
    {
        $user_id = $this->create_user();
        $eq_id = $this->add_eq();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'to_be_validated',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/usg', [
            'usg_validate' => 'to_be_validated',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => $user_id,
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('usages', [
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'usg_validate' => 'to_be_validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->first()->id,
        ]);
    }

    /**
     * Test Conception Number: 15
     * Add a usage as validated with correct values
     * Type: "three"
     * Precaution: "three"
     * Expected result: The usage is added to the database
     * @return void
     */
    public function test_add_usage_validated_correct_values()
    {
        $user_id = $this->create_user();
        $eq_id = $this->add_eq();
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'validated',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/usg', [
            'usg_validate' => 'validated',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => $user_id,
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('usages', [
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'usg_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->first()->id,
        ]);
    }

    /**
     * Test Conception Number: 16
     * Add a usage to a signed equipment
     * Type: "three"
     * Precaution: "three"
     * Expected result: The usage is added to the database and the equipment is no longer signed
     * @return void
     */
    public function test_add_usage_to_signed_equipment()
    {
        $user_id = $this->create_user();
        $eq_id = $this->add_eq('validated', true);
        $response = $this->post('/usage/verif', [
            'usg_validate' => 'validated',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/usg', [
            'usg_validate' => 'validated',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => $user_id,
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('usages', [
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'usg_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $eq_id)->first()->id,
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => $eq_id,
            'eqTemp_lifeSheetCreated' => 0,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
            'eqTemp_version' => 2,
        ]);
    }


}
