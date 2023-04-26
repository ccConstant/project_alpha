<?php

/**
 * Filename: EquipmentTest.php
 * Creation date: 21 Apr 2023
 * Update date: 21 Apr 2023
 * This file contains all the tests about the equipment table.
 * Coverage : 100%
 */

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EquipmentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test Conception Number: 1
     * Add new equipment as drafted with no values
     * Internal Ref: /
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter an internal reference"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_drafted_no_values()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter an internal reference',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 2
     * Add new equipment as drafted with a too short internal ref
     * Internal Ref: "in"
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_drafted_short_intern_ref()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'in'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter at least 3 characters',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 3
     * Add a new equipment as drafted with a too long internal ref
     * Internal Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 16 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_drafted_long_intern_ref()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non '
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter a maximum of 16 characters',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 4
     * Add a new equipment as drafted with a too long name
     * Internal Ref: "three"
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor."
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_drafted_long_name()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_name' => 'You must enter a maximum of 100 characters',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 5
     * Add a new equipment as drafted with a too short external ref
     * Internal Ref: "three"
     * Name: /
     * External Ref: "in"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     * @returns void
     */
    public function test_add_equipment_drafted_short_alpha_ref()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'in'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_externalReference' => 'You must enter at least 3 characters'
        ]);
    }

    /**
     * Test Conception Number: 6
     * Add a new equipment as drafted with a too long external ref
     * Internal Ref: "three"
     * Name: /
     * External Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor."
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     * @returns void
     */
    public function test_add_equipment_drafted_long_alpha_ref()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_externalReference' => 'You must enter a maximum of 100 characters'
        ]);
    }

    /**
     * Test Conception Number: 7
     * Add a new equipment as drafted with a too long serial number
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_equipment_drafted_long_serial_number()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_serialNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non '
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_serialNumber' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Add a new equipment as drafted with a too long constructor
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 30 characters"
     * @returns void
     */
    public function test_add_equipment_drafted_long_constructor()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_constructor' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non '
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_constructor' => 'You must enter a maximum of 30 characters'
        ]);
    }

    /**
     * Test Conception Number: ç
     * Add a new equipment as drafted with a too long mass
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 8 characters"
     * @returns void
     */
    public function test_add_equipment_drafted_long_mass()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_mass' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non '
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_mass' => 'You must enter a maximum of 8 characters'
        ]);
    }

    /**
     * Test Conception Number: 10
     * Add a new equipment as drafted with a too long set
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit:
     * Mobil ? : /
     * Remarks: /
     * Set: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 20 characters"
     * @returns void
     */
    public function test_add_equipment_drafted_long_set()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_set' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non '
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_set' => 'You must enter a maximum of 20 characters'
        ]);
    }

    /**
     * Test Conception Number: 11
     * Successfully saved a new equipment as drafted
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: The equipment is saved as drafted and successfully added in the database
     * @returns void
     */
    public function test_add_equipment_drafted_success()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three'
        ]);
        $response->assertStatus(200);
        $this->post('/equipment/add', [
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three'
        ]);
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three'
        ]);
    }

    /**
     * Test Conception Number: 12
     * Add a new equipment as to be validated with no values
     * Internal Ref: /
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter an internal reference"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_no_values()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter an internal reference',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 13
     * Add a new equipment as to be validated with a too short internal reference
     * Internal Ref: "in"
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_short_internal_reference()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'in'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter at least 3 characters',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 14
     * Add a new equipment as to be validated with a too long internal reference
     * Internal Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 16 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_internal_reference()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non '
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter a maximum of 16 characters',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 15
     * Add a new equipment as to be validated with a too long name
     * Internal Ref: "three"
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. "
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_name()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_name' => 'You must enter a maximum of 100 characters',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 16
     * Add a new equipment as to be validated with a too short external reference
     * Internal Ref: "three"
     * Name: /
     * External Ref: "in"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_short_external_reference()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'in'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_externalReference' => 'You must enter at least 3 characters'
        ]);
    }

    /**
     * Test Conception Number: 17
     * Add a new equipment as to be validated with a too long external reference
     * Internal Ref: "three"
     * Name: /
     * External Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. "
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_external_reference()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. '
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_externalReference' => 'You must enter a maximum of 100 characters'
        ]);
    }

    /**
     * Test Conception Number: 18
     * Add a new equipment as to be validated with a too long serial number
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_serial_number()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_serialNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non '
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_serialNumber' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 19
     * Add a new equipment as to be validated with a too long constructor
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_constructor()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_constructor' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non '
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_constructor' => 'You must enter a maximum of 30 characters'
        ]);
    }

    /**
     * Test Conception Number: 20
     * Add a new equipment as to be validated with a too long mass
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_mass()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_mass' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non '
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_mass' => 'You must enter a maximum of 8 characters'
        ]);
    }

    /**
     * Test Conception Number: 21
     * Add new equipment as to be validated with a too long set
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 20 characters"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_set()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_set' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non '
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_set' => 'You must enter a maximum of 20 characters'
        ]);
    }

    /**
     * Test Conception Number: 22
     * Successfully add new equipment as to be validated and check if it is in the database
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: The equipment is in the database
     * @returns void
     */
    public function test_add_equipment_to_be_validated_success()
    {
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three'
        ]);
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three'
        ]);
    }
}
