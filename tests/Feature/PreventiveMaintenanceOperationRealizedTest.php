<?php

namespace Tests\Feature;

use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\PreventiveMaintenanceOperation;
use App\Models\SW01\PreventiveMaintenanceOperationRealized;
use App\Models\SW01\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreventiveMaintenanceOperationRealizedTest extends TestCase
{
    use RefreshDatabase;

    public function addUser()
    {
        if (User::all()->count() === 0) {
            $countUser = User::all()->count();
            $response = $this->post('register', [
                'user_firstName' => 'Verifier',
                'user_lastName' => 'Verifier',
                'user_pseudo' => 'Verifier',
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
     * Add new preventive maintenance realized as drafted with no values
     * Start date: /
     * End date: /
     * Report number: /
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a report number for the preventive maintenance operation realized"
     * @returns void
     */
    public function test_add_drafted_preventive_maintenance_realized_with_no_values()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOpRlz_reportNumber' => 'You must enter a report number for the preventive maintenance operation realized',
        ]);
    }

    public function create_equipment($name, $validated = 'drafted')
    {
        $user_id = $this->addUser();
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
        return Equipment::all()->last()->id;
    }

    public function add_prevMaintenance($eq_id, $name, $validated = 'drafted', $periodicity = 1, $symbolPeriodicity = 'M', $preventiveOperation = true)
    {
        $user_id = $this->addUser();
        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => $validated,
            'prvMtnOp_description' => $name,
            'prvMtnOp_protocol' => $name,
            'prvMtnOp_periodicity' => $periodicity,
            'prvMtnOp_symbolPeriodicity' => $symbolPeriodicity,
            'prvMtnOp_preventiveOperation' => $preventiveOperation,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => $validated,
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => $name,
            'prvMtnOp_protocol' => $name,
            'prvMtnOp_periodicity' => $periodicity,
            'prvMtnOp_symbolPeriodicity' => $symbolPeriodicity,
            'prvMtnOp_preventiveOperation' => $preventiveOperation,
        ]);
        $response->assertStatus(200);
        $nextDate = null;
        switch ($symbolPeriodicity) {
            case 'H':
                $nextDate = Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addHours($periodicity)->format('Y-m-d H:i:s');
                break;
            case 'D':
                $nextDate = Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addDays($periodicity)->format('Y-m-d H:i:s');
                break;
            case 'M':
                $nextDate = Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addMonths($periodicity)->format('Y-m-d H:i:s');
                break;
            case 'Y':
                $nextDate = Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addYears($periodicity)->format('Y-m-d H:i:s');
                break;
            default:
                break;
        }
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => $preventiveOperation,
            'prvMtnOp_description' => $name,
            'prvMtnOp_protocol' => $name,
            'prvMtnOp_validate' => $validated,
            'prvMtnOp_periodicity' => $periodicity,
            'prvMtnOp_symbolPeriodicity' => $symbolPeriodicity,
            'prvMtnOp_nextDate' => $nextDate,
        ]);
    }

    /**
     * Test Conception Number: 2
     * Add new preventive maintenance realized as drafted with too short report number
     * Start date: /
     * End date: /
     * Report number: "in"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least three characters"
     * @returns void
     */
    public function test_add_drafted_preventive_maintenance_realized_with_too_short_report_number()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_reportNumber' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOpRlz_reportNumber' => 'You must enter at least three characters',
        ]);
    }

    /**
     * Test Conception Number: 3
     * Add new preventive maintenance realized as drafted with too long report number
     * Start date: /
     * End date: /
     * Report number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_drafted_preventive_maintenance_realized_with_too_long_report_number()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_reportNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOpRlz_reportNumber' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 4
     * Add new preventive maintenance realized as drafted with too long comment
     * Start date: /
     * End date: /
     * Report number: "three"
     * Comment: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_drafted_preventive_maintenance_realized_with_too_long_comment()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_reportNumber' => 'three',
            'prvMtnOpRlz_comment' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ",
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOpRlz_comment' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 5
     * Add new preventive maintenance realized as drafted with only a correct report number
     * Start date: /
     * End date: /
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You have to entered the startDate of your preventive maintenance operation realized"
     * @returns void
     */
    public function test_add_drafted_preventive_maintenance_realized_with_only_a_correct_report_number()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_startDate' => 'You have to entered the startDate of your preventive maintenance operation realized',
        ]);
    }

    /**
     * Test Conception Number: 6
     * Add new preventive maintenance realized as drafted with only a start date older than one month
     * Start date: Today - 2 month
     * End date: /
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You can't enter a date that is older than one month"
     * @returns void
     */
    public function test_add_drafted_preventive_maintenance_realized_with_only_a_start_date_older_than_one_month()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->subMonth()->subMonth()->format('Y-m-d H:i:s'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_startDate' => 'You can\'t enter a date that is older than one month',
        ]);
    }

    /**
     * Test Conception Number: 7
     * Add new preventive maintenance realized as drafted with only an end date older than one month
     * Start date: Today
     * End date: Today - 2 month
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You can't enter a date that is older than one month"
     * @returns void
     */
    public function test_add_drafted_preventive_maintenance_realized_with_only_an_end_date_older_than_one_month()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->subMonth()->subMonth()->format('Y-m-d H:i:s'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_endDate' => 'You can\'t enter a date that is older than one month',
        ]);
    }

    /**
     * Test Conception Number: 8
     * Add new preventive maintenance realized as drafted with only a start date older than the end date
     * Start date: Today + 1 month
     * End date: Today
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must entered a startDate that is before endDate"
     * @returns void
     */
    public function test_add_drafted_preventive_maintenance_realized_with_only_a_start_date_older_than_the_end_date()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->addMonth()->format('Y-m-d H:i:s'),
            'prvMtnOpRlz_endDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_endDate' => 'You must entered a startDate that is before endDate',
        ]);
    }

    /**
     * Test Conception Number: 9
     * Add new preventive maintenance realized as drafted with only a start date before the start date of the state linked to the equipment
     * Start date: Today - 10 days
     * End date: /
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You can't entered this startDate because it must be after the startDate of the state"
     * @returns void
     */
    public function test_add_drafted_preventive_maintenance_realized_with_only_a_start_date_before_the_start_date_of_the_state_linked_to_the_equipment()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->subDays(10)->format('Y-m-d H:i:s'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_startDate' => 'You can\'t entered this startDate because it must be after the startDate of the state',
        ]);
    }

    /**
     * Test Conception Number: 10
     * Add new preventive maintenance realized as drafted with only a start date before the start date of the state linked to the equipment
     * Start date: Today
     * End date: Today - 10 days
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You can't entered this endDate because it must be after the startDate of the state"
     * @returns void
     */
    public function test_add_drafted_preventive_maintenance_realized_with_only_a_end_date_before_the_start_date_of_the_state_linked_to_the_equipment()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->subDays(10)->format('Y-m-d H:i:s'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_endDate' => 'You can\'t entered this endDate because it must be after the startDate of the state',
        ]);
    }

    /**
     * Test Conception Number: 11
     * Add new preventive maintenance realized as drafted with correct values
     * Start date: Today
     * End date: Today + 1 month
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: The preventive maintenance realized is added to the database
     * @returns void
     */
    public function test_add_drafted_preventive_maintenance_realized_with_correct_values()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operation_realizeds', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);
    }

    /**
     * Test Conception Number: 12
     * Add new preventive maintenance realized as to be validated with no values
     * Start date: /
     * End date: /
     * Report number: /
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a report number for the preventive maintenance operation realized"
     * @returns void
     */
    public function test_add_to_be_validated_preventive_maintenance_realized_with_no_values()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOpRlz_reportNumber' => 'You must enter a report number for the preventive maintenance operation realized',
        ]);
    }

    /**
     * Test Conception Number: 13
     * Add new preventive maintenance realized as to be validated with too short report number
     * Start date: /
     * End date: /
     * Report number: "in"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least three characters"
     * @returns void
     */
    public function test_add_to_be_validated_preventive_maintenance_realized_with_too_short_report_number()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_reportNumber' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOpRlz_reportNumber' => 'You must enter at least three characters',
        ]);
    }

    /**
     * Test Conception Number: 14
     * Add new preventive maintenance realized as to be validated with too long report number
     * Start date: /
     * End date: /
     * Report number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_to_be_validated_preventive_maintenance_realized_with_too_long_report_number()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_reportNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOpRlz_reportNumber' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 15
     * Add new preventive maintenance realized as to be validated with too long comment
     * Start date: /
     * End date: /
     * Report number: "three"
     * Comment: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_to_be_validated_preventive_maintenance_realized_with_too_long_comment()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_reportNumber' => 'three',
            'prvMtnOpRlz_comment' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ",
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOpRlz_comment' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 16
     * Add new preventive maintenance realized as to be validated with only a correct report number
     * Start date: /
     * End date: /
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You have to entered the startDate of your preventive maintenance operation realized"
     * @returns void
     */
    public function test_add_to_be_validated_preventive_maintenance_realized_with_only_a_correct_report_number()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_startDate' => 'You have to entered the startDate of your preventive maintenance operation realized',
        ]);
    }

    /**
     * Test Conception Number: 17
     * Add new preventive maintenance realized as to be validated with only a start date older than one month
     * Start date: Today - 2 month
     * End date: /
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You can't enter a date that is older than one month"
     * @returns void
     */
    public function test_add_to_be_validated_preventive_maintenance_realized_with_only_a_start_date_older_than_one_month()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->subMonth()->subMonth()->format('Y-m-d H:i:s'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_startDate' => 'You can\'t enter a date that is older than one month',
        ]);
    }

    /**
     * Test Conception Number: 18
     * Add new preventive maintenance realized as to be validated with only an end date older than one month
     * Start date: Today
     * End date: Today - 2 month
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You can't enter a date that is older than one month"
     * @returns void
     */
    public function test_add_to_be_validated_preventive_maintenance_realized_with_only_an_end_date_older_than_one_month()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->subMonth()->subMonth()->format('Y-m-d H:i:s'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_endDate' => 'You can\'t enter a date that is older than one month',
        ]);
    }

    /**
     * Test Conception Number: 19
     * Add new preventive maintenance realized as to be validated with only a start date older than the end date
     * Start date: Today + 1 month
     * End date: Today
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must entered a startDate that is before endDate"
     * @returns void
     */
    public function test_add_to_be_validated_preventive_maintenance_realized_with_only_a_start_date_older_than_the_end_date()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->addMonth()->format('Y-m-d H:i:s'),
            'prvMtnOpRlz_endDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_endDate' => 'You must entered a startDate that is before endDate',
        ]);
    }

    /**
     * Test Conception Number: 20
     * Add new preventive maintenance realized as to be validated with only a start date before the start date of the state linked to the equipment
     * Start date: Today - 10 days
     * End date: /
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You can't entered this startDate because it must be after the startDate of the state"
     * @returns void
     */
    public function test_add_to_be_validated_preventive_maintenance_realized_with_only_a_start_date_before_the_start_date_of_the_state_linked_to_the_equipment()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->subDays(10)->format('Y-m-d H:i:s'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_startDate' => 'You can\'t entered this startDate because it must be after the startDate of the state',
        ]);
    }

    /**
     * Test Conception Number: 21
     * Add new preventive maintenance realized as to be validated with only a start date before the start date of the state linked to the equipment
     * Start date: Today
     * End date: Today - 10 days
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You can't entered this endDate because it must be after the startDate of the state"
     * @returns void
     */
    public function test_add_to_be_validated_preventive_maintenance_realized_with_only_a_end_date_before_the_start_date_of_the_state_linked_to_the_equipment()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'test', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->subDays(10)->format('Y-m-d H:i:s'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,

        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_endDate' => 'You can\'t entered this endDate because it must be after the startDate of the state',
        ]);
    }

    /**
     * Test Conception Number: 22
     * Add new preventive maintenance realized as to be validated with correct values
     * Start date: Today
     * End date: Today + 1 month
     * Report number: "three"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: The preventive maintenance realized is added to the database
     * @returns void
     */
    public function test_add_to_be_validated_preventive_maintenance_realized_with_correct_values()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Month', 'validated');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);
        $this->assertDatabaseHas('preventive_maintenance_operation_realizeds', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);

        $this->add_prevMaintenance($eq_id, 'Hours', 'validated', 1024, 'H');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);
        $this->assertDatabaseHas('preventive_maintenance_operation_realizeds', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);

        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);
        $this->assertDatabaseHas('preventive_maintenance_operation_realizeds', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);

        $this->add_prevMaintenance($eq_id, 'Year', 'validated', 1, 'Y');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);
        $this->assertDatabaseHas('preventive_maintenance_operation_realizeds', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);
    }

    /**
     * Test Conception Number: 23
     * Add new preventive maintenance realized as validated with no values
     * Start date: /
     * End date: /
     * Report number: /
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You have to entered the endDate of your preventive maintenance operation realized for validate it"
     * @returns void
     */
    public function test_add_new_prevMaintenance_realized_as_validated_with_no_values()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'validated',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_endDate' => 'You have to entered the endDate of your preventive maintenance operation realized for validate it',
        ]);
    }

    /**
     * Test Conception Number: 24
     * Add new preventive maintenance realized as validated with only an end date
     * Start date: /
     * End date: Today
     * Report number: /
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You have to entered the realizator of this preventive maintenance operation realized for validate it"
     * @returns void
     */
    public function test_add_new_prevMaintenance_realized_as_validated_with_only_an_end_date()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_endDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_validate' => 'validated',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_validate' => 'You have to entered the realizator of this preventive maintenance operation realized for validate it',
        ]);
    }

    /**
     * Test Conception Number: 25
     * Add new preventive maintenance realized as validated with an end date and a realizator
     * Start date: /
     * End date: Today
     * Report number: /
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a report number for the preventive maintenance operation realized"
     * @returns void
     */
    public function test_add_new_prevMaintenance_realized_as_validated_with_an_end_date_and_a_realizator()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_endDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_validate' => 'validated',
            'realizedBy_id' => User::all()->last()->id,
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOpRlz_reportNumber' => 'You must enter a report number for the preventive maintenance operation realized',
        ]);
    }

    /**
     * Test Conception Number: 26
     * Add new preventive maintenance realized as validated with a too short report number
     * Start date: /
     * End date: Today
     * Report number: "1"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least three characters"
     * @returns void
     */
    public function test_add_new_prevMaintenance_realized_as_validated_with_a_too_short_report_number()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_endDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_validate' => 'validated',
            'realizedBy_id' => User::all()->last()->id,
            'prvMtnOpRlz_reportNumber' => '1',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOpRlz_reportNumber' => 'You must enter at least three characters',
        ]);
    }

    /**
     * Test Conception Number: 27
     * Add new preventive maintenance realized as validated with a too long report number
     * Start date: /
     * End date: Today
     * Report number: "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_new_prevMaintenance_realized_as_validated_with_a_too_long_report_number()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_endDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_validate' => 'validated',
            'realizedBy_id' => User::all()->last()->id,
            'prvMtnOpRlz_reportNumber' => '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOpRlz_reportNumber' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 28
     * Add new preventive maintenance realized as validated with a too long comment
     * Start date: /
     * End date: Today
     * Report number: "12345"
     * Comment: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non '
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_new_prevMaintenance_realized_as_validated_with_a_too_long_comment()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_endDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_validate' => 'validated',
            'realizedBy_id' => User::all()->last()->id,
            'prvMtnOpRlz_reportNumber' => '12345',
            'prvMtnOpRlz_comment' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'prvMtnOpRlz_comment' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 29
     * Add new preventive maintenance realized as validated with correct data and an end date + the realizator
     * Start date: /
     * End date: Today + 1 day
     * Report number: "12345"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You have to entered the startDate of your preventive maintenance operation realized"
     * @returns void
     */
    public function test_add_new_prevMaintenance_realized_as_validated_with_correct_data_and_end_data_and_realizator()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_endDate' => Carbon::now()->addDay()->format('Y-m-d H:i:s'),
            'prvMtnOpRlz_validate' => 'validated',
            'realizedBy_id' => User::all()->last()->id,
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_startDate' => 'You have to entered the startDate of your preventive maintenance operation realized',
        ]);
    }

    /**
     * Test Conception Number: 30
     * Add new preventive maintenance realized as validated with correct data and an both date + the realizator
     * Start date: Today
     * End date: Today + 1 month
     * Report number: "12345"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: The preventive maintenance operation realized is added to the database
     * @returns void
     */
    public function test_add_new_prevMaintenance_realized_as_validated_with_correct_data_and_both_date_and_realizator()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $response = $this->post('/prvMtnOpRlz/verif', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_validate' => 'validated',
            'realizedBy_id' => User::all()->last()->id,
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_validate' => 'validated',
            'realizedBy_id' => User::all()->last()->id,
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test Conception Number: 31
     * Update a preventive maintenance realized as to be validated with correct data and an both date
     * Start date: Today
     * End date: Today + 1 month
     * Report number: "12345"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: The differents data are updated in the database
     * @returns void
     */
    public function test_update_prevMaintenance_realized_as_to_be_validated_with_correct_data_and_both_date()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'H');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'reason' => 'update',
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/update/state/prvMtnOpRlz/'.PreventiveMaintenanceOperationRealized::all()->last()->id, [
            'reason' => 'update',
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);

        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'reason' => 'update',
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/update/state/prvMtnOpRlz/'.PreventiveMaintenanceOperationRealized::all()->last()->id, [
            'reason' => 'update',
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);

        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7);
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'reason' => 'update',
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/update/state/prvMtnOpRlz/'.PreventiveMaintenanceOperationRealized::all()->last()->id, [
            'reason' => 'update',
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'Y');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'reason' => 'update',
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/update/state/prvMtnOpRlz/'.PreventiveMaintenanceOperationRealized::all()->last()->id, [
            'reason' => 'update',
            'prvMtnOpRlz_validate' => 'to_be_validated',
            'prvMtnOpRlz_id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test Conception Number: 33
     * Update a preventive maintenance realized as validated with correct data and an both date
     * Start date: Today
     * End date: Today + 1 month
     * Report number: "12345"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receive an error:
     *                                    "You have to entered the realizator of this preventive maintenance operation realized for validate it"
     * @returns void
     */
    public function test_update_prevMaintenance_realized_as_validated_with_correct_data_and_both_date()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
        ]);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'reason' => 'update',
            'prvMtnOpRlz_validate' => 'validated',
            'prvMtnOpRlz_id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_validate' => 'You have to entered the realizator of this preventive maintenance operation realized for validate it',
        ]);
    }

    /**
     * Test Conception Number: 33
     * Update a preventive maintenance realized as validated with correct data and an both date + the realizator
     * Start date: Today
     * End date: Today + 1 month
     * Report number: "12345"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receive an error:
     *                                    "You have to entered the person who approved this preventive maintenance operation realized for validate it"
     * @returns void
     */
    public function test_update_prevMaintenance_realized_as_validated_with_correct_data_and_both_date_and_realizator()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'realizedBy_id' => User::all()->last()->id,
        ]);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'reason' => 'update',
            'prvMtnOpRlz_validate' => 'validated',
            'prvMtnOpRlz_id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_validate' => 'You have to entered the person who approved this preventive maintenance operation realized for validate it',
        ]);
    }

    /**
     * Test Conception Number: 34
     * Update a preventive maintenance realized as validated with correct data and an both date + the realizator + the approver
     * Start date: Today
     * End date: Today + 1 month
     * Report number: "12345"
     * Comment: /
     * Symbol Periodicity: /
     * Expected Result: Receive an error:
     *                                    "You can't update a preventive maintenance operation realized already validated"
     * @returns void
     */
    public function test_update_prevMaintenance_realized_as_validated_with_correct_data_and_both_date_and_realizator_and_approver()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'realizedBy_id' => User::all()->last()->id,
        ]);

        $response = $this->post('/prvMtnOpRlz/approve/'.PreventiveMaintenanceOperationRealized::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'Verifier',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'reason' => 'update',
            'prvMtnOpRlz_validate' => 'validated',
            'prvMtnOpRlz_id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => '12345',
            'user_id' => User::all()->last()->id,
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_validate' => 'You can\'t update a preventive maintenance operation realized already validated',
        ]);
    }

    /**
     * Test Conception Number: 35
     * Delete a preventive maintenance realized validated
     * Expected Result: Receive an error:
     *                                    "You can delete a preventive maintenance operation realized validated"
     * @returns void
     */
    public function test_delete_prevMaintenance_realized_validated()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'realizedBy_id' => User::all()->last()->id,
        ]);

        $response = $this->post('/prvMtnOpRlz/approve/'.PreventiveMaintenanceOperationRealized::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'Verifier',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/state/delete/prvMtnOpRlz/'.PreventiveMaintenanceOperationRealized::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'Verifier',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_delete' => 'You can delete a preventive maintenance operation realized validated',
        ]);
    }

    /**
     * Test Conception Number: 36
     * Delete a preventive maintenance realized
     * Expected Result: The preventive maintenance realized is deleted
     * @returns void
     */
    public function test_delete_prevMaintenance_realized()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'realizedBy_id' => User::all()->last()->id,
        ]);

        $id = PreventiveMaintenanceOperationRealized::all()->last()->id;
        $response = $this->post('/state/delete/prvMtnOpRlz/'.$id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'Verifier',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('preventive_maintenance_operation_realizeds', [
            'id' => $id,
        ]);
    }

    /**
     * Test Conception Number: 37
     * Send a preventive maintenance realized list (id of state)
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_prevMaintenance_realized_list_state_id()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'realizedBy_id' => User::all()->last()->id,
        ]);

        $response = $this->post('/prvMtnOpRlz/approve/'.PreventiveMaintenanceOperationRealized::all()->last()->id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'Verifier',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);

        PreventiveMaintenanceOperationRealized::all()->last()->update([
            'enteredBy_id' => User::all()->last()->id,
        ]);

        $response = $this->get('/state/prvMtnOpRlz/send/'.State::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
                'prvMtnOpRlz_reportNumber' => 'three',
                'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
                'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
                'prvMtnOpRlz_entryDate' => Carbon::now()->format('Y-m-d'),
                'prvMtnOpRlz_validate' => 'validated',
                'prvMtnOpRlz_comment' => null,
                'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
                'prvMtnOp_number' => '1',
                'prvMtnOp_description' => 'Days',
                'prvMtnOp_protocol' => 'Days',
                'realizedBy_firstName' => 'Verifier',
                'realizedBy_lastName' => 'Verifier',
                'enteredBy_firstName' => 'Verifier',
                'enteredBy_lastName' => 'Verifier',
                'approvedBy_firstName' => 'Verifier',
                'approvedBy_lastName' => 'Verifier',
            ],
        ]);
    }

    /**
     * Test Conception Number: 38
     * Approve a preventive maintenance realized with correct user information
     * Expected Result: The preventive maintenance realized is approved
     * @returns void
     */
    public function test_approve_prevMaintenance_realized_correct_user_info()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'realizedBy_id' => User::all()->last()->id,
        ]);

        $id = PreventiveMaintenanceOperationRealized::all()->last()->id;

        $response = $this->post('/prvMtnOpRlz/approve/'.$id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'Verifier',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operation_realizeds', [
            'id' => $id,
            'approvedBy_id' => User::all()->last()->id,
            'prvMtnOpRlz_validate' => 'validated',
        ]);
    }

    /**
     * Test Conception Number: 39
     * Approve a preventive maintenance realized with incorrect user information
     * Expected Result: Receiving an error:
     *                                          "Verification failed, the couple pseudo password isn't recognized"
     * @returns void
     */
    public function test_approve_prevMaintenance_realized_incorrect_user_info()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'realizedBy_id' => User::all()->last()->id,
        ]);

        $id = PreventiveMaintenanceOperationRealized::all()->last()->id;

        $response = $this->post('/prvMtnOpRlz/approve/'.$id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'Verifier',
            'user_password' => 'Verifier',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'connexion' => 'Verification failed, the couple pseudo password isn\'t recognized',
        ]);
    }

    /**
     * Test Conception Number: 40
     * Realize a preventive maintenance realized with correct user information
     * Expected Result: The preventive maintenance realized is realized
     * @returns void
     */
    public function test_realize_prevMaintenance_realized_correct_user_info()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'realizedBy_id' => User::all()->last()->id,
        ]);

        $id = PreventiveMaintenanceOperationRealized::all()->last()->id;

        $response = $this->post('/prvMtnOpRlz/realize/'.$id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'Verifier',
            'user_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operation_realizeds', [
            'id' => $id,
            'realizedBy_id' => User::all()->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 41
     * Realize a preventive maintenance realized with incorrect user information
     * Expected Result: Receiving an error:
     *                                          "Verification failed, the couple pseudo password isn't recognized"
     * @returns void
     */
    public function test_realize_prevMaintenance_realized_incorrect_user_info()
    {
        $eq_id = $this->create_equipment('test', 'validated');
        $this->add_prevMaintenance($eq_id, 'Days', 'validated', 7, 'D');
        $this->post('/equipment/add/state/prvMtnOpRlz', [
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'state_id' => State::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_endDate' => Carbon::now()->addMonth()->format('Y-m-d'),
            'prvMtnOpRlz_reportNumber' => 'three',
            'realizedBy_id' => User::all()->last()->id,
        ]);

        $id = PreventiveMaintenanceOperationRealized::all()->last()->id;

        $response = $this->post('/prvMtnOpRlz/realize/'.$id, [
            'user_id' => User::all()->last()->id,
            'user_pseudo' => 'Verifier',
            'user_password' => 'Verifier',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'connexion' => 'Verification failed, the couple pseudo password isn\'t recognized',
        ]);
    }
}

