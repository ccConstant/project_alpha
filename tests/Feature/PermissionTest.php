<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\SW01\CurativeMaintenanceOperation;
use App\Models\SW01\Dimension;
use App\Models\SW01\EnumDimensionName;
use App\Models\SW01\EnumDimensionType;
use App\Models\SW01\EnumDimensionUnit;
use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\EnumRiskFor;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\Mme;
use App\Models\SW01\MmeState;
use App\Models\SW01\Power;
use App\Models\SW01\PreventiveMaintenanceOperation;
use App\Models\SW01\PreventiveMaintenanceOperationRealized;
use App\Models\SW01\Risk;
use App\Models\SW01\SpecialProcess;
use App\Models\SW01\State;
use App\Models\SW01\Usage;
use App\Models\SW01\Verification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Conception Number: 1
     * Try to add as validated equipment without the permission
     * Expected result: Receiving an error:
     *                                      "You don't have the user right to save an equipment ID as validated"
     * @return void
     */
    public function test_add_new_equipment_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        // Try to add equipment as validated
        $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
        $response = $this->post('/equipment/enum/massUnit/add', [
            'value' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        $countEqType = EnumEquipmentType::all()->count();
        $response = $this->post('/equipment/enum/type/add', [
            'value' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'eq_type' => 'three',
            'eq_massUnit' => 'three',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_internalReference' => 'You don\'t have the user right to save an equipment ID as validated'
        ]);
    }

    public function make_a_user_with_no_permission()
    {
        $this->post('/logout');
        $countUsers = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 'three',
            'user_password' => 'Xn!jkpc!)B640!{$A1MB',
            'user_confirmation_password' => 'Xn!jkpc!)B640!{$A1MB'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countUsers + 1, User::all()->count());
        $this->assertDatabaseHas('users', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 'three'
        ]);
        $this->assertTrue(Hash::check('Xn!jkpc!)B640!{$A1MB', User::all()->where('user_pseudo', '==', 'three')->first()->password));
        return User::all()->where('user_pseudo', '==', 'three')->first()->id;
    }

    /**
     * Test Conception Number: 2
     * Try to update as drafted or to be validated equipment without the permission
     * Expected result: Receiving an error:
     *                                          "You don't have the user right to update an equipment ID save as drafted or in to be validated"
     * @return void
     */
    public function test_update_new_equipment_as_drafted_tbv()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq();

        // Try to update equipment as drafted
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'createdBy_id' => $user_id,
            'eq_id' => $eq_id,
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'other',
            'eq_externalReference' => 'other',
            'eq_name' => 'other',
            'eq_serialNumber' => 'other',
            'eq_constructor' => 'other',
            'eq_mass' => 1234,
            'eq_remarks' => 'other',
            'eq_set' => 'other',
            'eq_location' => 'other',
            'eq_type' => 'other',
            'eq_massUnit' => 'other',

        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_internalReference' => 'You don\'t have the user right to update an equipment ID save as drafted or in to be validated'
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => $eq_id,
            'eqTemp_version' => 1,
            'eqTemp_location' => 'three',
            'eqTemp_validate' => 'drafted',
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_mass' => 1234,
            'eqTemp_remarks' => 'three',
            'eqTemp_mobility' => null,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'External')->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'kg')->first()->id,
        ]);
    }

    public function add_eq($validate = 'drafted', $signed = false)
    {
        $this->create_required_enum();
        $admin = $this->make_a_user_with_permission();
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
        if (EnumDimensionName::all()->where('value', '=', 'Length')->count() === 0) {
            $countDimName = EnumDimensionName::all()->count();
            $response = $this->post('/dimension/enum/name/add', [
                'value' => 'Length',
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
        if (EnumRiskFor::all()->where('value', '=', 'Risk')->count() === 0) {
            $countDimName = EnumRiskFor::all()->count();
            $response = $this->post('/risk/enum/riskfor/add', [
                'value' => 'Risk',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimName + 1, EnumRiskFor::all());
        }
    }

    public function make_a_user_with_permission()
    {
        $this->post('/logout');
        if (User::all()->where('user_pseudo', '==', 'admin')->count() === 0) {
            $countUser = User::all()->count();
            $response = $this->post('register', [
                'user_firstName' => 'admin',
                'user_lastName' => 'admin',
                'user_pseudo' => 'admin',
                'user_password' => 'VerifierVerifier',
                'user_confirmation_password' => 'VerifierVerifier',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countUser + 1, User::all());
            $user = User::all()->where('user_pseudo', '==', 'admin')->first();
            $user->update([
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
     * Test Conception Number: 3
     * Try to update validated equipment without the permission
     * Expected result: Receiving an error:
     *                                          "You don't have the user right to update an equipment ID save as validated"
     * @return void
     */
    public function test_update_new_equipment_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        // Try to update equipment as validated
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'createdBy_id' => $user_id,
            'eq_id' => $eq_id,
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'other',
            'eq_externalReference' => 'other',
            'eq_name' => 'other',
            'eq_serialNumber' => 'other',
            'eq_constructor' => 'other',
            'eq_mass' => 1234,
            'eq_remarks' => 'other',
            'eq_set' => 'other',
            'eq_location' => 'other',
            'eq_type' => 'other',
            'eq_massUnit' => 'other',

        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_internalReference' => 'You don\'t have the user right to update an equipment ID save as validated'
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => $eq_id,
            'eqTemp_version' => 1,
            'eqTemp_location' => 'three',
            'eqTemp_validate' => 'validated',
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_mass' => 1234,
            'eqTemp_remarks' => 'three',
            'eqTemp_mobility' => null,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'External')->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'kg')->first()->id,
        ]);
    }

    /**
     * Test Conception Number: 4
     * Try to update signed equipment without the permission
     * Expected result: Receiving an error:
     *                                          "You don't have the user right to update an equipment ID signed"
     * @return void
     */
    public function test_update_signed_equipment_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_updateDataValidatedButNotSignedRight' => 1
        ]);

        $eq_id = $this->add_eq('validated', true);

        // Try to update equipment as validated
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'createdBy_id' => $user_id,
            'eq_id' => $eq_id,
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'other',
            'eq_externalReference' => 'other',
            'eq_name' => 'other',
            'eq_serialNumber' => 'other',
            'eq_constructor' => 'other',
            'eq_mass' => 1234,
            'eq_remarks' => 'other',
            'eq_set' => 'other',
            'eq_location' => 'other',
            'eq_type' => 'other',
            'eq_massUnit' => 'other',
            'lifesheet_created' => true,

        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_internalReference' => 'You don\'t have the user right to update an equipment ID signed'
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => $eq_id,
            'eqTemp_version' => 1,
            'eqTemp_location' => 'three',
            'eqTemp_validate' => 'validated',
            'eqTemp_lifeSheetCreated' => 1,
            'eqTemp_mass' => 1234,
            'eqTemp_remarks' => 'three',
            'eqTemp_mobility' => null,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'External')->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'kg')->first()->id,
        ]);
    }

    /**
     * Test Conception Number: 5
     * Try to add a new preventive maintenance as validated to equipment without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to save a preventive maintenance operation as validated"
     * @return void
     */
    public function test_add_new_preventive_maintenance_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You don\'t have the user right to save a preventive maintenance operation as validated'
        ]);
    }

    /**
     * Test Conception Number: 6
     * Try to update a preventive maintenance as drafted to equipment without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to update a preventive maintenance operation save as drafted or in to be validated"
     * @return void
     */
    public function test_update_preventive_maintenance_as_draft_tbv()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'drafted',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOp/verif', [
            'reason' => 'update',
            'prvMtnOp_validate' => 'to_be_validated',
            'prvMtnOp_description' => 'other',
            'prvMtnOp_protocol' => 'other',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You don\'t have the user right to update a preventive maintenance operation save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 7
     * Try to update a preventive maintenance as validated to equipment without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to update a preventive maintenance operation save as validated"
     * @return void
     */
    public function test_update_preventive_maintenance_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOp/verif', [
            'reason' => 'update',
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'other',
            'prvMtnOp_protocol' => 'other',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You don\'t have the user right to update a preventive maintenance operation save as validated'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Try to update a signed preventive maintenance without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to update a preventive maintenance operation signed"
     * @return void
     */
    public function test_update_preventive_maintenance_as_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_updateDataValidatedButNotSignedRight' => 1
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOp/verif', [
            'reason' => 'update',
            'prvMtnOp_validate' => 'to_be_validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => $user_id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'lifesheet_created' => true,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You don\'t have the user right to update a preventive maintenance operation signed'
        ]);
    }

    /**
     * Test Conception Number: 9
     * Try to delete a preventive maintenance saved as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to delete a preventive maintenance operation save as drafted or in to be validated"
     * @return void
     */
    public function test_delete_preventive_maintenance_as_drafted_or_to_be_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'drafted',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/delete/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'eq_id' => $eq_id,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You don\'t have the user right to delete a preventive maintenance operation save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 10
     * Try to delete a preventive maintenance saved as validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to delete a preventive maintenance operation save as validated"
     * @return void
     */
    public function test_delete_preventive_maintenance_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/delete/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'eq_id' => $eq_id,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You don\'t have the user right to delete a preventive maintenance operation save as validated'
        ]);
    }

    /**
     * Test Conception Number: 11
     * Try to delete a signed preventive maintenance without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to delete a preventive maintenance operation signed"
     * @return void
     */
    public function test_delete_signed_preventive_maintenance()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_deleteDataValidatedLinkedToEqOrMmeRight' => 1
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/delete/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'eq_id' => $eq_id,
            'user_id' => $user_id,
            'lifesheet_created' => true
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_description' => 'You don\'t have the user right to delete a preventive maintenance operation signed'
        ]);
    }

    /**
     * Test Conception Number: 12
     * Try to reform a preventive maintenance without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to reform a preventive maintenance operation"
     * @return void
     */
    public function test_reform_preventive_maintenance()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/reform/prvMtnOp/' . PreventiveMaintenanceOperation::all()->last()->id, [
            'eq_id' => $eq_id,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOp_reformDate' => 'You don\'t have the user right to reform a preventive maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 13
     * Try to add as validated a dimension without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to save a dimension as validated"
     * @return void
     */
    public function test_add_dimension_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value' => '32',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You don\'t have the user right to save a dimension as validated'
        ]);
    }

    /**
     * Test Conception Number: 14
     * Try to update as drafted or to be validated a dimension without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to update a dimension save as drafted or in to be validated"
     * @return void
     */
    public function test_update_dimension_as_draft_or_tbv()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'drafted',
            'dim_value' => '32',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_validate' => 'drafted',
            'eq_id' => $eq_id,
            'dim_value' => '32',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/dimension/verif', [
            'reason' => 'update',
            'dim_validate' => 'drafted',
            'dim_value' => '32',
            'user_id' => $user_id,
            'dim_id' => Dimension::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You don\'t have the user right to update a dimension save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 15
     * Try to update as validated a dimension without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to update a dimension save as validated"
     * @return void
     */
    public function test_update_dimension_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $admin = $this->make_a_user_with_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'user_id' => $admin,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/dimension/verif', [
            'reason' => 'update',
            'dim_validate' => 'drafted',
            'dim_value' => '32',
            'user_id' => $user_id,
            'dim_id' => Dimension::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You don\'t have the user right to update a dimension save as validated'
        ]);
    }

    /**
     * Test Conception Number: 16
     * Try to update a signed dimension without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to update a dimension signed"
     * @return void
     */
    public function test_update_dimension_as_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_updateDataValidatedButNotSignedRight' => 1
        ]);

        $admin = $this->make_a_user_with_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'user_id' => $admin,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/dimension/verif', [
            'reason' => 'update',
            'dim_validate' => 'drafted',
            'dim_value' => '32',
            'user_id' => $user_id,
            'dim_id' => Dimension::all()->last()->id,
            'lifesheet_created' => true
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You don\'t have the user right to update a dimension signed'
        ]);
    }

    /**
     * Test Conception Number: 17
     * Try to delete a dimension saved as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to delete a dimension save as drafted or in to be validated"
     * @return void
     */
    public function test_delete_dimension_as_draft_or_tbv()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->first()->id
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/delete/dim/' . Dimension::all()->last()->id, [
            'eq_id' => $eq_id,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You don\'t have the user right to delete a dimension save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 18
     * Try to delete a dimension saved as validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to delete a dimension save as validated"
     * @return void
     */
    public function test_delete_dimension_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->first()->id
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/delete/dim/' . Dimension::all()->last()->id, [
            'eq_id' => $eq_id,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You don\'t have the user right to delete a dimension save as validated'
        ]);
    }

    /**
     * Test Conception Number: 19
     * Try to delete a signed dimension without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to delete a dimension signed"
     * @return void
     */
    public function test_delete_dimension_as_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_deleteDataValidatedLinkedToEqOrMmeRight' => 1
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->first()->id
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/delete/dim/' . Dimension::all()->last()->id, [
            'eq_id' => $eq_id,
            'user_id' => $user_id,
            'lifesheet_created' => true
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You don\'t have the user right to delete a dimension signed'
        ]);
    }

    /**
     * Test Conception Number: 20
     * Try to add as validated a power source without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to save a power as validated"
     * @return void
     */
    public function test_add_ps_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => $eq_id,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'pow_type' => 'You don\'t have the user right to save a power as validated'
        ]);
    }

    /**
     * Test Conception Number: 21
     * Try to update a power source saved as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to update a power save as drafted or in to be validated"
     * @return void
     */
    public function test_update_ps_as_draft_or_tbv()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => $eq_id,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/power/verif', [
            'reason' => 'update',
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'other',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => $eq_id,
            'user_id' => $user_id,
            'pow_id' => Power::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'pow_type' => 'You don\'t have the user right to update a power save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 22
     * Try to update a power source saved as validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to update a power save as validated"
     * @return void
     */
    public function test_update_ps_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => $eq_id,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/power/verif', [
            'reason' => 'update',
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'other',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => $eq_id,
            'user_id' => $user_id,
            'pow_id' => Power::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'pow_type' => 'You don\'t have the user right to update a power save as validated'
        ]);
    }

    /**
     * Test Conception Number: 23
     * Try to update a signed power source without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to update a power signed"
     * @return void
     */
    public function test_update_ps_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_updateDataValidatedButNotSignedRight' => 1
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => $eq_id,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/power/verif', [
            'reason' => 'update',
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'other',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => $eq_id,
            'user_id' => $user_id,
            'pow_id' => Power::all()->last()->id,
            'lifesheet_created' => true
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'pow_type' => 'You don\'t have the user right to update a power signed'
        ]);
    }

    /**
     * Test Conception Number: 24
     * Try to delete a power source saved as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to delete a power save as drafted or in to be validated"
     * @return void
     */
    public function test_delete_ps_as_drafted_or_to_be_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => $eq_id,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/delete/pow/' . Power::all()->last()->id, [
            'eq_id' => $eq_id,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'pow_type' => 'You don\'t have the user right to delete a power save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 25
     * Try to delete a power source saved as validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to delete a power save as validated"
     * @return void
     */
    public function test_delete_ps_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_deleteDataValidatedButNotSignedRight' => 1
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => $eq_id,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/delete/pow/' . Power::all()->last()->id, [
            'eq_id' => $eq_id,
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'pow_type' => 'You don\'t have the user right to delete a power save as validated'
        ]);
    }

    /**
     * Test Conception Number: 26
     * Try to delete a signed power source saved without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to delete a power signed"
     * @return void
     */
    public function test_delete_ps_as_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_deleteDataValidatedLinkedToEqOrMmeRight' => 1
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => $eq_id,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/delete/pow/' . Power::all()->last()->id, [
            'eq_id' => $eq_id,
            'user_id' => $user_id,
            'lifesheet_created' => true
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'pow_type' => 'You don\'t have the user right to delete a power signed'
        ]);
    }

    /**
     * Test Conception Number: 27
     * Try to add a risk as validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to save a risk as validated"
     * @return void
     */
    public function test_add_risk_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'risk_for' => 'You don\'t have the user right to save a risk as validated'
        ]);
    }

    /**
     * Test Conception Number: 28
     * Try to update a risk save as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to update a risk save as drafted or in to be validated"
     * @return void
     */
    public function test_update_risk_as_draft_or_to_be_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/risk', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);

        $response = $this->post('/risk/verif', [
            'reason' => 'update',
            'risk_validate' => 'drafted',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
            'risk_id' => Risk::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'risk_for' => 'You don\'t have the user right to update a risk save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 29
     * Try to update a risk save as validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to update a risk save as validated"
     * @return void
     */
    public function test_update_risk_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
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

        $response = $this->post('/risk/verif', [
            'reason' => 'update',
            'risk_validate' => 'drafted',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
            'risk_id' => Risk::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'risk_for' => 'You don\'t have the user right to update a risk save as validated'
        ]);
    }

    /**
     * Test Conception Number: 30
     * Try to update a signed risk without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to update a risk signed"
     * @return void
     */
    public function test_update_risk_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_updateDataValidatedButNotSignedRight' => 1
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
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

        $response = $this->post('/risk/verif', [
            'reason' => 'update',
            'risk_validate' => 'drafted',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => $user_id,
            'risk_id' => Risk::all()->last()->id,
            'lifesheet_created' => true
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'risk_for' => 'You don\'t have the user right to update a risk signed'
        ]);
    }

    /**
     * Test Conception Number: 31
     * Try to delete a risk saved as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to delete a risk save as drafted or in to be validated"
     * @return void
     */
    public function test_delete_risk_as_draft_or_to_be_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/risk', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/delete/risk/' . Risk::all()->last()->id, [
            'user_id' => $user_id,
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'risk_for' => 'You don\'t have the user right to delete a risk save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 32
     * Try to delete a risk saved as validated without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to delete a risk save as validated"
     * @return void
     */
    public function test_delete_risk_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
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

        $response = $this->post('/equipment/delete/risk/' . Risk::all()->last()->id, [
            'user_id' => $user_id,
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'risk_for' => 'You don\'t have the user right to delete a risk save as validated'
        ]);
    }

    /**
     * Test Conception Number: 33
     * Try to delete a signed risk without the permission
     * Expected result: Receiving an error:
     *                                         "You don't have the user right to delete a risk signed"
     * @return void
     */
    public function test_delete_risk_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_deleteDataValidatedLinkedToEqOrMmeRight' => 1
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/risk/verif', [
            'risk_validate' => 'validated',
            'risk_remarks' => 'three',
            'risk_wayOfControl' => 'three',
            'risk_for' => 'Risk',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
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

        $response = $this->post('/equipment/delete/risk/' . Risk::all()->last()->id, [
            'user_id' => $user_id,
            'eq_id' => $eq_id,
            'lifesheet_created' => true
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'risk_for' => 'You don\'t have the user right to delete a risk signed'
        ]);
    }

    /**
     * Test Conception Number: 34
     * Try to add a file as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to save a file as validated"
     * @return void
     */
    public function test_add_file_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'file_location' => 'FilePath',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'file_name' => 'You don\'t have the user right to save a file as validated'
        ]);
    }

    /**
     * Test Conception Number: 35
     * Try to update a file saved as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a file save as drafted or in to be validated"
     * @return void
     */
    public function test_update_file_as_draft_or_to_be_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('file/verif', [
            'file_validate' => 'drafted',
            'file_name' => 'File',
            'file_location' => 'FilePath',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/file', [
            'file_validate' => 'drafted',
            'file_name' => 'File',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);

        $response = $this->post('file/verif', [
            'reason' => 'update',
            'file_validate' => 'drafted',
            'file_name' => 'File',
            'file_location' => 'FilePath',
            'user_id' => $user_id,
            'file_id' => File::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'file_name' => 'You don\'t have the user right to update a file save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 36
     * Try to update a file saved as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a file save as validated"
     * @return void
     */
    public function test_update_file_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'file_location' => 'FilePath',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/file', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);

        $response = $this->post('file/verif', [
            'reason' => 'update',
            'file_validate' => 'drafted',
            'file_name' => 'File',
            'file_location' => 'FilePath',
            'user_id' => $user_id,
            'file_id' => File::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'file_name' => 'You don\'t have the user right to update a file save as validated'
        ]);
    }

    /**
     * Test Conception Number: 37
     * Try to update a signed file without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a file signed"
     * @return void
     */
    public function test_update_file_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_updateDataValidatedButNotSignedRight' => 1
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'file_location' => 'FilePath',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/file', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);

        $response = $this->post('file/verif', [
            'reason' => 'update',
            'file_validate' => 'drafted',
            'file_name' => 'File',
            'file_location' => 'FilePath',
            'user_id' => $user_id,
            'file_id' => File::all()->last()->id,
            'lifesheet_created' => true,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'file_name' => 'You don\'t have the user right to update a file signed'
        ]);
    }

    /**
     * Test Conception Number: 38
     * Try to delete a file saved as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to delete a file save as drafted or in to be validated"
     * @return void
     */
    public function test_delete_file_as_draft_or_to_be_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');
        $mme_id = $this->add_mme('validated');

        $response = $this->post('file/verif', [
            'file_validate' => 'drafted',
            'file_name' => 'File',
            'file_location' => 'FilePath',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/file', [
            'file_validate' => 'drafted',
            'file_name' => 'File',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/delete/file/' . File::all()->last()->id, [
            'user_id' => $user_id,
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'file_name' => 'You don\'t have the user right to delete a file save as drafted or in to be validated'
        ]);

        $response = $this->post('/mme/add/file', [
            'file_validate' => 'drafted',
            'file_name' => 'File',
            'mme_id' => $mme_id
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/delete/file/' . File::all()->last()->id, [
            'user_id' => $user_id,
            'mme_id' => $mme_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'file_name' => 'You don\'t have the user right to delete a file save as drafted or in to be validated'
        ]);
    }

    public function add_mme($validate = 'drafted', $signed = false)
    {
        $admin = $this->make_a_user_with_permission();
        $response = $this->post('/mme/verif', [
            'mme_validate' => $validate,
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => $admin
        ]);
        $response->assertStatus(200);
        $countEquipment = Mme::all()->count();
        $response = $this->post('/mme/add', [
            'mme_validate' => $validate,
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Mme::all()->count());
        $this->assertDatabaseHas('mmes', [
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_set' => 'three',
        ]);
        $this->assertDatabaseHas('mme_temps', [
            'mmeTemp_location' => 'three',
            'mmeTemp_validate' => $validate,
            'mmeTemp_remarks' => 'three',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
        ]);
        if ($signed) {
            $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
                'reason' => 'technical',
                'enteredBy_id' => $admin,
            ]);
            $response->assertStatus(200);

            $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
                'reason' => 'quality',
                'enteredBy_id' => $admin,
            ]);
            $response->assertStatus(200);
        }
        return Mme::all()->last()->id;
    }

    /**
     * Test Conception Number: 39
     * Try to delete a file saved as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to delete a file save as validated"
     * @return void
     */
    public function test_delete_file_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');
        $mme_id = $this->add_mme('validated');

        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'file_location' => 'FilePath',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/file', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/delete/file/' . File::all()->last()->id, [
            'user_id' => $user_id,
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'file_name' => 'You don\'t have the user right to delete a file save as validated'
        ]);

        $response = $this->post('/mme/add/file', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'mme_id' => $mme_id
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/delete/file/' . File::all()->last()->id, [
            'user_id' => $user_id,
            'mme_id' => $mme_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'file_name' => 'You don\'t have the user right to delete a file save as validated'
        ]);
    }

    /**
     * Test Conception Number: 40
     * Try to delete a file signed without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to delete a file signed"
     * @return void
     */
    public function test_delete_file_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_deleteDataValidatedLinkedToEqOrMmeRight' => 1
        ]);

        $eq_id = $this->add_eq('validated');
        $mme_id = $this->add_mme('validated');

        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'file_location' => 'FilePath',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/file', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'eq_id' => $eq_id
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/delete/file/' . File::all()->last()->id, [
            'user_id' => $user_id,
            'eq_id' => $eq_id,
            'lifesheet_created' => true,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'file_name' => 'You don\'t have the user right to delete a file signed'
        ]);

        $response = $this->post('/mme/add/file', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'mme_id' => $mme_id
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/delete/file/' . File::all()->last()->id, [
            'user_id' => $user_id,
            'mme_id' => $mme_id,
            'lifesheet_created' => true,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'file_name' => 'You don\'t have the user right to delete a file signed'
        ]);
    }

    /**
     * Test Conception Number: 41
     * Try to add a mme as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to save a MME ID as validated"
     * @return void
     */
    public function test_add_mme_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_internalReference' => 'You don\'t have the user right to save a MME ID as validated'
        ]);
    }

    /**
     * Test Conception Number: 42
     * Try to update a mme saved as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a MME ID save as drafted or in to be validated"
     * @return void
     */
    public function test_update_mme_as_draft_or_to_be_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme();

        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'other',
            'mme_externalReference' => 'other',
            'mme_name' => 'other',
            'mme_serialNumber' => 'other',
            'mme_constructor' => 'other',
            'mme_remarks' => 'other',
            'mme_set' => 'other',
            'mme_location' => 'other',
            'createdBy_id' => $user_id,
            'mme_id' => $mme_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_internalReference' => 'You don\'t have the user right to update a MME ID save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 43
     * Try to update a mme saved as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a MME ID save as validated"
     * @return void
     */
    public function test_update_mme_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'other',
            'mme_externalReference' => 'other',
            'mme_name' => 'other',
            'mme_serialNumber' => 'other',
            'mme_constructor' => 'other',
            'mme_remarks' => 'other',
            'mme_set' => 'other',
            'mme_location' => 'other',
            'createdBy_id' => $user_id,
            'mme_id' => $mme_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_internalReference' => 'You don\'t have the user right to update a MME ID save as validated'
        ]);
    }

    /**
     * Test Conception Number: 44
     * Try to update a signed mme without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a MME ID signed"
     * @return void
     */
    public function test_update_mme_as_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->last()->update([
            'user_updateDataValidatedButNotSignedRight' => 1
        ]);

        $mme_id = $this->add_mme('validated', true);

        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'other',
            'mme_externalReference' => 'other',
            'mme_name' => 'other',
            'mme_serialNumber' => 'other',
            'mme_constructor' => 'other',
            'mme_remarks' => 'other',
            'mme_set' => 'other',
            'mme_location' => 'other',
            'createdBy_id' => $user_id,
            'mme_id' => $mme_id,
            'lifesheet_created' => true,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_internalReference' => 'You don\'t have the user right to update a MME ID signed'
        ]);
    }

    /**
     * Test Conception Number: 45
     * Try to add a usage as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to save an usage as validated"
     * @return void
     */
    public function test_add_usage_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $response = $this->post('/usage/verif', [
            'usg_validate' => 'validated',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'usg_type' => 'You don\'t have the user right to save an usage as validated'
        ]);
    }

    /**
     * Test Conception Number: 46
     * Try to update a usage saved as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update an usage save as drafted or in to be validated"
     * @return void
     */
    public function test_update_usage_as_draft_or_to_be_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/usage/verif', [
            'usg_validate' => 'drafted',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/usg', [
            'usg_validate' => 'drafted',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/usage/verif', [
            'reason' => 'update',
            'usg_validate' => 'drafted',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => $user_id,
            'usg_id' => Usage::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'usg_type' => 'You don\'t have the user right to update an usage save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 47
     * Try to update a usage saved as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update an usage save as validated"
     * @return void
     */
    public function test_update_usage_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/usage/verif', [
            'usg_validate' => 'validated',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/usg', [
            'usg_validate' => 'validated',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/usage/verif', [
            'reason' => 'update',
            'usg_validate' => 'drafted',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => $user_id,
            'usg_id' => Usage::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'usg_type' => 'You don\'t have the user right to update an usage save as validated'
        ]);
    }

    /**
     * Test Conception Number: 48
     * Try to update a usage as signed without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update an usage signed"
     * @return void
     */
    public function test_update_usage_as_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_updateDataValidatedButNotSignedRight' => 1,
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/usage/verif', [
            'usg_validate' => 'validated',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/usg', [
            'usg_validate' => 'validated',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/usage/verif', [
            'reason' => 'update',
            'usg_validate' => 'drafted',
            'usg_type' => 'three',
            'usg_precaution' => 'three',
            'user_id' => $user_id,
            'usg_id' => Usage::all()->last()->id,
            'lifesheet_created' => true,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'usg_type' => 'You don\'t have the user right to update an usage signed'
        ]);
    }

    /**
     * Test Conception Number: 49
     * Try to add a special process without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to save a special process as validated"
     * @return void
     */
    public function test_add_special_process_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $response = $this->post('/spProc/verif', [
            'spProc_validate' => 'validated',
            'sp_type' => 'three',
            'sp_precaution' => 'three',
            'user_id' => $user_id,
            'spProc_exist' => false,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'spProc_remarksOrPrecaution' => 'You don\'t have the user right to save a special process as validated'
        ]);
    }

    /**
     * Test Conception Number: 50
     * Try to update a special process saved as drafted or to_be_validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a special process save as drafted or in to be validated"
     * @return void
     */
    public function test_update_special_process_as_drafted_or_to_be_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/spProc/verif', [
            'spProc_validate' => 'drafted',
            'sp_type' => 'three',
            'sp_precaution' => 'three',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'spProc_exist' => false,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/spProc', [
            'spProc_validate' => 'drafted',
            'sp_type' => 'three',
            'sp_precaution' => 'three',
            'eq_id' => $eq_id,
            'spProc_exist' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/spProc/verif', [
            'reason' => 'update',
            'spProc_validate' => 'drafted',
            'sp_type' => 'other',
            'sp_precaution' => 'other',
            'user_id' => $user_id,
            'spProc_id' => SpecialProcess::all()->last()->id,
            'spProc_exist' => false,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'spProc_remarksOrPrecaution' => 'You don\'t have the user right to update a special process save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 51
     * Try to update a special process saved as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a special process save as validated"
     * @return void
     */
    public function test_update_special_process_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/spProc/verif', [
            'spProc_validate' => 'validated',
            'sp_type' => 'three',
            'sp_precaution' => 'three',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'spProc_exist' => false,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/spProc', [
            'spProc_validate' => 'validated',
            'sp_type' => 'three',
            'sp_precaution' => 'three',
            'eq_id' => $eq_id,
            'spProc_exist' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/spProc/verif', [
            'reason' => 'update',
            'spProc_validate' => 'drafted',
            'sp_type' => 'three',
            'sp_precaution' => 'three',
            'user_id' => $user_id,
            'spProc_id' => SpecialProcess::all()->last()->id,
            'spProc_exist' => false,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'spProc_remarksOrPrecaution' => 'You don\'t have the user right to update a special process save as validated'
        ]);
    }

    /**
     * Test Conception Number: 52
     * Try to update a special process signed without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a special process signed"
     * @return void
     */
    public function test_update_special_process_as_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_updateDataValidatedButNotSignedRight' => 1,
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/spProc/verif', [
            'spProc_validate' => 'validated',
            'sp_type' => 'three',
            'sp_precaution' => 'three',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'spProc_exist' => false,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/spProc', [
            'spProc_validate' => 'validated',
            'sp_type' => 'three',
            'sp_precaution' => 'three',
            'eq_id' => $eq_id,
            'spProc_exist' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/spProc/verif', [
            'reason' => 'update',
            'spProc_validate' => 'drafted',
            'sp_type' => 'three',
            'sp_precaution' => 'three',
            'user_id' => $user_id,
            'spProc_id' => SpecialProcess::all()->last()->id,
            'lifesheet_created' => true,
            'spProc_exist' => false,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'spProc_remarksOrPrecaution' => 'You don\'t have the user right to update a special process signed'
        ]);
    }

    /**
     * Test Conception Number: 53
     * Try to technically validate equipment without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to realize a technical validation"
     * @return void
     */
    public function test_technically_validate_equipment()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/equipment/verifValidation/' . $eq_id, [
            'user_id' => $user_id,
            'reason' => 'technical',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'validation' => 'You don\'t have the right to realize a technical validation'
        ]);
    }

    /**
     * Test Conception Number: 54
     * Try to quality validate equipment without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to realize a quality validation"
     * @return void
     */
    public function test_quality_validate_equipment()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/equipment/verifValidation/' . $eq_id, [
            'user_id' => $user_id,
            'reason' => 'quality',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'validation' => 'You don\'t have the right to realize a quality validation'
        ]);
    }

    /**
     * Test Conception Number: 55
     * Try to add a state as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to save a state as validated"
     * @return void
     */
    public function test_add_state_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $response = $this->post('/state/verif', [
            'state_validate' => 'validated',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You don\'t have the user right to save a state as validated'
        ]);
    }

    /**
     * Test Conception Number: 56
     * Try to add a state as drafted without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to declare a new state"
     * @return void
     */
    public function test_add_state_as_drafted()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $response = $this->post('/state/verif', [
            'reason' => 'add',
            'state_validate' => 'drafted',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You don\'t have the user right to declare a new state'
        ]);
    }

    /**
     * Test Conception Number: 57
     * Try to update a state without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a state save as drafted or in to be validated"
     * @return void
     */
    public function test_update_state_as_drafted()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/state/verif', [
            'state_validate' => 'drafted',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'state_remarks' => 'test',
            'state_name' => 'Waiting_for_installation',
            'state_startDate' => Carbon::now()->format('Y-m-d'),
            'eq_id' => $eq_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'reason' => 'update',
            'state_validate' => 'drafted',
            'eq_id' => $eq_id,
            'state_remarks' => 'test',
            'state_name' => 'Waiting_for_installation',
            'state_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);
        $response = $this->post('/state/verif', [
            'reason' => 'update',
            'state_validate' => 'drafted',
            'user_id' => $user_id,
            'state_id' => State::all()->last()->id,
            'state_remarks' => 'test',
            'state_name' => 'Waiting_for_installation',
            'state_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You don\'t have the user right to update a state save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 58
     * Try to add preventive maintenance as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to validate a preventive maintenance operation realized"
     * @return void
     */
    public function test_add_preventive_maintenance_realized_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'drafted',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'validated',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_validate' => 'You don\'t have the right to validate a preventive maintenance operation realized'
        ]);
    }

    /**
     * Test Conception Number: 59
     * Try to update preventive maintenance without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to update a preventive maintenance operation realized"
     * @return void
     */
    public function test_update_preventive_maintenance_realized()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'drafted',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'prvMtnOpRlz_reportNumber' => 'three',
            'prvMtnOpRlz_comment' => 'three',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/prvMtnOpRlz', [
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'prvMtnOpRlz_reportNumber' => 'three',
            'prvMtnOpRlz_comment' => 'three',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'reason' => 'update',
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => $user_id,
            'prvMtnOpRlz_reportNumber' => 'other',
            'prvMtnOpRlz_comment' => 'other',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_validate' => 'You don\'t have the right to update a preventive maintenance operation realized'
        ]);
    }

    /**
     * Test Conception Number: 60
     * Try to update preventive maintenance as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a preventive maintenance operation realized save as drafted or in to be validated"
     * @return void
     */
    public function test_update_preventive_maintenance_realized_draft_tbv()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_makeEqOpValidationRight' => 1
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'drafted',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'prvMtnOpRlz_reportNumber' => 'three',
            'prvMtnOpRlz_comment' => 'three',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/prvMtnOpRlz', [
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'prvMtnOpRlz_reportNumber' => 'three',
            'prvMtnOpRlz_comment' => 'three',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'reason' => 'update',
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => $user_id,
            'prvMtnOpRlz_reportNumber' => 'other',
            'prvMtnOpRlz_comment' => 'other',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
            'prvMtnOpRlz_id' => PreventiveMaintenanceOperationRealized::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_validate' => 'You don\'t have the user right to update a preventive maintenance operation realized save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 61
     * Try to add preventive maintenance as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to realize a preventive maintenance operation realized"
     * @return void
     */
    public function test_add_preventive_maintenance_realized_validated_no_realization()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_validateOtherDataRight' => 1
        ]);

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'drafted',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'validated',
            'user_id' => $user_id,
            'prvMtnOpRlz_endDate' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'realizedBy_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'connexion' => 'You don\'t have the right to realize a preventive maintenance operation realized'
        ]);
    }

    /**
     * Test Conception Number: 62
     * Try to delete preventive maintenance without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to delete a preventive maintenance operation realized"
     * @return void
     */
    public function test_delete_preventive_maintenance_realized()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'drafted',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'prvMtnOpRlz_reportNumber' => 'three',
            'prvMtnOpRlz_comment' => 'three',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/prvMtnOpRlz', [
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'prvMtnOpRlz_reportNumber' => 'three',
            'prvMtnOpRlz_comment' => 'three',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);

        $response = $this->post('/state/delete/prvMtnOpRlz/' . PreventiveMaintenanceOperationRealized::all()->last()->id, [
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prvMtnOpRlz_delete' => 'You don\'t have the right to delete a preventive maintenance operation realized'
        ]);
    }

    /**
     * Test Conception Number: 63
     * Try to approve preventive maintenance without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to approve a preventive maintenance operation realized"
     * @return void
     */
    public function test_approve_preventive_maintenance_realized()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'drafted',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'prvMtnOpRlz_reportNumber' => 'three',
            'prvMtnOpRlz_comment' => 'three',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/prvMtnOpRlz', [
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'prvMtnOpRlz_reportNumber' => 'three',
            'prvMtnOpRlz_comment' => 'three',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOpRlz/approve/' . PreventiveMaintenanceOperationRealized::all()->last()->id, [
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'connexion' => 'You don\'t have the right to approve a preventive maintenance operation realized'
        ]);
    }

    /**
     * Test Conception Number: 64
     * Try to realize preventive maintenance without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to realize a preventive maintenance operation realized"
     * @return void
     */
    public function test_realize_preventive_maintenance_realized()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'drafted',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'drafted',
            'eq_id' => $eq_id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOpRlz/verif', [
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'prvMtnOpRlz_reportNumber' => 'three',
            'prvMtnOpRlz_comment' => 'three',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/prvMtnOpRlz', [
            'state_id' => State::all()->last()->id,
            'prvMtnOp_id' => PreventiveMaintenanceOperation::all()->last()->id,
            'prvMtnOpRlz_validate' => 'drafted',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'prvMtnOpRlz_reportNumber' => 'three',
            'prvMtnOpRlz_comment' => 'three',
            'prvMtnOpRlz_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);

        $response = $this->post('/prvMtnOpRlz/realize/' . PreventiveMaintenanceOperationRealized::all()->last()->id, [
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'connexion' => 'You don\'t have the right to realize a preventive maintenance operation realized'
        ]);
    }

    /**
     * Test Conception Number: 65
     * Try to add curative maintenance as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to validate a curative maintenance operation"
     * @return void
     */
    public function test_add_curative_maintenance_operation_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');
        $mme_id = $this->add_mme('validated');

        $response = $this->post('/curMtnOp/verif', [
            'curMtnOp_validate' => 'validated',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_description' => 'three',
            'user_id' => $user_id,
            'state_id' => State::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You don\'t have the right to validate a curative maintenance operation'
        ]);
        $response = $this->post('/mme/curMtnOp/verif', [
            'curMtnOp_validate' => 'validated',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_description' => 'three',
            'user_id' => $user_id,
            'state_id' => MmeState::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You don\'t have the right to validate a curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 66
     * Try to update curative maintenance saved as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a curative maintenance operation save as drafted or in to be validated"
     * @return void
     */
    public function test_update_curative_maintenance_operation_draft_tbv()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_makeMmeOpValidationRight' => 1,
        ]);

        $eq_id = $this->add_eq('validated');
        $mme_id = $this->add_mme('validated');

        $response = $this->post('/equipment/add/state/curMtnOp', [
            'curMtnOp_validate' => 'drafted',
            'eq_id' => $eq_id,
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_description' => 'three',
            'state_id' => State::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->format('Y-m-d'),
            'curMtnOp_endDate' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'realizedBy_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/curMtnOp/verif', [
            'reason' => 'update',
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_description' => 'three',
            'user_id' => $user_id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->where('curMtnOp_reportNumber', '=', 'three')->first()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You don\'t have the user right to update a curative maintenance operation save as drafted or in to be validated'
        ]);

        $response = $this->post('/mme/add/state/curMtnOp', [
            'curMtnOp_validate' => 'drafted',
            'mme_id' => $mme_id,
            'curMtnOp_reportNumber' => 'threeMme',
            'curMtnOp_description' => 'threeMme',
            'state_id' => MmeState::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->format('Y-m-d'),
            'curMtnOp_endDate' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'realizedBy_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/curMtnOp/verif', [
            'reason' => 'update',
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_reportNumber' => 'threeMme',
            'curMtnOp_description' => 'threeMme',
            'user_id' => $user_id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->where('curMtnOp_reportNumber', '=', 'threeMme')->first()->id,
            'state_id' => MmeState::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You don\'t have the user right to update a curative maintenance operation save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 67
     * Try to update curative maintenance saved as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to update a curative maintenance operation"
     * @return void
     */
    public function test_update_curative_maintenance_operation_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');
        $mme_id = $this->add_mme('validated');

        $response = $this->post('/equipment/add/state/curMtnOp', [
            'curMtnOp_validate' => 'validated',
            'eq_id' => $eq_id,
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_description' => 'three',
            'state_id' => State::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->format('Y-m-d'),
            'curMtnOp_endDate' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'realizedBy_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/curMtnOp/verif', [
            'reason' => 'update',
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_description' => 'three',
            'user_id' => $user_id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->where('curMtnOp_reportNumber', '=', 'three')->first()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You don\'t have the right to update a curative maintenance operation'
        ]);

        $response = $this->post('/mme/add/state/curMtnOp', [
            'curMtnOp_validate' => 'validated',
            'mme_id' => $mme_id,
            'curMtnOp_reportNumber' => 'threeMme',
            'curMtnOp_description' => 'threeMme',
            'state_id' => MmeState::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->format('Y-m-d'),
            'curMtnOp_endDate' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'realizedBy_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/curMtnOp/verif', [
            'reason' => 'update',
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_reportNumber' => 'threeMme',
            'curMtnOp_description' => 'threeMme',
            'user_id' => $user_id,
            'curMtnOp_id' => CurativeMaintenanceOperation::all()->where('curMtnOp_reportNumber', '=', 'threeMme')->first()->id,
            'state_id' => MmeState::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You don\'t have the right to update a curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 68
     * Try to delete curative maintenance of equipment without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to delete a curative maintenance operation"
     * @return void
     */
    public function test_delete_curative_maintenance_operation_of_equipment()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/equipment/add/state/curMtnOp', [
            'curMtnOp_validate' => 'validated',
            'eq_id' => $eq_id,
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_description' => 'three',
            'state_id' => State::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->format('Y-m-d'),
            'curMtnOp_endDate' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'realizedBy_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/state/delete/curMtnOp/' . CurativeMaintenanceOperation::all()->last()->id, [
            'reason' => 'equipment',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_delete' => 'You don\'t have the right to delete a curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 69
     * Try to delete curative maintenance of mme without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to delete a curative maintenance operation"
     * @return void
     */
    public function test_delete_curative_maintenance_operation_of_mme()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/mme/add/state/curMtnOp', [
            'curMtnOp_validate' => 'validated',
            'mme_id' => $mme_id,
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_description' => 'three',
            'state_id' => MmeState::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->format('Y-m-d'),
            'curMtnOp_endDate' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'realizedBy_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/state/delete/curMtnOp/' . CurativeMaintenanceOperation::all()->last()->id, [
            'reason' => 'mme',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_delete' => 'You don\'t have the right to delete a curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 70
     * Try to realize curative maintenance of equipment without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to realize a curative maintenance operation"
     * @return void
     */
    public function test_realize_curative_maintenance_operation_of_equipment()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/equipment/add/state/curMtnOp', [
            'curMtnOp_validate' => 'validated',
            'eq_id' => $eq_id,
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_description' => 'three',
            'state_id' => State::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->format('Y-m-d'),
            'curMtnOp_endDate' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'realizedBy_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/curMtnOp/realize/' . CurativeMaintenanceOperation::all()->last()->id, [
            'reason' => 'equipment',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'connexion' => 'You don\'t have the right to realize a curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 71
     * Try to realize curative maintenance of mme without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to realize a curative maintenance operation"
     * @return void
     */
    public function test_realize_curative_maintenance_operation_of_mme()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/mme/add/state/curMtnOp', [
            'curMtnOp_validate' => 'validated',
            'mme_id' => $mme_id,
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_description' => 'three',
            'state_id' => MmeState::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->format('Y-m-d'),
            'curMtnOp_endDate' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'realizedBy_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/curMtnOp/realize/' . CurativeMaintenanceOperation::all()->last()->id, [
            'reason' => 'mme',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'connexion' => 'You don\'t have the right to realize a curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 72
     * Try to technicaly validate curative maintenance without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to realize technical validation on a curative maintenance operation"
     * @return void
     */
    public function test_technicaly_validate_curative_maintenance_operation()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/equipment/add/state/curMtnOp', [
            'curMtnOp_validate' => 'validated',
            'eq_id' => $eq_id,
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_description' => 'three',
            'state_id' => State::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->format('Y-m-d'),
            'curMtnOp_endDate' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'realizedBy_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('curMtnOp/technicalVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'connexion' => 'You don\'t have the right to realize technical validation on a curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 73
     * Try to quality validate curative maintenance without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to realize quality validation on a curative maintenance operation"
     * @return void
     */
    public function test_quality_validate_curative_maintenance_operation()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq('validated');

        $response = $this->post('/equipment/add/state/curMtnOp', [
            'curMtnOp_validate' => 'validated',
            'eq_id' => $eq_id,
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_description' => 'three',
            'state_id' => State::all()->last()->id,
            'curMtnOp_startDate' => Carbon::now()->format('Y-m-d'),
            'curMtnOp_endDate' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'realizedBy_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('curMtnOp/qualityVerifier/' . CurativeMaintenanceOperation::all()->last()->id, [
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'connexion' => 'You don\'t have the right to realize quality validation on a curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 74
     * Try to add a MME precaution as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to save a precaution as validated"
     * @return void
     */
    public function test_add_mme_precaution_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/precaution/verif', [
            'user_id' => $user_id,
            'prctn_validate' => 'validated',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'prctn_type' => 'You don\'t have the user right to save a precaution as validated'
        ]);
    }

    /**
     * Test Conception Number: 75
     * Try to add a MME verification as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to save a verification as validated"
     * @return void
     */
    public function test_add_mme_verification_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/verif/verif', [
            'user_id' => $user_id,
            'verif_validate' => 'validated',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_preventiveOperation' => 'You don\'t have the user right to save a verification as validated'
        ]);
    }

    /**
     * Test Conception Number: 76
     * Try to update a MME verification saved as drafted or to be validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a verification save as drafted or in to be validated"
     * @return void
     */
    public function test_update_mme_verification_as_drafted_or_to_be_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/verif/verif', [
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_puttingIntoService' => true,
            'verif_preventiveOperation' => false,
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/verif', [
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_puttingIntoService' => true,
            'verif_preventiveOperation' => false,
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);


        $response = $this->post('/verif/verif', [
            'reason' => 'update',
            'user_id' => $user_id,
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_puttingIntoService' => true,
            'verif_preventiveOperation' => false,
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'mme_id' => $mme_id,
            'verif_id' => Verification::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_preventiveOperation' => 'You don\'t have the user right to update a verification save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 77
     * Try to update a MME precaution saved as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a verification save as validated"
     * @return void
     */
    public function test_update_mme_precaution_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/mme/add/verif', [
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_puttingIntoService' => true,
            'verif_preventiveOperation' => false,
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);


        $response = $this->post('/verif/verif', [
            'reason' => 'update',
            'user_id' => $user_id,
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_puttingIntoService' => true,
            'verif_preventiveOperation' => false,
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'mme_id' => $mme_id,
            'verif_id' => Verification::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_preventiveOperation' => 'You don\'t have the user right to update a verification save as validated'
        ]);
    }

    /**
     * Test Conception Number: 78
     * Try to update a signed MME verification without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a verification signed"
     * @return void
     */
    public function test_update_mme_verification_as_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_updateDataValidatedButNotSignedRight' => 1
        ]);

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/mme/add/verif', [
            'user_id' => $user_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_puttingIntoService' => true,
            'verif_preventiveOperation' => false,
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/'. $mme_id, [
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'mme_validate' => 'technical',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/validation/'. $mme_id, [
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'mme_validate' => 'quality',
        ]);
        $response->assertStatus(200);


        $response = $this->post('/verif/verif', [
            'reason' => 'update',
            'user_id' => $user_id,
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_puttingIntoService' => true,
            'verif_preventiveOperation' => false,
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'mme_id' => $mme_id,
            'verif_id' => Verification::all()->last()->id,
            'lifesheet_created' => true,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_preventiveOperation' => 'You don\'t have the user right to update a verification signed'
        ]);
    }

    /**
     * Test Conception Number: 79
     * Try to delete a MME verification saved as drafted without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to delete a verification save as drafted or in to be validated"
     * @return void
     */
    public function test_delete_mme_verification_as_drafted()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/verif/verif', [
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_puttingIntoService' => true,
            'verif_preventiveOperation' => false,
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/verif', [
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_puttingIntoService' => true,
            'verif_preventiveOperation' => false,
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);


        $response = $this->post('/mme/delete/verif/'.Verification::all()->last()->id, [
            'user_id' => $user_id,
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_preventiveOperation' => 'You don\'t have the user right to delete a verification save as drafted or in to be validated'
        ]);
    }

    /**
     * Test Conception Number: 80
     * Try to delete a MME precaution saved as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to delete a verification save as validated"
     * @return void
     */
    public function test_delete_mme_precaution_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/mme/add/verif', [
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_puttingIntoService' => true,
            'verif_preventiveOperation' => false,
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);


        $response = $this->post('/mme/delete/verif/'.Verification::all()->last()->id, [
            'user_id' => $user_id,
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_preventiveOperation' => 'You don\'t have the user right to delete a verification save as validated'
        ]);
    }

    /**
     * Test Conception Number: 81
     * Try to delete a signed MME verification without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to delete a verification signed"
     * @return void
     */
    public function test_delete_mme_verification_as_signed()
    {
        $user_id = $this->make_a_user_with_no_permission();

        User::all()->where('id', '=', $user_id)->first()->update([
            'user_deleteDataValidatedLinkedToEqOrMmeRight' => 1
        ]);

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/mme/add/verif', [
            'user_id' => $user_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_puttingIntoService' => true,
            'verif_preventiveOperation' => false,
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/'. $mme_id, [
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'mme_validate' => 'technical',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/validation/'. $mme_id, [
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'mme_validate' => 'quality',
        ]);
        $response->assertStatus(200);


        $response = $this->post('/mme/delete/verif/'.Verification::all()->last()->id, [
            'user_id' => $user_id,
            'mme_id' => $mme_id,
            'lifesheet_created' => true,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_preventiveOperation' => 'You don\'t have the user right to delete a verification signed'
        ]);
    }

    /**
     * Test Conception Number: 82
     * Try to reform a MME precaution without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to reform a verification"
     * @return void
     */
    public function test_reform_mme_precaution()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/mme/add/verif', [
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_puttingIntoService' => true,
            'verif_preventiveOperation' => false,
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/reform/verif/'.Verification::all()->last()->id, [
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_reformDate' => 'You don\'t have the user right to reform a verification'
        ]);
    }

    /**
     * Test Conception Number: 83
     * Try to technicaly validate a MME without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to realize a technical validation"
     * @return void
     */
    public function test_technical_validate_mme()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/mme/verifValidation/'. $mme_id, [
            'user_id' => $user_id,
            'reason' => 'technical',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'validation' => 'You don\'t have the right to realize a technical validation'
        ]);
    }

    /**
     * Test Conception Number: 84
     * Try to quality validate a MME without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the right to realize a quality validation"
     * @return void
     */
    public function test_quality_validate_mme()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('validated');

        $response = $this->post('/mme/verifValidation/'. $mme_id, [
            'user_id' => $user_id,
            'reason' => 'quality',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'validation' => 'You don\'t have the right to realize a quality validation'
        ]);
    }

    /**
     * Test Conception Number: 85
     * Try to add a MME state as validated without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to save a state as validated"
     * @return void
     */
    public function test_add_mme_state_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $response = $this->post('/mme_state/verif', [
            'state_validate' => 'validated',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You don\'t have the user right to save a state as validated'
        ]);
    }

    /**
     * Test Conception Number: 86
     * Try to add a MME state without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to declare a new state"
     * @return void
     */
    public function test_add_mme_state()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $response = $this->post('/mme_state/verif', [
            'reason' => 'add',
            'state_validate' => 'drafted',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You don\'t have the user right to declare a new state'
        ]);
    }

    /**
     * Test Conception Number: 87
     * Try to update a MME state without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to update a state save as drafted or in to be validated"
     * @return void
     */
    public function test_update_mme_state_as_drafted()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $mme_id = $this->add_mme('drafted');

        $response = $this->post('/mme_state/verif', [
            'state_validate' => 'drafted',
            'user_id' => User::all()->where('user_pseudo', '=', 'admin')->first()->id,
            'state_remarks' => 'test',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => Carbon::now()->format('Y-m-d'),
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'reason' => 'update',
            'state_validate' => 'drafted',
            'mme_id' => $mme_id,
            'state_remarks' => 'test',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme_state/verif', [
            'reason' => 'update',
            'state_validate' => 'drafted',
            'user_id' => $user_id,
            'state_id' => MmeState::all()->last()->id,
            'state_remarks' => 'test',
            'state_name' => 'In_use',
            'state_startDate' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You don\'t have the user right to update a state save as drafted or in to be validated'
        ]);
    }


}
