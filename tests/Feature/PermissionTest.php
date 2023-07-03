<?php

namespace Tests\Feature;

use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Conception Number: 1
     * Try to add as validated equipment without the permission
     * Expected result: Receiving an error: "Un truc"
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
            'eq_massUnit' => 'three'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
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
            'eq_massUnit' => 'three'
        ]);
        $response->assertStatus(101);
    }

    public function make_a_user_with_no_permission()
    {
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
     * Try to update as validated equipment without the permission
     * Expected result: Receiving an error: "Un truc"
     * @return void
     */
    public function test_update_new_equipment_as_validated()
    {
        $user_id = $this->make_a_user_with_no_permission();

        $eq_id = $this->add_eq($user_id);

        // Try to update equipment as validated
        $response = $this->post('/equipment/update/' . $eq_id, [
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
            'eq_massUnit' => 'three'
        ]);
        $response->assertStatus(101);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_location' => 'three',
            'eqTemp_validate' => 'to_be_validated',
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_mass' => 1234,
            'eqTemp_remarks' => 'three',
            'eqTemp_mobility' => null,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'three')->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'three')->first()->id,
        ]);
    }

    public function add_eq($user_id)
    {
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
            'eq_validate' => 'to_be_validated',
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
            'eq_massUnit' => 'three'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'to_be_validated',
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
            'eq_massUnit' => 'three'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_location' => 'three',
            'eqTemp_validate' => 'to_be_validated',
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_mass' => 1234,
            'eqTemp_remarks' => 'three',
            'eqTemp_mobility' => null,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'three')->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'three')->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        return Equipment::all()->last()->id;
    }
}
