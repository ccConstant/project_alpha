<?php

namespace Tests\Feature;

use App\Models\SW01\CurativeMaintenanceOperation;
use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\Mme;
use App\Models\SW01\MmeTemp;
use App\Models\SW01\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurativeMaintenanceOperationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Conception Number: 1
     * Add a curative maintenance operation as drafted with no values
     * Description: /
     * Report Number: /
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a description for the curative maintenance operation"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_drafted_with_no_values()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'drafted',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter a description for the curative maintenance operation'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'drafted',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter a description for the curative maintenance operation'
        ]);
    }

    public function create_equipment($name, $validated = 'drafted')
    {

        $user_id = $this->create_user('test');
        $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
        $response = $this->post('/equipment/enum/massUnit/add', [
            'value' => $name,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        $countEqType = EnumEquipmentType::all()->count();
        $response = $this->post('/equipment/enum/type/add', [
            'value' => $name,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqType + 1, EnumEquipmentType::all());
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
            'eq_massUnit' => $name
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_location' => $name,
            'eqTemp_validate' => $validated,
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_mass' => 1234,
            'eqTemp_remarks' => $name,
            'eqTemp_mobility' => null,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', $name)->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', $name)->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        return Equipment::all()->last()->id;
    }

    public function create_user($name)
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

    public function create_mme($name, $validated = 'drafted')
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => $validated,
            'mme_internalReference' => $name,
            'mme_externalReference' => $name,
            'mme_name' => $name,
            'mme_serialNumber' => $name,
            'mme_constructor' => $name,
            'mme_remarks' => $name,
            'mme_set' => $name,
            'mme_location' => $name,
            'createdBy_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $countEquipment = Mme::all()->count();
        $response = $this->post('/mme/add', [
            'mme_validate' => $validated,
            'mme_internalReference' => $name,
            'mme_externalReference' => $name,
            'mme_name' => $name,
            'mme_serialNumber' => $name,
            'mme_constructor' => $name,
            'mme_remarks' => $name,
            'mme_set' => $name,
            'mme_location' => $name,
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Mme::all()->count());
        return Mme::all()->where('mme_internalReference', '=', $name)->last()->id;
    }

    /**
     * Test Conception Number: 2
     * Add a curative maintenance operation as drafted with a too short description
     * Description: "in"
     * Report Number: /
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter at least three characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_drafted_with_short_desc()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter at least three characters'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 3
     * Add a curative maintenance operation as drafted with a too long description
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Report Number: /
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_drafted_with_long_desc()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter a maximum of 50 characters'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 4
     * Add a curative maintenance operation as drafted with a too long report number
     * Description: "three"
     * Report Number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_drafted_with_long_report_number()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a maximum of 255 characters'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a maximum of 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 4
     * Add a curative maintenance operation as drafted with correct values
     * Description: "three"
     * Report Number: "three"
     * Start Date: /
     * End Date: /
     * Expected Result: The curative maintenance operation is added to the database
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_drafted_with_correct_values()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_validate' => 'drafted',
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'mme_state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_validate' => 'drafted',
        ]);
    }


    /**
     * Test Conception Number: 6
     * Add a curative maintenance operation as to be validated with no values
     * Description: /
     * Report Number: /
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a description for the curative maintenance operation"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_to_be_validated_with_no_values()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter a description for the curative maintenance operation'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter a description for the curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 7
     * Add a curative maintenance operation as to be validated with a too short description
     * Description: "in"
     * Report Number: /
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter at least three characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_to_be_validated_with_short_desc()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter at least three characters'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Add a curative maintenance operation as to be validated with a too long description
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Report Number: /
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_to_be_validated_with_long_desc()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter a maximum of 50 characters'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 9
     * Add a curative maintenance operation as to be validated with a too long report number
     * Description: "three"
     * Report Number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_to_be_validated_with_long_report_number()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a maximum of 255 characters'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a maximum of 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 10
     * Add a curative maintenance operation as to be validated with correct values
     * Description: "three"
     * Report Number: "three"
     * Start Date: /
     * End Date: /
     * Expected Result: The curative maintenance operation is added to the database
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_to_be_validated_with_correct_values()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'state_id' => $mostRecentlyEqState->id,
        ]);

        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'other',
            'curMtnOp_reportNumber' => 'other',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'other',
            'curMtnOp_reportNumber' => 'other',
            'state_id' => $mostRecentlyEqState->id,
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'mme_state_id' => $mostRecentlyMmeState->id,
        ]);

        $response = $this->post('/mme/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'other',
            'curMtnOp_reportNumber' => 'other',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'other',
            'curMtnOp_reportNumber' => 'other',
            'mme_state_id' => $mostRecentlyMmeState->id,
        ]);
    }

    /**
     * Test Conception Number: 11
     * Add a curative maintenance operation as validated with no values
     * Description: /
     * Report Number: /
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a report number for the curative maintenance operation"
     *                                          "You must enter a description for the curative maintenance operation"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_no_values()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a report number for the curative maintenance operation',
            'curMtnOp_description' => 'You must enter a description for the curative maintenance operation'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'validated',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a report number for the curative maintenance operation',
            'curMtnOp_description' => 'You must enter a description for the curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 12
     * Add a curative maintenance operation as validated with a too short description
     * Description: "in"
     * Report Number: /
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a report number for the curative maintenance operation"
     *                                          "You must enter at least three characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_short_description()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a report number for the curative maintenance operation',
            'curMtnOp_description' => 'You must enter at least three characters'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a report number for the curative maintenance operation',
            'curMtnOp_description' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 13
     * Add a curative maintenance operation as validated with a too long description
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Report Number: /
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a report number for the curative maintenance operation"
     *                                          "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_long_description()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a report number for the curative maintenance operation',
            'curMtnOp_description' => 'You must enter a maximum of 50 characters'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a report number for the curative maintenance operation',
            'curMtnOp_description' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 14
     * Add a curative maintenance operation as validated with a too short report number
     * Description: "three"
     * Report Number: "in"
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter at least three characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_short_report_number()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter at least three characters'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 15
     * Add a curative maintenance operation as validated with a too long report number
     * Description: "three"
     * Report Number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_long_report_number()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a maximum of 255 characters'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a maximum of 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 16
     * Add a curative maintenance operation as validated with correct values but no start date
     * Description: "three"
     * Report Number: "three"
     * Start Date: /
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You have to entered the startDate of your curative maintenance operation for validate it"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_correct_values()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_startDate' => 'You have to entered the startDate of your curative maintenance operation for validate it'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_startDate' => 'You have to entered the startDate of your curative maintenance operation for validate it'
        ]);
    }

    /**
     * Test Conception Number: 17
     * Add a curative maintenance operation as validated with correct values but no end date
     * Description: "three"
     * Report Number: "three"
     * Start Date: Today
     * End Date: /
     * Expected Result: Receiving an error:
     *                                          "You have to entered the endDate of your curative maintenance operation for validate it"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_correct_values_but_no_end_date()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_startDate' => Carbon::now(),
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_endDate' => 'You have to entered the endDate of your curative maintenance operation for validate it'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_startDate' => Carbon::now(),
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_endDate' => 'You have to entered the endDate of your curative maintenance operation for validate it'
        ]);
    }

    /**
     * Test Conception Number: 18
     * Add a curative maintenance operation as validated with correct values and dates
     * Description: "three"
     * Report Number: "three"
     * Start Date: Today
     * End Date: Today + 1 week
     * Expected Result: Receiving an error:
     *                                          "You have to entered the realizator of this curative maintenance operation for validate it"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_correct_values_and_dates()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You have to entered the realizator of this curative maintenance operation for validate it'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You have to entered the realizator of this curative maintenance operation for validate it'
        ]);
    }

    /**
     * Test Conception Number: 19
     * Add a curative maintenance operation as validated with correct values and dates + realizator
     * Description: "three"
     * Report Number: "three"
     * Start Date: Today
     * End Date: Today + 1 week
     * Expected Result: Receiving an error:
     *                                          "You have to entered the quality and the technical Verifier of this curative maintenance operation for validate it"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_correct_values_and_dates_and_realizator()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
            'user_id' => User::all()->last()->id,
            'realizedBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You have to entered the quality and the technical Verifier of this curative maintenance operation for validate it'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
            'user_id' => User::all()->last()->id,
            'realizedBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You have to entered the quality and the technical Verifier of this curative maintenance operation for validate it'
        ]);
    }

    /**
     * Test Conception Number: 20
     * Add a curative maintenance operation with a start date before one month ago
     * Description: "three"
     * Report Number: "three"
     * Start Date: Today - 2 month
     * End Date: Today + 1 week
     * Expected Result: Receiving an error:
     *                                          "You can't enter a date that is older than one month"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_with_start_date_before_one_month_ago()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'realizedBy_id' => User::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->subMonths(2),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_startDate' => 'You can\'t enter a date that is older than one month'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'realizedBy_id' => User::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->subMonths(2),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_startDate' => 'You can\'t enter a date that is older than one month'
        ]);
    }

    /**
     * Test Conception Number: 21
     * Add a curative maintenance operation with a end date before one month ago
     * Description: "three"
     * Report Number: "three"
     * Start Date: Today
     * End Date: Today - 2 month
     * Expected Result: Receiving an error:
     *                                          "You can't enter a date that is older than one month"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_with_end_date_before_one_month_ago()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'realizedBy_id' => User::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->subMonths(2),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_endDate' => 'You can\'t enter a date that is older than one month'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'realizedBy_id' => User::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->subMonths(2),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_endDate' => 'You can\'t enter a date that is older than one month'
        ]);
    }

    /**
     * Test Conception Number: 22
     * Add a curative maintenance operation with a start date before state start date
     * Description: "three"
     * Report Number: "three"
     * Start Date: Today - 1 week
     * End Date: Today + 1 week
     * Expected Result: Receiving an error:
     *                                          "You can't entered this startDate because it must be after the startDate of the state"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_with_start_date_before_state_start_date()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'realizedBy_id' => User::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->subWeek(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_startDate' => 'You can\'t entered this startDate because it must be after the startDate of the state'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'realizedBy_id' => User::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->subWeek(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_startDate' => 'You can\'t entered this startDate because it must be after the startDate of the state'
        ]);
    }

    /**
     * Test Conception Number: 23
     * Add a curative maintenance operation with a end date before state start date
     * Description: "three"
     * Report Number: "three"
     * Start Date: Today + 1 week
     * End Date: Today - 1 week
     * Expected Result: Receiving an error:
     *                                          "You can't entered this endDate because it must be after the startDate of the state"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_with_end_date_before_state_start_date()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'realizedBy_id' => User::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->addWeek(),
            'curMtnOp_endDate' => Carbon::now()->subWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_endDate' => 'You can\'t entered this endDate because it must be after the startDate of the state'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'realizedBy_id' => User::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->addWeek(),
            'curMtnOp_endDate' => Carbon::now()->subWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_endDate' => 'You can\'t entered this endDate because it must be after the startDate of the state'
        ]);
    }

    /**
     * Test Conception Number: 24
     * Add a curative maintenance operation with an end date before start date
     * Description: "three"
     * Report Number: "three"
     * Start Date: Today + 1 week
     * End Date: Today
     * Expected Result: Receiving an error:
     *                                          "You must entered a startDate that is before endDate"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_with_end_date_before_start_date()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'realizedBy_id' => User::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->addWeek(),
            'curMtnOp_endDate' => Carbon::now(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_endDate' => 'You must entered a startDate that is before endDate'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'realizedBy_id' => User::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->addWeek(),
            'curMtnOp_endDate' => Carbon::now(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_endDate' => 'You must entered a startDate that is before endDate'
        ]);
    }

    /**
     * Test Conception Number: 25
     * Update a curative maintenance operation as validated without realizator
     * Description: "three"
     * Report Number: "three"
     * Start Date: Today
     * End Date: Today + 1 week
     * Expected Result: Receiving an error:
     *                                          "You have to entered the realizator of this curative maintenance operation for validate it"
     * @returns void
     */
    public function test_update_curative_maintenance_operation_as_validated_without_realizator()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'state_id' => $mostRecentlyEqState->id,
        ]);

        $response = $this->post('/curMtnOp/verif', [
            'reason' => 'update',
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You have to entered the realizator of this curative maintenance operation for validate it'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'mme_state_id' => $mostRecentlyMmeState->id,
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'reason' => 'update',
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You have to entered the realizator of this curative maintenance operation for validate it'
        ]);
    }

    /**
     * Test Conception Number: 26
     * Update a curative maintenance operation as validated without quality approver
     * Description: "three"
     * Report Number: "three"
     * Start Date: Today
     * End Date: Today + 1 week
     * Expected Result: Receiving an error:
     *                                          "You have to entered the quality Verifier of this curative maintenance operation for validate it"
     * @returns void
     */
    public function test_update_curative_maintenance_operation_as_validated_without_quality()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'state_id' => $mostRecentlyEqState->id,
        ]);

        $response = $this->post('/curMtnOp/realize/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'reason' => 'equipment',
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/curMtnOp/verif', [
            'reason' => 'update',
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You have to entered the quality Verifier of this curative maintenance operation for validate it'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'mme_state_id' => $mostRecentlyMmeState->id,
        ]);

        $response = $this->post('/curMtnOp/realize/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'reason' => 'mme',
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/curMtnOp/verif', [
            'reason' => 'update',
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You have to entered the quality Verifier of this curative maintenance operation for validate it'
        ]);
    }

    /**
     * Test Conception Number: 27
     * Update a curative maintenance operation as validated without technical approver
     * Description: "three"
     * Report Number: "three"
     * Start Date: Today
     * End Date: Today + 1 week
     * Expected Result: Receiving an error:
     *                                          "You have to entered the technical Verifier of this curative maintenance operation for validate it"
     * @returns void
     */
    public function test_update_curative_maintenance_operation_as_validated_without_technical()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'state_id' => $mostRecentlyEqState->id,
        ]);

        $response = $this->post('/curMtnOp/realize/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'reason' => 'equipment',
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/curMtnOp/qualityVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/curMtnOp/verif', [
            'reason' => 'update',
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You have to entered the technical Verifier of this curative maintenance operation for validate it'
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'mme_state_id' => $mostRecentlyMmeState->id,
        ]);

        $response = $this->post('/curMtnOp/realize/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'reason' => 'mme',
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/curMtnOp/qualityVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/curMtnOp/verif', [
            'reason' => 'update',
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You have to entered the technical Verifier of this curative maintenance operation for validate it'
        ]);
    }

    /**
     * Test Conception Number: 28
     * Update a curative maintenance operation as drafted or to be validated
     * Description: "other"
     * Report Number: "other"
     * Start Date: Today
     * End Date: Today + 1 week
     * Expected Result: The curative maintenance operation is updated
     * @returns void
     */
    public function test_update_curative_maintenance_operation()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'state_id' => $mostRecentlyEqState->id,
        ]);

        $response = $this->post('/curMtnOp/verif', [
            'reason' => 'update',
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'other',
            'curMtnOp_reportNumber' => 'other',
            'user_id' => User::all()->last()->id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/update/state/curMtnOp/' . CurativeMaintenanceOperation::all()->last()->id, [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'other',
            'curMtnOp_reportNumber' => 'other',
            'user_id' => User::all()->last()->id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'other',
            'curMtnOp_reportNumber' => 'other',
            'state_id' => $mostRecentlyEqState->id,
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'mme_state_id' => $mostRecentlyMmeState->id,
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'reason' => 'update',
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/update/state/curMtnOp/' . CurativeMaintenanceOperation::all()->last()->id, [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'other',
            'curMtnOp_reportNumber' => 'other',
            'user_id' => User::all()->last()->id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'other',
            'curMtnOp_reportNumber' => 'other',
            'state_id' => $mostRecentlyEqState->id,
        ]);
    }

    /**
     * Test Conception Number: 29
     * Send a curative maintenance operation list
     * Expected Result: The data are sent correctly
     * @returns void
     */
    public function test_send_curative_maintenance_operation()
    {
        $eq_id = $this->create_equipment('test');
        $mme_id = $this->create_mme('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyMmeState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyMmeState->created_at;
            if ($date >= $date2) {
                $mostRecentlyMmeState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'state_id' => $mostRecentlyEqState->id,
        ]);

        $response = $this->post('/curMtnOp/realize/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'reason' => 'equipment',
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/curMtnOp/qualityVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/curMtnOp/technicalVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        CurativeMaintenanceOperation::all()->last()->update([
            'enteredBy_id' => User::all()->last()->id,
        ]);

        $response = $this->get('/state/curMtnOp/send/' . $mostRecentlyEqState->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => CurativeMaintenanceOperation::all()->last()->id,
                'curMtnOp_number' => '1',
                'curMtnOp_reportNumber' => 'three',
                'curMtnOp_description' => 'three',
                'curMtnOp_startDate' => null,
                'curMtnOp_endDate' => null,
                'curMtnOp_validate' => 'validated',
                'qualityVerifier_firstName' => 'test',
                'qualityVerifier_lastName' => 'test',
                'realizedBy_firstName' => 'test',
                'realizedBy_lastName' => 'test',
                'enteredBy_firstName' => 'test',
                'enteredBy_lastName' => 'test',
                'technicalVerifier_firstName' => 'test',
                'technicalVerifier_lastName' => 'test'
            ]
        ]);

        $response = $this->post('/mme/curMtnOp/verif', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyMmeState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'mme_state_id' => $mostRecentlyMmeState->id,
        ]);

        $response = $this->post('/curMtnOp/realize/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'reason' => 'mme',
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/curMtnOp/qualityVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/curMtnOp/technicalVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        CurativeMaintenanceOperation::all()->last()->update([
            'enteredBy_id' => User::all()->last()->id,
        ]);

        $response = $this->get('/mme_state/curMtnOp/send/' . $mostRecentlyMmeState->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => CurativeMaintenanceOperation::all()->last()->id,
                'curMtnOp_number' => '1',
                'curMtnOp_reportNumber' => 'three',
                'curMtnOp_description' => 'three',
                'curMtnOp_startDate' => null,
                'curMtnOp_endDate' => null,
                'curMtnOp_validate' => 'validated',
                'qualityVerifier_firstName' => 'test',
                'qualityVerifier_lastName' => 'test',
                'realizedBy_firstName' => 'test',
                'realizedBy_lastName' => 'test',
                'enteredBy_firstName' => 'test',
                'enteredBy_lastName' => 'test',
                'technicalVerifier_firstName' => 'test',
                'technicalVerifier_lastName' => 'test'
            ]
        ]);
    }

    /**
     * Test Conception Number: 30
     * Delete a curative maintenance operation
     * Expected Result: The data are deleted correctly
     * @returns void
     */
    public function test_delete_curative_maintenance_operation()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'state_id' => $mostRecentlyEqState->id,
        ]);

        $response = $this->post('/state/delete/curMtnOp/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'reason' => 'equipment',
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test Conception Number: 31
     * Delete a validated curative maintenance operation
     * Expected Result: Receiving an error:
     *                                      "You can't delete a curative maintenance operation validated'
     * @returns void
     */
    public function test_delete_validated_curative_maintenance_operation()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'state_id' => $mostRecentlyEqState->id,
        ]);

        $response = $this->post('/curMtnOp/realize/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'reason' => 'equipment',
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/curMtnOp/qualityVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/curMtnOp/technicalVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        CurativeMaintenanceOperation::all()->last()->update([
            'enteredBy_id' => User::all()->last()->id,
        ]);

        $response = $this->post('/state/delete/curMtnOp/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'reason' => 'equipment',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_delete' => 'You can\'t delete a curative maintenance operation validated',
        ]);
    }

    /**
     * Test Conception Number: 32
     * Technical approve a curative maintenance operation with correct user information
     * Expected Result: The curative maintenance operation is approved
     * @returns void
     */
    public function test_technical_approve_curative_maintenance_operation_with_correct_user_information()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/curMtnOp/technicalVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'state_id' => $mostRecentlyEqState->id,
            'technicalVerifier_id' => User::all()->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 33
     * Technical approve a curative maintenance operation with incorrect user information
     * Expected Result: Receiving an error:
     *                                      "Verification failed, the couple pseudo password isn't recognized"
     * @returns void
     */
    public function test_technical_approve_curative_maintenance_operation_with_incorrect_user_information()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/curMtnOp/technicalVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'test',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'connexion' => 'Verification failed, the couple pseudo password isn\'t recognized',
        ]);
    }

    /**
     * Test Conception Number: 34
     * Quality approve a curative maintenance operation with correct user information
     * Expected Result: The curative maintenance operation is approved
     * @returns void
     */
    public function test_quality_approve_curative_maintenance_operation_with_correct_user_information()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/curMtnOp/qualityVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'state_id' => $mostRecentlyEqState->id,
            'qualityVerifier_id' => User::all()->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 35
     * Quality approve a curative maintenance operation with incorrect user information
     * Expected Result: Receiving an error:
     *                                      "Verification failed, the couple pseudo password isn't recognized"
     * @returns void
     */
    public function test_quality_approve_curative_maintenance_operation_with_incorrect_user_information()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/curMtnOp/qualityVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'test',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'connexion' => 'Verification failed, the couple pseudo password isn\'t recognized',
        ]);
    }

    /**
     * Test Conception Number: 36
     * Realize a curative maintenance operation with correct user information
     * Expected Result: The curative maintenance operation is realized
     * @returns void
     */
    public function test_realize_curative_maintenance_operation_with_correct_user_information()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/curMtnOp/realize/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'state_id' => $mostRecentlyEqState->id,
            'realizedBy_id' => User::all()->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 37
     * Realize a curative maintenance operation with incorrect user information
     * Expected Result: Receiving an error:
     *                                      "Verification failed, the couple pseudo password isn't recognized"
     * @returns void
     */
    public function test_realize_curative_maintenance_operation_with_incorrect_user_information()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyEqState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyEqState->created_at;
            if ($date >= $date2) {
                $mostRecentlyEqState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyEqState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/curMtnOp/realize/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'test',
            'user_password' => 'test',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'connexion' => 'Verification failed, the couple pseudo password isn\'t recognized',
        ]);
    }
}
