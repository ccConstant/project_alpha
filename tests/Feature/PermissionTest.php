<?php

namespace Tests\Feature;

use App\Models\File;
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
use App\Models\SW01\Power;
use App\Models\SW01\PreventiveMaintenanceOperation;
use App\Models\SW01\Risk;
use App\Models\SW01\Usage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

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

        $response = $this->post('/equipment/delete/prvMtnOp/'.PreventiveMaintenanceOperation::all()->last()->id, [
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

        $response = $this->post('/equipment/delete/prvMtnOp/'.PreventiveMaintenanceOperation::all()->last()->id, [
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

        $response = $this->post('/equipment/delete/prvMtnOp/'.PreventiveMaintenanceOperation::all()->last()->id, [
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

        $response = $this->post('/equipment/reform/prvMtnOp/'.PreventiveMaintenanceOperation::all()->last()->id, [
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
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->first()->id
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
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->first()->id
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

        $response = $this->post('/equipment/delete/dim/'.Dimension::all()->last()->id, [
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

        $response = $this->post('/equipment/delete/dim/'.Dimension::all()->last()->id, [
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

        $response = $this->post('/equipment/delete/dim/'.Dimension::all()->last()->id, [
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
        $response = $this->post('/equipment/delete/pow/'.Power::all()->last()->id, [
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
        $response = $this->post('/equipment/delete/pow/'.Power::all()->last()->id, [
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
        $response = $this->post('/equipment/delete/pow/'.Power::all()->last()->id, [
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

        $response = $this->post('/equipment/delete/risk/'.Risk::all()->last()->id, [
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

        $response = $this->post('/equipment/delete/risk/'.Risk::all()->last()->id, [
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

        $response = $this->post('/equipment/delete/risk/'.Risk::all()->last()->id, [
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

        $response = $this->post('/equipment/delete/file/'.File::all()->last()->id, [
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

        $response = $this->post('/mme/delete/file/'.File::all()->last()->id, [
            'user_id' => $user_id,
            'mme_id' => $mme_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'file_name' => 'You don\'t have the user right to delete a file save as drafted or in to be validated'
        ]);
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

        $response = $this->post('/equipment/delete/file/'.File::all()->last()->id, [
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

        $response = $this->post('/mme/delete/file/'.File::all()->last()->id, [
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

        $response = $this->post('/equipment/delete/file/'.File::all()->last()->id, [
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

        $response = $this->post('/mme/delete/file/'.File::all()->last()->id, [
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
     * Try to add a usage as signed without the permission
     * Expected result: Receiving an error:
     *                                        "You don't have the user right to save an usage as signed"
     * @return void
     */
    public function test_add_usage_as_signed()
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
            'usg_type' => 'You don\'t have the user right to save an usage as signed'
        ]);
    }
}
