<?php

namespace Tests\Feature;

use App\Models\SW01\Dimension;
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

class EnumDimensionNameTest extends TestCase
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

    /**
     * Test Conception Number: 1
     * Try to add a non-existent name in the database
     * Name: Name
     * Expected result: The name is correctly added to the database
     * @return void
     */
    public function test_add_non_existent_name()
    {
        $oldCOunt = EnumDimensionName::all()->count();
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Name'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumDimensionName::all()->count(), $oldCOunt + 1);
    }

    /**
     * Test Conception Number: 2
     * Try to add two time the same name in the database
     * Name : Exist
     * Expected result: Receiving an error:
     *                                      "The value of the field for the new dimension name already exist in the data base"
     * @return void
     */
    public function test_add_two_time_same_name()
    {
        $oldCOunt = EnumDimensionName::all()->count();
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumDimensionName::all()->count(), $oldCOunt + 1);
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_dim_name' => [
                "The value of the field for the new dimension name already exist in the data base"
            ]
        ]);
        $this->assertEquals(EnumDimensionName::all()->count(), $oldCOunt + 1);
    }

    /**
     * Test Conception Number: 3
     * Analyze the enum 'Test' and expecting the correct data
     * Name: Name
     * Expected result: The data contain one validated equipment and one not validated
     * @returns void
     */
    public function test_analyze_data()
    {
        $this->requiredForTest();

        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'eq_name' => 'Test',
            'eq_serialNumber' => 'Test',
            'eq_constructor' => 'Test',
            'eq_set' => 'Test',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'Test',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'eq_name' => 'Test',
            'eq_serialNumber' => 'Test',
            'eq_constructor' => 'Test',
            'eq_set' => 'Test',
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_validate' => 'drafted',
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_mass' => 12,
            'eqTemp_remarks' => 'Test',
            'eqTemp_mobility' => true,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'Balance')->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'g')->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'TestAnalyze',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'Testvalidated',
            'eq_externalReference' => 'Testvalidated',
            'eq_name' => 'Testvalidated',
            'eq_serialNumber' => 'Testvalidated',
            'eq_constructor' => 'Testvalidated',
            'eq_set' => 'Testvalidated',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'Testvalidated',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 2, Equipment::all()->count());
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'Testvalidated',
            'eq_externalReference' => 'Testvalidated',
            'eq_name' => 'Testvalidated',
            'eq_serialNumber' => 'Testvalidated',
            'eq_constructor' => 'Testvalidated',
            'eq_set' => 'Testvalidated',
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_validate' => 'validated',
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_mass' => 12,
            'eqTemp_remarks' => 'Testvalidated',
            'eqTemp_mobility' => true,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'Balance')->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'g')->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
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
        $response = $this->post('dimension/enum/name/analyze/' . EnumDimensionName::all()->where('value', '=', 'Name')->first()->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'equipments',
            'validated_eq',
        ]);
        $response->assertJson([
            'id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'equipments' => [
                '0' => [
                    "eqTemp_id" => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->last()->id)->first()->id,
                    "name" => "Test",
                    "internalReference" => "Test"
                ],
            ],
            'validated_eq' => [
                '0' => [
                    "eqTemp_id" => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Testvalidated')->first()->id)->first()->id,
                    "name" => "Testvalidated",
                    "internalReference" => "Testvalidated"
                ]
            ],
        ]);
    }

    public function requiredForTest()
    {
        $user_id = $this->create_user('test');
        // Add the different enum of the name if they didn't already exist in the database
        if (EnumDimensionName::all()->where('value', '=', 'Name')->count() === 0) {
            $countDimName = EnumDimensionName::all()->count();
            $response = $this->post('/dimension/enum/name/add', [
                'value' => 'Name',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimName + 1, EnumDimensionName::all());
        }
        if (EnumDimensionName::all()->where('value', '=', 'Exist')->count() === 0) {
            $countDimName = EnumDimensionName::all()->count();
            $response = $this->post('/dimension/enum/name/add', [
                'value' => 'Exist',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimName + 1, EnumDimensionName::all());
        }
        // Add the prerequisite enum for the equipment creation if they didn't already exist in the database
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
        if (EnumDimensionType::all()->where('value', '=', 'External')->count() === 0) {
            $countDimType = EnumDimensionType::all()->count();
            $response = $this->post('/dimension/enum/type/add', [
                'value' => 'External',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimType + 1, EnumDimensionType::all());
        }
        if (EnumDimensionUnit::all()->where('value', '=', 'mm')->count() === 0) {
            $countDimUnit = EnumDimensionUnit::all()->count();
            $response = $this->post('/dimension/enum/unit/add', [
                'value' => 'mm',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimUnit + 1, EnumDimensionUnit::all());
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
    }

    /**
     * Test Conception Number: 4
     * Try to update an enum linked to drafted equipment with a non-existent name in the database
     * Name: TestDrafted
     * Expected result: The name is correctly updated in the database
     * @returns void
     */
    public function test_update_enum_linked_to_drafted_with_non_existent_name()
    {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'TestUpdateEnum1',
            'eq_externalReference' => 'TestUpdateEnum1',
            'eq_name' => 'TestUpdateEnum1',
            'eq_serialNumber' => 'TestUpdateEnum1',
            'eq_constructor' => 'TestUpdateEnum1',
            'eq_set' => 'TestUpdateEnum1',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestUpdateEnum1',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'TestUpdateEnum1',
            'eq_externalReference' => 'TestUpdateEnum1',
            'eq_name' => 'TestUpdateEnum1',
            'eq_serialNumber' => 'TestUpdateEnum1',
            'eq_constructor' => 'TestUpdateEnum1',
            'eq_set' => 'TestUpdateEnum1',
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_validate' => 'drafted',
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_mass' => 12,
            'eqTemp_remarks' => 'TestUpdateEnum1',
            'eqTemp_mobility' => true,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'Balance')->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'g')->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $oldId = EnumDimensionName::all()->where('value', '=', 'Name')->first()->id;
        $response = $this->post('dimension/enum/name/verif/' . EnumDimensionName::all()->where('value', '=', 'Name')->first()->id, [
            'value' => 'TestDrafted'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/dimension/enum/name/update/' . EnumDimensionName::all()->where('value', '=', 'Name')->first()->id, [
            'value' => 'TestDrafted',
            'validated_eq' => []
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $newId = EnumDimensionName::all()->where('value', '=', 'TestDrafted')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'TestDrafted',
        ]);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'TestDrafted')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
    }

    /**
     * Test Conception Number: 5
     * Try to update an enum linked to to_be_validated equipment with a non-existent name in the database
     * Name: TestToBeValidated
     * Expected result: The name is correctly updated in the database
     * @returns void
     */
    public function test_update_enum_linked_to_toBeValidated_with_non_existent_name()
    {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'TestUpdateEnum2',
            'eq_externalReference' => 'TestUpdateEnum2',
            'eq_name' => 'TestUpdateEnum2',
            'eq_serialNumber' => 'TestUpdateEnum2',
            'eq_constructor' => 'TestUpdateEnum2',
            'eq_set' => 'TestUpdateEnum2',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestUpdateEnum2',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'TestUpdateEnum2',
            'eq_externalReference' => 'TestUpdateEnum2',
            'eq_name' => 'TestUpdateEnum2',
            'eq_serialNumber' => 'TestUpdateEnum2',
            'eq_constructor' => 'TestUpdateEnum2',
            'eq_set' => 'TestUpdateEnum2',
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_validate' => 'to_be_validated',
            'eqTemp_mass' => 12,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'g')->first()->id,
            'eqTemp_remarks' => 'TestUpdateEnum2',
            'eqTemp_mobility' => 1,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'Balance')->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $response = $this->post('dimension/enum/name/verif/' . EnumDimensionName::all()->where('value', '=', 'Name')->first()->id, [
            'value' => 'TestToBeValidated'
        ]);
        $response->assertStatus(200);
        $oldId = EnumDimensionName::all()->where('value', '=', 'Name')->first()->id;
        $response = $this->post('/dimension/enum/name/update/' . EnumDimensionName::all()->where('value', '=', 'Name')->first()->id, [
            'value' => 'TestToBeValidated',
            'validated_eq' => []
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $newId = EnumDimensionName::all()->where('value', '=', 'TestToBeValidated')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'TestToBeValidated',
        ]);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'TestToBeValidated')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
    }

    /**
     * Test Conception Number: 6
     * Try to update an enum linked to validated equipment with a non-existent name in the database
     * Name: TestValidated
     * Expected result: The name is correctly updated in the database, and a history is created in the database
     * @returns void
     */
    public function test_update_enum_linked_to_validated_with_non_existent_name()
    {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'TestUpdateEnum3',
            'eq_externalReference' => 'TestUpdateEnum3',
            'eq_name' => 'TestUpdateEnum3',
            'eq_serialNumber' => 'TestUpdateEnum3',
            'eq_constructor' => 'TestUpdateEnum3',
            'eq_set' => 'TestUpdateEnum3',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestUpdateEnum3',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'TestUpdateEnum3',
            'eq_externalReference' => 'TestUpdateEnum3',
            'eq_name' => 'TestUpdateEnum3',
            'eq_serialNumber' => 'TestUpdateEnum3',
            'eq_constructor' => 'TestUpdateEnum3',
            'eq_set' => 'TestUpdateEnum3',
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_validate' => 'validated',
            'eqTemp_mass' => 12,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'g')->first()->id,
            'eqTemp_remarks' => 'TestUpdateEnum3',
            'eqTemp_mobility' => 1,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'Balance')->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
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
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_validate' => 'validated',
            'eqTemp_mass' => 12,
            'eqTemp_remarks' => 'TestUpdateEnum3',
            'eqTemp_mobility' => true,
            'eqTemp_version' => 1,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id,
        ]);
        $oldId = EnumDimensionName::all()->where('value', '=', 'Name')->first()->id;
        $response = $this->post('dimension/enum/name/analyze/' . EnumDimensionName::all()->where('value', '=', 'Name')->first()->id);
        $response->assertStatus(200);
        $tab = array();
        foreach (json_decode($response->getContent())->validated_eq as $eq) {
            array_push($tab, array(
                'eqTemp_id' => $eq->eqTemp_id,
                'name' => $eq->name,
                'internalReference' => $eq->internalReference,
            ));
        }
        $response = $this->post('dimension/enum/name/verif/' . EnumDimensionName::all()->where('value', '=', 'Name')->first()->id, [
            'value' => 'TestValidated'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/dimension/enum/name/update/' . EnumDimensionName::all()->where('value', '=', 'Name')->first()->id, [
            'value' => 'TestValidated',
            'validated_eq' => $tab,
            'history_reasonUpdate' => 'TestUpdateEnum3',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $newId = EnumDimensionName::all()->where('value', '=', 'TestValidated')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'TestValidated',
        ]);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'TestValidated')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_validate' => 'validated',
            'eqTemp_mass' => 12,
            'eqTemp_remarks' => 'TestUpdateEnum3',
            'eqTemp_mobility' => true,
            'eqTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 7
     * Try to update an enum linked to equipment with an existent name in the database
     * Name: /
     * Expected result: Receiving an error:
     *                                      "The value of the field for the dimension name already exist in the data base"
     * @returns void
     */
    public function test_update_enum_with_existant_value()
    {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'TestUpdateEnum4',
            'eq_externalReference' => 'TestUpdateEnum4',
            'eq_name' => 'TestUpdateEnum4',
            'eq_serialNumber' => 'TestUpdateEnum4',
            'eq_constructor' => 'TestUpdateEnum4',
            'eq_set' => 'TestUpdateEnum4',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestUpdateEnum4',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'TestUpdateEnum4',
            'eq_externalReference' => 'TestUpdateEnum4',
            'eq_name' => 'TestUpdateEnum4',
            'eq_serialNumber' => 'TestUpdateEnum4',
            'eq_constructor' => 'TestUpdateEnum4',
            'eq_set' => 'TestUpdateEnum4',
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_validate' => 'drafted',
            'eqTemp_mass' => 12,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'g')->first()->id,
            'eqTemp_remarks' => 'TestUpdateEnum4',
            'eqTemp_mobility' => 1,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'Balance')->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $response = $this->post('dimension/enum/name/verif/' . EnumDimensionName::all()->where('value', '=', 'Name')->first()->id, [
            'value' => 'Exist'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_dim_name' => 'The value of the field for the dimension name already exist in the data base'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Try to delete an enum not linked to an equipment
     * Name: /
     * Expected result: The name is correctly deleted in the database
     * @returns void
     */
    public function test_delete_enum_not_linked()
    {
        $this->requiredForTest();
        $countEnumDimName = EnumDimensionName::all()->count();
        $response = $this->post('/dimension/enum/name/delete/' . EnumDimensionName::all()->where('value', '=', 'Exist')->first()->id);
        $response->assertStatus(200);
        $this->assertCount($countEnumDimName - 1, EnumDimensionName::all());
    }

    /**
     * Test Conception Number: 9
     * Try to delete an enum linked to an equipment
     * Name: TestValidated
     * Expected result: Receiving an error:
     *                                      "This value is already used in the data base so you can't delete it"
     * @returns void
     */
    public function test_delete_enum_linked()
    {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'TestUpdateEnum3',
            'eq_externalReference' => 'TestUpdateEnum3',
            'eq_name' => 'TestUpdateEnum3',
            'eq_serialNumber' => 'TestUpdateEnum3',
            'eq_constructor' => 'TestUpdateEnum3',
            'eq_set' => 'TestUpdateEnum3',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestUpdateEnum3',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'TestUpdateEnum3',
            'eq_externalReference' => 'TestUpdateEnum3',
            'eq_name' => 'TestUpdateEnum3',
            'eq_serialNumber' => 'TestUpdateEnum3',
            'eq_constructor' => 'TestUpdateEnum3',
            'eq_set' => 'TestUpdateEnum3',
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_validate' => 'validated',
            'eqTemp_mass' => 12,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'g')->first()->id,
            'eqTemp_remarks' => 'TestUpdateEnum3',
            'eqTemp_mobility' => 1,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'Balance')->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
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
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_validate' => 'validated',
            'eqTemp_mass' => 12,
            'eqTemp_remarks' => 'TestUpdateEnum3',
            'eqTemp_mobility' => true,
            'eqTemp_version' => 1,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id,
        ]);
        $response = $this->post('/dimension/enum/name/delete/' . EnumDimensionName::all()->where('value', '=', 'Name')->first()->id);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_dim_name' => 'This value is already used in the data base so you can\'t delete it'
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Name',
        ]);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
    }

    /**
     * Test Conception Number: 10
     * Try to consult the enum list
     * Name: TestValidated
     * Expected result: The enum list is correct, and we receive all the data
     * @returns void
     */
    public function test_consult_enum()
    {
        $this->requiredForTest();
        $response = $this->get('/dimension/enum/name');
        $response->assertJson([
            0 => [
                'id' => EnumDimensionName::all()->where('value', '=', 'Exist')->first()->id,
                'value' => 'Exist',
                'id_enum' => 'DimensionName'
            ],
            1 => [
                'id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
                'value' => 'Name',
                'id_enum' => 'DimensionName'
            ],
        ]);
    }
}
