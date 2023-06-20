<?php

namespace Tests\Feature;

use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\EnumRiskFor;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\Risk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnumRiskForTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test Conception Number: 1
     * Try to add a non-existent risk in the database
     * Risk: Risk
     * Expected result: The risk is correctly added to the database
     * @return void
     */
    public function test_add_non_existent_risk() {
        $oldCOunt = EnumRiskFor::all()->count();
        $response = $this->post('/risk/enum/riskfor/add', [
            'value' => 'Risk'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumRiskFor::all()->count(), $oldCOunt+1);
    }

    /**
     * Test Conception Number: 2
     * Try to add two time the same risk in the database
     * Risk : Exist
     * Expected result: Receiving an error:
     *                                      "The value of the field for the new dimension risk already exist in the data base"
     * @return void
     */
    public function test_add_two_time_same_risk() {
        $oldCOunt = EnumRiskFor::all()->count();
        $response = $this->post('/risk/enum/riskfor/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumRiskFor::all()->count(), $oldCOunt+1);
        $response = $this->post('/risk/enum/riskfor/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_riskfor' => [
                "The value of the field for the new risk target already exist in the data base"
            ]
        ]);
        $this->assertEquals(EnumRiskFor::all()->count(), $oldCOunt+1);
    }

    public function requiredForTest() {
        // Add the different enum of the risk if they didn't already exist in the database
        if (EnumRiskFor::all()->where('value', '=', 'Risk')->count() === 0) {
            $countDimName=EnumRiskFor::all()->count();
            $response=$this->post('/risk/enum/riskfor/add', [
                'value' => 'Risk',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimName+1, EnumRiskFor::all());
        }
        if (EnumRiskFor::all()->where('value', '=', 'Exist')->count() === 0) {
            $countDimName=EnumRiskFor::all()->count();
            $response=$this->post('/risk/enum/riskfor/add', [
                'value' => 'Exist',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimName+1, EnumRiskFor::all());
        }
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
        }
    }

    /**
     * Test Conception Number: 3
     * Analyze the enum 'Test' and expecting the correct data
     * Risk: Risk
     * Expected result: The data contain one validated equipment and one not validated
     * @returns void
     */
    public function test_analyze_data() {
        $this->requiredForTest();

        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
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
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
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
        $countDim=Risk::all()->count();
        $response=$this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/risk', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Risk::all());
        $this->assertDatabaseHas('risks', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id,
        ]);
        $response=$this->post('/equipment/add', [
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
        $this->assertEquals($countEquipment+2, Equipment::all()->count());
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
        $countDim=Risk::all()->count();
        $response=$this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/risk', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Risk::all());
        $this->assertDatabaseHas('risks', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id,
        ]);
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/risk/enum/riskfor/analyze/' . EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'equipments',
            'validated_eq',
        ]);
        $response->assertJson([
            'id' => EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id,
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

    /**
     * Test Conception Number: 4
     * Try to update an enum linked to drafted equipment with a non-existent risk in the database
     * Risk: TestDrafted
     * Expected result: The risk is correctly updated in the database
     * @returns void
     */
    public function test_update_enum_linked_to_drafted_with_non_existent_risk() {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
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
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
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
        $countDim=Risk::all()->count();
        $response=$this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/risk', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Risk::all());
        $this->assertDatabaseHas('risks', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id,
        ]);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $oldId = EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id;
        $response = $this->post('/risk/enum/riskfor/verif/' . EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id, [
            'value' => 'TestDrafted'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/risk/enum/riskfor/update/'.EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id, [
            'value' => 'TestDrafted',
            'validated_eq' => []
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $newId = EnumRiskFor::all()->where('value', '=', 'TestDrafted')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_risk_fors', [
            'value' => 'TestDrafted',
        ]);
        $this->assertDatabaseHas('risks', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'TestDrafted')->first()->id,
        ]);
    }

    /**
     * Test Conception Number: 5
     * Try to update an enum linked to to_be_validated equipment with a non-existent risk in the database
     * Risk: TestToBeValidated
     * Expected result: The risk is correctly updated in the database
     * @returns void
     */
    public function test_update_enum_linked_to_toBeValidated_with_non_existent_risk() {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
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
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
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
        $countDim=Risk::all()->count();
        $response=$this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/risk', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Risk::all());
        $this->assertDatabaseHas('risks', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id,
        ]);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $response = $this->post('/risk/enum/riskfor/verif/' . EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id, [
            'value' => 'TestToBeValidated'
        ]);
        $response->assertStatus(200);
        $oldId = EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id;
        $response = $this->post('/risk/enum/riskfor/update/'.EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id, [
            'value' => 'TestToBeValidated',
            'validated_eq' => []
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $newId = EnumRiskFor::all()->where('value', '=', 'TestToBeValidated')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_risk_fors', [
            'value' => 'TestToBeValidated',
        ]);
        $this->assertDatabaseHas('risks', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'TestToBeValidated')->first()->id,
        ]);
    }

    /**
     * Test Conception Number: 6
     * Try to update an enum linked to validated equipment with a non-existent risk in the database
     * Risk: TestValidated
     * Expected result: The risk is correctly updated in the database, and a history is created in the database
     * @returns void
     */
    public function test_update_enum_linked_to_validated_with_non_existent_risk() {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
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
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
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
        $countDim=Risk::all()->count();
        $response=$this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/risk', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Risk::all());
        $this->assertDatabaseHas('risks', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id,
        ]);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_validate' => 'validated',
            'eqTemp_mass' => 12,
            'eqTemp_remarks' => 'TestUpdateEnum3',
            'eqTemp_mobility' => true,
            'eqTemp_version' => 1,
            'qualityVerifier_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
            'technicalVerifier_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $oldId = EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id;
        $response = $this->post('/risk/enum/riskfor/analyze/' . EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id);
        $response->assertStatus(200);
        $tab = array();
        foreach (json_decode($response->getContent())->validated_eq as $eq) {
            array_push($tab, array(
                'eqTemp_id' => $eq->eqTemp_id,
                'name' => $eq->name,
                'internalReference' => $eq->internalReference,
            ));
        }
        $response = $this->post('/risk/enum/riskfor/verif/' . EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id, [
            'value' => 'TestValidated'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/risk/enum/riskfor/update/'.EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id, [
            'value' => 'TestValidated',
            'validated_eq' => $tab,
            'history_reasonUpdate' => 'TestUpdateEnum3',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Risk::all());
        $newId = EnumRiskFor::all()->where('value', '=', 'TestValidated')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_risk_fors', [
            'value' => 'TestValidated',
        ]);
        $this->assertDatabaseHas('risks', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'TestValidated')->first()->id,
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
     * Try to update an enum linked to equipment with an existent risk in the database
     * Risk: /
     * Expected result: Receiving an error:
     *                                      "The value of the field for the dimension risk already exist in the data base"
     * @returns void
     */
    public function test_update_enum_with_existant_value() {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
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
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
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
        $countDim=Risk::all()->count();
        $response=$this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/risk', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Risk::all());
        $this->assertDatabaseHas('risks', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id,
        ]);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $response = $this->post('/risk/enum/riskfor/verif/' . EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id, [
            'value' => 'Exist'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_riskfor' => 'The value of the field for the risk target already exist in the data base'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Try to delete an enum not linked to an equipment
     * Risk: /
     * Expected result: The risk is correctly deleted in the database
     * @returns void
     */
    public function test_delete_enum_not_linked() {
        $this->requiredForTest();
        $countEnumDimName = EnumRiskFor::all()->count();
        $response = $this->post('/risk/enum/riskfor/delete/'.EnumRiskFor::all()->where('value', '=', 'Exist')->first()->id);
        $response->assertStatus(200);
        $this->assertCount($countEnumDimName-1, EnumRiskFor::all());
    }

    /**
     * Test Conception Number: 9
     * Try to delete an enum linked to an equipment
     * Risk: TestValidated
     * Expected result: Receiving an error:
     *                                      "This value is already used in the data base so you can't delete it"
     * @returns void
     */
    public function test_delete_enum_linked() {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
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
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
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
        $countDim=Risk::all()->count();
        $response=$this->post('/risk/verif', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/risk', [
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'risk_for' => 'Risk',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Risk::all());
        $this->assertDatabaseHas('risks', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id,
        ]);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_validate' => 'validated',
            'eqTemp_mass' => 12,
            'eqTemp_remarks' => 'TestUpdateEnum3',
            'eqTemp_mobility' => true,
            'eqTemp_version' => 1,
            'qualityVerifier_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
            'technicalVerifier_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response = $this->post('/risk/enum/riskfor/delete/'.EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_riskfor' => 'This value is already used in the data base so you can\'t delete it'
        ]);
        $this->assertDatabaseHas('enum_risk_fors', [
            'value' => 'Risk',
        ]);
        $this->assertDatabaseHas('risks', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'risk_validate' => 'drafted',
            'risk_remarks' => 'Test',
            'risk_wayOfControl' => 'Test',
            'enumRiskFor_id' => EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id,
        ]);
    }

    /**
     * Test Conception Number: 10
     * Try to consult the enum list
     * Risk: TestValidated
     * Expected result: The enum list is correct, and we receive all the data
     * @returns void
     */
    public function test_consult_enum() {
        $this->requiredForTest();
        $response = $this->get('/risk/enum/riskfor');
        $response->assertJson([
            0 => [
                'id' => EnumRiskFor::all()->where('value', '=', 'Exist')->first()->id,
                'value' => 'Exist',
                'id_enum' => 'RiskFor'
            ],
            1 => [
                'id' => EnumRiskFor::all()->where('value', '=', 'Risk')->first()->id,
                'value' => 'Risk',
                'id_enum' => 'RiskFor'
            ],
        ]);
    }
}
