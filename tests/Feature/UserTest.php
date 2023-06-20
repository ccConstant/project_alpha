<?php

/**
 * Filename: UserTest.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file contains all the tests about the user table.
 * Coverage : 100%
 */

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_warning()
    {
        $this->assertTrue(true);
    }

    /*
     * Test Conception Number: 1
     * Save a User with no value
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "You must enter your firstName"
     *                                      "You must enter your lastName"
     *                                      "You must enter a pseudo"
     *                                      "You must enter a password"
     *                                      "You must confirm your password"
     * @returns void
     */
    public function test_add_an_empty_user()
    {
        $response = $this->post('register', []);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_firstName' => 'You must enter your firstName',
            'user_lastName' => 'You must enter your lastName',
            'user_pseudo' => 'You must enter a pseudo',
            'user_password' => 'You must enter a password',
            'user_confirmation_password' => 'You must confirm your password'
        ]);
    }

    /**
     * Test Conception Number: 2
     * Save a User with a too short first name
     * First name: "a"
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 2 characters"
     *                                      "You must enter your lastName"
     *                                      "You must enter a pseudo"
     *                                      "You must enter a password"
     *                                      "You must confirm your password"
     * @returns void
     */
    public function test_add_a_user_with_a_too_short_first_name()
    {
        $response = $this->post('register', [
            'user_firstName' => 'a'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_firstName' => 'You must enter at least 2 characters',
            'user_lastName' => 'You must enter your lastName',
            'user_pseudo' => 'You must enter a pseudo',
            'user_password' => 'You must enter a password',
            'user_confirmation_password' => 'You must confirm your password'
        ]);
    }

    /**
     * Test Conception Number: 3
     * Save a User with a too long first name
     * First name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     *                                      "You must enter your lastName"
     *                                      "You must enter a pseudo"
     *                                      "You must enter a password"
     *                                      "You must confirm your password"
     * @returns void
     */
    public function test_add_a_user_with_a_too_long_first_name() {
        $response = $this->post('register', [
            'user_firstName' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_firstName' => 'You must enter a maximum of 50 characters',
            'user_lastName' => 'You must enter your lastName',
            'user_pseudo' => 'You must enter a pseudo',
            'user_password' => 'You must enter a password',
            'user_confirmation_password' => 'You must confirm your password'
        ]);
    }

    /**
     * Test Conception Number: 4
     * Save a User with a too short last name
     * First name: "three"
     * Last name: "a"
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 2 characters"
     *                                      "You must enter a pseudo"
     *                                      "You must enter a password"
     *                                      "You must confirm your password"
     * @returns void
     */
    public function test_add_a_user_with_a_too_short_last_name() {
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'a'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_lastName' => 'You must enter at least 2 characters',
            'user_pseudo' => 'You must enter a pseudo',
            'user_password' => 'You must enter a password',
            'user_confirmation_password' => 'You must confirm your password'
        ]);
    }

    /**
     * Test Conception Number: 5
     * Save a User with a too long last name
     * First name: "three"
     * Last name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     *                                      "You must enter a pseudo"
     *                                      "You must enter a password"
     *                                      "You must confirm your password"
     * @returns void
     */
    public function test_add_a_user_with_a_too_long_last_name() {
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_lastName' => 'You must enter a maximum of 50 characters',
            'user_pseudo' => 'You must enter a pseudo',
            'user_password' => 'You must enter a password',
            'user_confirmation_password' => 'You must confirm your password'
        ]);
    }

    /**
     * Test Conception Number: 6
     * Save a User with a too short pseudo
     * First name: "three"
     * Last name: "three"
     * Pseudo: "a"
     * Password: /
     * Confirm password: /
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 2 characters"
     *                                      "You must enter a password"
     *                                      "You must confirm your password"
     * @returns void
     */
    public function test_add_a_user_with_a_too_short_pseudo() {
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 'a'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_pseudo' => 'You must enter at least 2 characters',
            'user_password' => 'You must enter a password',
            'user_confirmation_password' => 'You must confirm your password'
        ]);
    }

    /**
     * Test Conception Number: 7
     * Save a User with a too long pseudo
     * First name: "three"
     * Last name: "three"
     * Pseudo: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Password: /
     * Confirm password: /
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     *                                      "You must enter a password"
     *                                      "You must confirm your password"
     * @returns void
     */
    public function test_add_a_user_with_a_too_long_pseudo()
    {
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_pseudo' => 'You must enter a maximum of 50 characters',
            'user_password' => 'You must enter a password',
            'user_confirmation_password' => 'You must confirm your password'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Save a User with a too short password
     * First name: "three"
     * Last name: "three"
     * Pseudo: "three"
     * Password: "a"
     * Confirm password: /
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 8 characters"
     *                                      "You must confirm your password"
     * @returns void
     */
    public function test_add_a_user_with_a_too_short_password()
    {
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 'three',
            'user_password' => 'a'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_password' => 'You must enter at least 8 characters',
            'user_confirmation_password' => 'You must confirm your password'
        ]);
    }

    /**
     * Test Conception Number: 9
     * Save a User with a too short confirm password
     * First name: "three"
     * Last name: "three"
     * Pseudo: "three"
     * Password: "password"
     * Confirm password: "a"
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 8 characters"
     * @returns void
     */
    public function test_add_a_user_with_a_too_short_confirm_password()
    {
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 'three',
            'user_password' => 'password',
            'user_confirmation_password' => 'a'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_confirmation_password' => 'You must enter at least 8 characters'
        ]);
    }

    /**
     * Test Conception Number: 10
     * Save a User with a different password and confirm password
     * First name: "three"
     * Last name: "three"
     * Pseudo: "three"
     * Password: "password"
     * Confirm password: "different"
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "These passwords are different"
     * @returns void
     */
    public function test_add_a_user_with_a_different_password_confirm_password()
    {
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 'three',
            'user_password' => 'password',
            'user_confirmation_password' => 'different'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_confirmation_password' => 'These passwords are different'
        ]);
    }

    /**
     * Test Conception Number: 11
     * Save a User with a non string as first name
     * First name: 1234567890
     * Last name: "three"
     * Pseudo: "three"
     * Password: "password"
     * Confirm password: "password"
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "Your firstName must be of type string"
     * @returns void
     */
    public function test_add_a_user_with_a_non_string_first_name()
    {
        $response = $this->post('register', [
            'user_firstName' => 1234567890,
            'user_lastName' => 'three',
            'user_pseudo' => 'three',
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_firstName' => 'Your firstName must be of type string'
        ]);
    }

    /**
     * Test Conception Number: 12
     * Save a User with a non string as last name
     * First name: "three"
     * Last name: 1234567890
     * Pseudo: "three"
     * Password: "password"
     * Confirm password: "password"
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "Your lastName must be of type string"
     * @returns void
     */
    public function test_add_a_user_with_a_non_string_last_name()
    {
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 1234567890,
            'user_pseudo' => 'three',
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_lastName' => 'Your lastName must be of type string'
        ]);
    }

    /**
     * Test Conception Number: 13
     * Save a User with a non string as pseudo
     * First name: "three"
     * Last name: "three"
     * Pseudo: 1234567890
     * Password: "password"
     * Confirm password: "password"
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "Your pseudo must be of type string"
     * @returns void
     */
    public function test_add_a_user_with_a_non_string_pseudo()
    {
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 1234567890,
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_pseudo' => 'Your pseudo must be of type string'
        ]);
    }

    /**
     * Test Conception Number: 14
     * Save a User with a non string as password
     * First name: "three"
     * Last name: "three"
     * Pseudo: "three"
     * Password: 1234567890
     * Confirm password: "password"
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "Your password must be of type string"
     * @returns void
     */
    public function test_add_a_user_with_a_non_string_password()
    {
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 'three',
            'user_password' => 1234567890,
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_password' => 'Your password must be of type string'
        ]);
    }

    /**
     * Test Conception Number: 15
     * Save a User with a non string as confirm password
     * First name: "three"
     * Last name: "three"
     * Pseudo: "three"
     * Password: "password"
     * Confirm password: 1234567890
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "Your password must be of type string"
     * @returns void
     */
    public function test_add_a_user_with_a_non_string_confirm_password()
    {
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 'three',
            'user_password' => 'password',
            'user_confirmation_password' => 1234567890
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_confirmation_password' => 'Your password must be of type string'
        ]);
    }

    /**
     * Test Conception Number: 16
     * Save a User with correct data
     * First name: "three"
     * Last name: "three"
     * Pseudo: "three"
     * Password: "password"
     * Confirm password: "password"
     * Initial: /
     * Expected result: The user is saved and correctly added in the database
     * @returns void
     */
    public function test_add_a_user_with_correct_data()
    {
        $countUsers = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 'three',
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countUsers + 1, User::all()->count());
        $this->assertDatabaseHas('users', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 'three'
        ]);
        $this->assertTrue(Hash::check('password', User::all()->where('user_pseudo', '==', 'three')->first()->password));
    }

    /**
     * Test Conception Number: 17
     * Save a User with the same data, as a previous one
     * First name: "three"
     * Last name: "three"
     * Pseudo: "three"
     * Password: "password"
     * Confirm password: "password"
     * Initial: /
     * Expected result: Receiving an error:
     *                                      "This username is already used"
     * @returns void
     */
    public function test_add_a_user_with_the_same_data_as_a_previous_one()
    {
        // Add the first one
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        // Add the duplicate one
        $response = $this->post('register', [
            'user_firstName' => 'three',
            'user_lastName' => 'three',
            'user_pseudo' => 'three',
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_pseudo' => 'This username is already used'
        ]);
    }

    private function add_an_admin_user(): void
    {
        $countUsers = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'admin',
            'user_lastName' => 'admin',
            'user_pseudo' => 'admin',
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countUsers + 1, User::all()->count());
        $this->assertDatabaseHas('users', [
            'user_firstName' => 'admin',
            'user_lastName' => 'admin',
            'user_pseudo' => 'admin'
        ]);
        $this->assertTrue(Hash::check('password', User::all()->where('user_pseudo', '==', 'admin')->first()->password));
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $admin->update([
            'user_initials' => "AA",
            'user_signaturePath' => "SignaturePath",
            'user_menuUserAcessRight' => true,
            'user_resetUserPasswordRight' => true,
            'user_updateDataInDraftRight' => true,
            'user_validateDescriptiveLifeSheetDataRight' => true,
            'user_validateOtherDataRight' => true,
            'user_updateDataValidatedButNotSignedRight' => true,
            'user_updateDescriptiveLifeSheetDataSignedRight' => true,
            'user_makeQualityValidationRight' => true,
            'user_makeTechnicalValidationRight' => true,
            'user_deleteDataNotValidatedLinkedToEqOrMmeRight' => true,
            'user_deleteDataValidatedLinkedToEqOrMmeRight' => true,
            'user_deleteDataSignedLinkedToEqOrMmeRight' => true,
            'user_deleteEqOrMmeRight' => true,
            'user_makeReformRight' => true,
            'user_declareNewStateRight' => true,
            'user_updateEnumRight' => true,
            'user_deleteEnumRight' => true,
            'user_addEnumRight' => true,
            'user_updateInformationRight' => true,
            'user_makeEqOpValidationRight' => true,
            'user_personTrainedToGeneralPrinciplesOfEqManagementRight' => true,
            'user_makeEqRespValidationRight' => true,
            'user_personTrainedToGeneralPrinciplesOfMMEManagementRight' => true,
            'user_makeMmeOpValidationRight' => true,
            'user_makeMmeRespValidationRight' => true
        ]);
    }

    private function edit_own_permission($uri): void
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update_right/user_'.$uri.'/'.$user->id, [
            'user_id' => $user->id,
            'user_value' => true
        ]);
        $response->assertStatus(429);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'user_'.$uri => false
        ]);
    }

    private function edit_permission_of_admin($uri): void
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if ($uri == 'resetUserPasswordRight') {
            $user->update([
                'user_resetUserPasswordRight' => true
            ]);
        }
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update_right/user_'.$uri.'/'.$admin->id, [
            'user_id' => $user->id,
            'user_value' => false
        ]);
        $response->assertStatus(429);
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'user_'.$uri => true
        ]);
        if ($uri == 'resetUserPasswordRight') {
            $user->update([
                'user_resetUserPasswordRight' => false
            ]);
        }
    }

    /**
     * This function is used to edit "three" user permission by the admin user
     * @param $uri
     * @return void
     */
    private function edit_permission_of_another_user($uri): void
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update_right/user_'.$uri.'/'.$user->id, [
            'user_id' => $admin->id,
            'user_value' => true
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'user_'.$uri => true
        ]);
        $response = $this->post('/user/update_right/user_'.$uri.'/'.$user->id, [
            'user_id' => $admin->id,
            'user_value' => false
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'user_'.$uri => false
        ]);
    }

    /**
     * Test Conception Number: 18
     * Update his own menuUserAccessRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_menuUserAccessRight_permission()
    {
        $this->edit_own_permission('menuUserAcessRight');
    }

    /**
     * Test Conception Number: 19
     * Update the menuUserAccessRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_menuUserAccessRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('menuUserAcessRight');
    }

    /**
     * Test Conception Number: 20
     * Update the permission of a User, we put the menuUserAccessRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_menuUserAccessRight()
    {
        $this->edit_permission_of_another_user('menuUserAcessRight');
    }

    /**
     * Test Conception Number: 21
     * Update his own resetUserPasswordRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_resetUserPasswordRight_permission()
    {
        $this->edit_own_permission('resetUserPasswordRight');
    }

    /**
     * Test Conception Number: 22
     * Update the resetUserPasswordRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_resetUserPasswordRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('resetUserPasswordRight');
    }

    /**
     * Test Conception Number: 23
     * Update the permission of a User, we put the resetUserPasswordRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_resetUserPasswordRight()
    {
        $this->edit_permission_of_another_user('resetUserPasswordRight');
    }

    /**
     * Test Conception Number: 24
     * Update the permission of a User, we put the resetUserPasswordRight to false without the right
     * Expected result: Receiving an error:
     *                                      "You don't have the right to update this permission"
     * @returns void
     */
    public function test_update_a_user_permission_resetUserPasswordRight_without_the_right()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        // Add an other user
        $countUsers = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'other',
            'user_lastName' => 'other',
            'user_pseudo' => 'other',
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countUsers + 1, User::all()->count());
        $this->assertDatabaseHas('users', [
            'user_firstName' => 'other',
            'user_lastName' => 'other',
            'user_pseudo' => 'other'
        ]);
        $this->assertTrue(Hash::check('password', User::all()->where('user_pseudo', '==', 'other')->first()->password));
        $other = User::all()->where('user_pseudo', '==', 'other')->first();
        // Update the permission of the other user
        $response = $this->post('/user/update_right/user_resetUserPasswordRight/'.$user->id, [
            'user_id' => $other->id,
            'user_value' => true
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user' => 'You don\'t have the right to modify the password of another user'
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $other->id,
            'user_resetUserPasswordRight' => false
        ]);
    }

    /**
     * Test Conception Number: 25
     * Update his own updateDataInDraftRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_updateDataInDraftRight_permission()
    {
        $this->edit_own_permission('updateDataInDraftRight');
    }

    /**
     * Test Conception Number: 26
     * Update the updateDataInDraftRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_updateDataInDraftRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('updateDataInDraftRight');
    }

    /**
     * Test Conception Number: 27
     * Update the permission of a User, we put the updateDataInDraftRight to false
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_a_user_permission_updateDataInDraftRight()
    {
        $this->edit_permission_of_another_user('updateDataInDraftRight');
    }

    /**
     * Test Conception Number: 28
     * Update his own validateDescriptiveLifeSheetDataRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_validateDescriptiveLifeSheetDataRight_permission()
    {
        $this->edit_own_permission('validateDescriptiveLifeSheetDataRight');
    }

    /**
     * Test Conception Number: 29
     * Update the validateDescriptiveLifeSheetDataRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_validateDescriptiveLifeSheetDataRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('validateDescriptiveLifeSheetDataRight');
    }

    /**
     * Test Conception Number: 30
     * Update the permission of a User, we put the validateDescriptiveLifeSheetDataRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_validateDescriptiveLifeSheetDataRight()
    {
        $this->edit_permission_of_another_user('validateDescriptiveLifeSheetDataRight');
    }

    /**
     * Test Conception Number: 31
     * Update his own validateOtherDataRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_validateOtherDataRight_permission()
    {
        $this->edit_own_permission('validateOtherDataRight');
    }

    /**
     * Test Conception Number: 32
     * Update the validateOtherDataRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @return void
     */
    public function test_update_the_validateOtherDataRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('validateOtherDataRight');
    }

    /**
     * Test Conception Number: 33
     * Update the permission of a User, we put the validateOtherDataRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_validateOtherDataRight()
    {
        $this->edit_permission_of_another_user('validateOtherDataRight');
    }

    /**
     * Test Conception Number: 34
     * Update his own updateDataValidatedButNotSignedRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_updateDataValidatedButNotSignedRight_permission()
    {
        $this->edit_own_permission('updateDataValidatedButNotSignedRight');
    }

    /**
     * Test Conception Number: 35
     * Update the updateDataValidatedButNotSignedRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_updateDataValidatedButNotSignedRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('updateDataValidatedButNotSignedRight');
    }

    /**
     * Test Conception Number: 36
     * Update the permission of a User, we put the updateDataValidatedButNotSignedRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_updateDataValidatedButNotSignedRight()
    {
        $this->edit_permission_of_another_user('updateDataValidatedButNotSignedRight');
    }

    /**
     * Test Conception Number: 37
     * Update his own updateDescriptiveLifeSheetDataSignedRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_updateDescriptiveLifeSheetDataSignedRight_permission()
    {
        $this->edit_own_permission('updateDescriptiveLifeSheetDataSignedRight');
    }

    /**
     * Test Conception Number: 38
     * Update the updateDescriptiveLifeSheetDataSignedRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_updateDescriptiveLifeSheetDataSignedRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('updateDescriptiveLifeSheetDataSignedRight');
    }

    /**
     * Test Conception Number: 39
     * Update the permission of a User, we put the updateDescriptiveLifeSheetDataSignedRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_updateDescriptiveLifeSheetDataSignedRight()
    {
        $this->edit_permission_of_another_user('updateDescriptiveLifeSheetDataSignedRight');
    }

    /**
     * Test Conception Number: 40
     * Update his own makeQualityValidationRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_makeQualityValidationRight_permission()
    {
        $this->edit_own_permission('makeQualityValidationRight');
    }

    /*
     * Test Conception Number: 41
     * Update the makeQualityValidationRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_makeQualityValidationRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('makeQualityValidationRight');
    }

    /**
     * Test Conception Number: 42
     * Update the permission of a User, we put the makeQualityValidationRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_makeQualityValidationRight()
    {
        $this->edit_permission_of_another_user('makeQualityValidationRight');
    }

    /**
     * Test Conception Number: 43
     * Update his own makeTechnicalValidationRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_makeTechnicalValidationRight_permission()
    {
        $this->edit_own_permission('makeTechnicalValidationRight');
    }

    /**
     * Test Conception Number: 44
     * Update the makeTechnicalValidationRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_makeTechnicalValidationRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('makeTechnicalValidationRight');
    }

    /**
     * Test Conception Number: 45
     * Update the permission of a User, we put the makeTechnicalValidationRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_makeTechnicalValidationRight()
    {
        $this->edit_permission_of_another_user('makeTechnicalValidationRight');
    }

    /**
     * Test Conception Number: 46
     * Update his own makeEqOpValidationRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_makeEqOpValidation_permission()
    {
        $this->edit_own_permission('makeEqOpValidationRight');
    }

    /**
     * Test Conception Number: 47
     * Update the makeEqOpValidationRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_makeEqOpValidation_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('makeEqOpValidationRight');
    }

    /**
     * Test Conception Number: 48
     * Update the permission of a User, we put the makeEqOpValidationRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_makeEqOpValidation()
    {
        $this->edit_permission_of_another_user('makeEqOpValidationRight');
    }

    /**
     * Test Conception Number: 49
     * Update his own makeMmeOpValidationRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_makeMmeOpValidation_permission()
    {
        $this->edit_own_permission('makeMmeOpValidationRight');
    }

    /**
     * Test Conception Number: 50
     * Update the makeMmeOpValidationRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_makeMmeOpValidation_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('makeMmeOpValidationRight');
    }

    /**
     * Test Conception Number: 51
     * Update the permission of a User, we put the makeMmeOpValidationRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_makeMmeOpValidation()
    {
        $this->edit_permission_of_another_user('makeMmeOpValidationRight');
    }

    /**
     * Test Conception Number: 52
     * Update his own updateEnumRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_updateEnumRight_permission()
    {
        $this->edit_own_permission('updateEnumRight');
    }

    /**
     * Test Conception Number: 53
     * Update the updateEnumRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_updateEnumRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('updateEnumRight');
    }

    /**
     * Test Conception Number: 54
     * Update the permission of a User, we put the updateEnumRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_updateEnumRight()
    {
        $this->edit_permission_of_another_user('updateEnumRight');
    }

    /**
     * Test Conception Number: 55
     * Update his own deleteEnumRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_deleteEnumRight_permission()
    {
        $this->edit_own_permission('deleteEnumRight');
    }

    /**
     * Test Conception Number: 56
     * Update the deleteEnumRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_deleteEnumRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('deleteEnumRight');
    }

    /**
     * Test Conception Number: 57
     * Update the permission of a User, we put the deleteEnumRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_deleteEnumRight()
    {
        $this->edit_permission_of_another_user('deleteEnumRight');
    }

    /**
     * Test Conception Number: 58
     * Update his own addEnumRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_addEnumRight_permission()
    {
        $this->edit_own_permission('addEnumRight');
    }

    /**
     * Test Conception Number: 59
     * Update the addEnumRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_addEnumRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('addEnumRight');
    }

    /**
     * Test Conception Number: 60
     * Update the permission of a User, we put the addEnumRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_addEnumRight()
    {
        $this->edit_permission_of_another_user('addEnumRight');
    }

    /**
     * Test Conception Number: 61
     * Update his own deleteDataNotValidatedLinkedToEqOrMmeRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_deleteDataNotValidatedLinkedToEqOrMmeRight_permission()
    {
        $this->edit_own_permission('deleteDataNotValidatedLinkedToEqOrMmeRight');
    }

    /**
     * Test Conception Number: 62
     * Update the deleteDataNotValidatedLinkedToEqOrMmeRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_deleteDataNotValidatedLinkedToEqOrMmeRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('deleteDataNotValidatedLinkedToEqOrMmeRight');
    }

    /**
     * Test Conception Number: 63
     * Update the permission of a User, we put the deleteDataNotValidatedLinkedToEqOrMmeRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_deleteDataNotValidatedLinkedToEqOrMmeRight()
    {
        $this->edit_permission_of_another_user('deleteDataNotValidatedLinkedToEqOrMmeRight');
    }

    /**
     * Test Conception Number: 64
     * Update his own deleteDataValidatedLinkedToEqOrMmeRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_deleteDataValidatedLinkedToEqOrMmeRight_permission()
    {
        $this->edit_own_permission('deleteDataValidatedLinkedToEqOrMmeRight');
    }

    /**
     * Test Conception Number: 65
     * Update the deleteDataValidatedLinkedToEqOrMmeRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_deleteDataValidatedLinkedToEqOrMmeRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('deleteDataValidatedLinkedToEqOrMmeRight');
    }

    /**
     * Test Conception Number: 66
     * Update the permission of a User, we put the deleteDataValidatedLinkedToEqOrMmeRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_deleteDataValidatedLinkedToEqOrMmeRight()
    {
        $this->edit_permission_of_another_user('deleteDataValidatedLinkedToEqOrMmeRight');
    }

    /**
     * Test Conception Number: 67
     * Update his own deleteDataSignedLinkedToEqOrMmeRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_deleteDataSignedLinkedToEqOrMmeRight_permission()
    {
        $this->edit_own_permission('deleteDataSignedLinkedToEqOrMmeRight');
    }

    /**
     * Test Conception Number: 68
     * Update the deleteDataSignedLinkedToEqOrMmeRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_deleteDataSignedLinkedToEqOrMmeRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('deleteDataSignedLinkedToEqOrMmeRight');
    }

    /**
     * Test Conception Number: 69
     * Update the permission of a User, we put the deleteDataSignedLinkedToEqOrMmeRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_deleteDataSignedLinkedToEqOrMmeRight()
    {
        $this->edit_permission_of_another_user('deleteDataSignedLinkedToEqOrMmeRight');
    }

    /**
     * Test Conception Number: 70
     * Update his own deleteEqOrMmeRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_deleteEqOrMmeRight_permission()
    {
        $this->edit_own_permission('deleteEqOrMmeRight');
    }

    /**
     * Test Conception Number: 71
     * Update the deleteEqOrMmeRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_deleteEqOrMmeRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('deleteEqOrMmeRight');
    }

    /**
     * Test Conception Number: 72
     * Update the permission of a User, we put the deleteEqOrMmeRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_deleteEqOrMmeRight()
    {
        $this->edit_permission_of_another_user('deleteEqOrMmeRight');
    }

    /**
     * Test Conception Number: 73
     * Update his own updateInformationRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_updateInformationRight_permission()
    {
        $this->edit_own_permission('updateInformationRight');
    }

    /**
     * Test Conception Number: 74
     * Update the updateInformationRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_updateInformationRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('updateInformationRight');
    }

    /**
     * Test Conception Number: 75
     * Update the permission of a User, we put the updateInformationRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_updateInformationRight()
    {
        $this->edit_permission_of_another_user('updateInformationRight');
    }

    /**
     * Test Conception Number: 76
     * Update his own personTrainedToGeneralPrinciplesOfEqManagementRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_personTrainedToGeneralPrinciplesOfEqManagementRight_permission()
    {
        $this->edit_own_permission('personTrainedToGeneralPrinciplesOfEqManagementRight');
    }

    /**
     * Test Conception Number: 77
     * Update the personTrainedToGeneralPrinciplesOfEqManagementRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_personTrainedToGeneralPrinciplesOfEqManagementRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('personTrainedToGeneralPrinciplesOfEqManagementRight');
    }

    /**
     * Test Conception Number: 78
     * Update the permission of a User, we put the personTrainedToGeneralPrinciplesOfEqManagementRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_personTrainedToGeneralPrinciplesOfEqManagementRight()
    {
        $this->edit_permission_of_another_user('personTrainedToGeneralPrinciplesOfEqManagementRight');
    }

    /**
     * Test Conception Number: 79
     * Update his own personTrainedToGeneralPrinciplesOfMMEManagementRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_personTrainedToGeneralPrinciplesOfMMEManagementRight_permission()
    {
        $this->edit_own_permission('personTrainedToGeneralPrinciplesOfMMEManagementRight');
    }

    /**
     * Test Conception Number: 80
     * Update the personTrainedToGeneralPrinciplesOfMMEManagementRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_personTrainedToGeneralPrinciplesOfMMEManagementRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('personTrainedToGeneralPrinciplesOfMMEManagementRight');
    }

    /**
     * Test Conception Number: 81
     * Update the permission of a User, we put the personTrainedToGeneralPrinciplesOfMMEManagementRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_personTrainedToGeneralPrinciplesOfMMEManagementRight()
    {
        $this->edit_permission_of_another_user('personTrainedToGeneralPrinciplesOfMMEManagementRight');
    }

    /**
     * Test Conception Number: 82
     * Update his own makeEqRespValidationRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_makeEqRespValidationRight_permission()
    {
        $this->edit_own_permission('makeEqRespValidationRight');
    }

    /**
     * Test Conception Number: 83
     * Update the makeEqRespValidationRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_makeEqRespValidationRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('makeEqRespValidationRight');
    }

    /**
     * Test Conception Number: 84
     * Update the permission of a User, we put the makeEqRespValidationRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_makeEqRespValidationRight()
    {
        $this->edit_permission_of_another_user('makeEqRespValidationRight');
    }

    /**
     * Test Conception Number: 85
     * Update his own makeMmeRespValidationRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_makeMmeRespValidationRight_permission()
    {
        $this->edit_own_permission('makeMmeRespValidationRight');
    }

    /**
     * Test Conception Number: 86
     * Update the makeMmeRespValidationRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_makeMmeRespValidationRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('makeMmeRespValidationRight');
    }

    /**
     * Test Conception Number: 87
     * Update the permission of a User, we put the makeMmeRespValidationRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_makeMmeRespValidationRight()
    {
        $this->edit_permission_of_another_user('makeMmeRespValidationRight');
    }

    /**
     * Test Conception Number: 88
     * Update his own makeReformRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_makeReformRight_permission()
    {
        $this->edit_own_permission('makeReformRight');
    }

    /**
     * Test Conception Number: 89
     * Update the makeReformRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_makeReformRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('makeReformRight');
    }

    /**
     * Test Conception Number: 90
     * Update the permission of a User, we put the makeReformRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_makeReformRight()
    {
        $this->edit_permission_of_another_user('makeReformRight');
    }

    /**
     * Test Conception Number: 91
     * Update his own declareNewStateRight permission
     * Expected result: Receiving an error:
     *                                      "You can't update your own permission"
     * @returns void
     */
    public function test_update_his_own_declareNewStateRight_permission()
    {
        $this->edit_own_permission('declareNewStateRight');
    }

    /**
     * Test Conception Number: 92
     * Update the declareNewStateRight permission of the admin User
     * Expected result: Receiving an error:
     *                                      "You can't update the permission of the admin user"
     * @returns void
     */
    public function test_update_the_declareNewStateRight_permission_of_the_admin_user()
    {
        $this->edit_permission_of_admin('declareNewStateRight');
    }

    /**
     * Test Conception Number: 93
     * Update the permission of a User, we put the declareNewStateRight to false
     * Expected result: The user permissions are correctly updated in the database
     * @returns void
     */
    public function test_update_a_user_permission_declareNewStateRight()
    {
        $this->edit_permission_of_another_user('declareNewStateRight');
    }

    /**
     * Test Conception Number: 94
     * Update his own informations
     * First name: "new three"
     * Last name: "new three"
     * Pseudo: "new three"
     * Password: "password1"
     * Confirm password: "password1"
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "You can't modify your own information here, please go in myAccount menu"
     * @returns void
     */
    public function test_update_his_own_informations()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_firstname' => 'new three',
            'user_lastname' => 'new three',
            'user_pseudo' => 'new three',
            'user_password' => 'password1',
            'user_confirmation_password' => 'password1',
            'user_id' => $user->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_confirmation_password' => 'You can\'t modify your own information here, please go in myAccount menu'
        ]);
    }

    /**
     * Test Conception Number: 95
     * Update the informations of the admin User
     * First name: "new admin"
     * Last name: "new admin"
     * Pseudo: "new admin"
     * Password: "password1"
     * Confirm password: "password1"
     * Initials: /
     * Expected result: Receiving an error:
     *                                     "You can't modify the information of the admin user"
     * @returns void
     */
    public function test_update_the_informations_of_the_admin_user()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$admin->id, [
            'user_firstname' => 'new admin',
            'user_lastname' => 'new admin',
            'user_pseudo' => 'new admin',
            'user_password' => 'password1',
            'user_confirmation_password' => 'password1',
            'user_id' => $user->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_confirmation_password' => 'You can\'t modify the information of the admin'
        ]);
    }

    /**
     * Test Conception Number: 96
     * Update the initial of another with a too short value
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: "T"
     * Expected result: Receiving an error:
     *                                      "You must enter at least 2 characters"
     * @returns void
     */
    public function test_update_the_initial_of_another_with_a_too_short_value()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_initials' => 'T',
            'user_id' => $admin->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_initials' => 'You must enter at least 2 characters'
        ]);
    }

    /**
     * Test Conception Number: 97
     * Update the initial of another with a too long value
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: "Three"
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 4 characters"
     * @returns void
     */
    public function test_update_the_initial_of_another_with_a_too_long_value()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_initials' => 'Three',
            'user_id' => $admin->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_initials' => 'You must enter a maximum of 4 characters'
        ]);
    }
    /**
     * Test Conception Number: 98
     * Update the initial of another with an already used value
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: "AA"
     * Expected result: Receiving an error:
     *                                      "This initials is already used"
     * @returns void
     */
    public function test_update_the_initial_of_another_with_an_already_used_value()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_initials' => 'AA',
            'user_id' => $admin->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_initials' => 'This initials are already used'
        ]);
    }

    /**
     * Test Conception Number: 99
     * Update the initial of another with a non string value
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: 12
     * Expected result: Receiving an error:
     *                                      "Your initials must be of type string"
     * @returns void
     */
    public function test_update_the_initials_non_string_value()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_initials' => 12,
            'user_id' => $admin->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_initials' => 'Your initials must be of type string'
        ]);
    }

    /**
     * Test Conception Number: 100
     * Update the working date of another with an end date before the start date
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: /
     * Start date: Carbon::now()
     * End date: Carbon::now()->subYear()
     * Expected result: Receiving an error:
     *                                      "You can't entered a leave date that is before start date"
     * @returns void
     */
    public function test_update_the_working_date_of_another_with_an_end_date_before_the_start_date()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $user->update([
            'user_formationEqDate' => Null,
            'user_formationsMmeDate' => Null,
            'user_endDate' => Null
        ]);
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_startDate' => Carbon::now(),
            'user_endDate' => Carbon::now()->subYear(),
            'user_id' => $admin->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_endDate' => 'You can\'t entered a leave date that is before start date'
        ]);
    }

    /**
     * Test Conception Number: 101
     * Update the working date of another with an end date after the start date
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: /
     * Start date: Carbon::now()
     * End date: Carbon::now()->addYear()
     * Expected result: The user is saved and correctly updated in the database
     * @returns void
     */
    public function test_update_the_working_date_of_another_with_an_end_date_after_the_start_date()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $user->update([
            'user_formationEqDate' => Null,
            'user_formationsMmeDate' => Null,
            'user_endDate' => Null
        ]);
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_startDate' => Carbon::now(),
            'user_endDate' => Carbon::now()->addYear(),
            'user_id' => $admin->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'user_firstname' => 'three',
            'user_lastname' => 'three',
            'user_pseudo' => 'three',
            'user_startDate' => Carbon::now()->format('Y-m-d'),
            'user_endDate' => Carbon::now()->addYear()->format('Y-m-d'),
        ]);
    }

    /**
     * Test Conception Number: 102
     * Update the formation date (EQ) of another with a correct date
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: /
     * EQ formation date: Carbon::now()
     * Expected result: The user is saved and correctly updated in the database
     * @returns void
     */
    public function test_update_the_eq_formation_date_of_another_with_a_correct_date()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $user->update([
            'user_formationEqDate' => Null,
            'user_formationsMmeDate' => Null,
            'user_endDate' => Null
        ]);
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_formationEqDate' => Carbon::now(),
            'user_id' => $admin->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'user_firstname' => 'three',
            'user_lastname' => 'three',
            'user_pseudo' => 'three',
            'user_formationEqDate' => Carbon::now()->format('Y-m-d'),
        ]);
    }

    /**
     * Test Conception Number: 103
     * Update the formation date (EQ) of another with an end date before the previously added
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: /
     * First EQ formation date: Carbon::now()
     * Second EQ formation date: Carbon::now()->subYear()
     * Expected result: Receiving an error:
     *                                      "You have to entered a formation equipment date that is after the previous formation equipment date"
     * @returns void
     */
    public function test_update_the_eq_formation_date_of_another_with_an_end_date_before_the_previously_added()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $user->update([
            'user_formationEqDate' => Null,
            'user_formationsMmeDate' => Null,
            'user_endDate' => Null
        ]);
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_formationEqDate' => Carbon::now(),
            'user_id' => $admin->id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_formationEqDate' => Carbon::now()->subYear(),
            'user_id' => $admin->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_formationEqDate' => 'You have to entered a formation equipment date that is after the previous formation equipment date'
        ]);
    }

    /**
     * Test Conception Number: 104
     * Update the formation date (MME) of another with a correct date
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: /
     * MME formation date: Carbon::now()
     * Expected result: The user is saved and correctly updated in the database
     * @returns void
     */
    public function test_update_the_mme_formation_date_of_another_with_a_correct_date()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $user->update([
            'user_formationEqDate' => Null,
            'user_formationsMmeDate' => Null,
            'user_endDate' => Null
        ]);
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_formationMmeDate' => Carbon::now(),
            'user_id' => $admin->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'user_firstname' => 'three',
            'user_lastname' => 'three',
            'user_pseudo' => 'three',
            'user_formationMmeDate' => Carbon::now()->format('Y-m-d'),
        ]);
    }

    /**
     * Test Conception Number: 105
     * Update the formation date (MME) of another with an end date before the previously added
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: /
     * First MME formation date: Carbon::now()
     * Second MME formation date: Carbon::now()->subYear()
     * Expected result: Receiving an error:
     *                                      "You have to entered a formation mme date that is after the previous formation mme date"
     * @returns void
     */
    public function test_update_the_mme_formation_date_of_another_with_an_end_date_before_the_previously_added()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $user->update([
            'user_formationEqDate' => Null,
            'user_formationsMmeDate' => Null,
            'user_endDate' => Null
        ]);
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_formationMmeDate' => Carbon::now(),
            'user_id' => $admin->id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_formationMmeDate' => Carbon::now()->subYear(),
            'user_id' => $admin->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_formationMmeDate' => 'You have to entered a formation mme date that is after the previous formation mme date'
        ]);
    }

    /**
     * Test Conception Number: 106
     * Update the password of another with a too short value
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: "pass"
     * Confirm password: /
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 8 characters"
     *                                      "You must confirm your password"
     * @returns void
     */
    public function test_update_the_password_of_another_with_a_too_short_value()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_password' => 'pass',
            'user_id' => $admin->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_password' => 'You must enter at least 8 characters',
            'user_confirmation_password' => 'You must confirm your password'
        ]);
    }

    /**
     * Test Conception Number: 107
     * Update the confirmation password of another with a too short value
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: "pass"
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "You must enter a password"
     *                                      "You must enter at least 8 characters"
     * @returns void
     */
    public function test_update_the_confirmation_password_of_another_with_a_too_short_value()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_confirmation_password' => 'pass',
            'user_id' => $admin->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_password' => 'You must enter a password',
            'user_confirmation_password' => 'You must enter at least 8 characters'
        ]);
    }

    /**
     * Test Conception Number: 108
     * Update the confirmation password of another with a differents password and confirmation password
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: "password"
     * Confirm password: "password1"
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "These passwords are differents"
     * @returns void
     */
    public function test_update_the_confirmation_password_of_another_with_a_differents_password_and_confirmation_password()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_password' => 'password',
            'user_confirmation_password' => 'password1',
            'user_id' => $admin->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_confirmation_password' => 'These passwords are differents'
        ]);
    }

    /**
     * Test Conception Number: 109
     * Update the password of another with a non string value
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: 1234567890
     * Confirm password: /
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "Your password must be of type string"
     * @returns void
     */
    public function test_update_the_password_of_another_with_a_non_string_value()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_password' => 1234567890,
            'user_confirmPassword' => 1234567890,
            'user_id' => $admin->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_password' => 'Your password must be of type string'
        ]);
    }

    /**
     * Test Conception Number: 110
     * Update the confirmation password of another with a non string value
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: "password"
     * Confirm password: 1234567890
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "Your password must be of type string"
     * @returns void
     */
    public function test_update_the_confirmation_password_of_another_with_a_non_string_value()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_password' => "password",
            'user_confirmation_password' => 1234567890,
            'user_id' => $admin->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_confirmation_password' => 'Your password must be of type string'
        ]);
    }

    /**
     * Test Conception Number: 111
     * Update correctly another User
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: "password1"
     * Confirm password: "password1"
     * Initials: "T1"
     * Expected result: The user is correctly saved and updated in the database
     * @returns void
     */
    public function test_update_correctly_another_user()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'admin')->count() == 0) {
            $this->add_an_admin_user();
        }
        $admin = User::all()->where('user_pseudo', '==', 'admin')->first();
        $response = $this->post('/user/update/infos/'.$user->id, [
            'user_password' => 'password1',
            'user_confirmation_password' => 'password1',
            'user_initials' => 'T1',
            'user_id' => $admin->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'user_firstname' => 'three',
            'user_lastname' => 'three',
            'user_pseudo' => 'three',
            'user_initials' => 'T1'
        ]);
        Hash::check("password1", User::all()->where('user_pseudo', '==', 'three')->first()->user_password);
    }

    /**
     * Test Conception Number: 112
     * Update my account with a too short first name
     * First name: "a"
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 2 characters"
     * @returns void
     */
    public function test_update_my_account_with_a_too_short_first_name()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_firstName' => 'a',
            'user_id' => $user->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_firstName' => 'You must enter at least 2 characters'
        ]);
    }

    /**
     * Test Conception Number: 113
     * Update my account with a too long first name
     * First name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_update_my_account_with_a_too_long_first_name()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_firstName' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non',
            'user_id' => $user->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_firstName' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 114
     * Update my account with a too short last name
     * First name: /
     * Last name: "a"
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 2 characters"
     * @returns void
     */
    public function test_update_my_account_with_a_too_short_last_name()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_lastName' => 'a',
            'user_id' => $user->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_lastName' => 'You must enter at least 2 characters'
        ]);
    }

    /**
     * Test Conception Number: 115
     * Update my account with a too long last name
     * First name: /
     * Last name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Pseudo: /
     * Password: /
     * Confirm password: /
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_update_my_account_with_a_too_long_last_name()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_lastName' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non',
            'user_id' => $user->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_lastName' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 116
     * Update my account with a too short password
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: "pass"
     * Confirm password: /
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 8 characters"
     * @returns void
     */
    public function test_update_my_account_with_a_too_short_password()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_password' => 'pass',
            'user_id' => $user->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_password' => 'You must enter at least 8 characters'
        ]);
    }

    /**
     * Test Conception Number: 117
     * Update my account with a too short confirmation password
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: /
     * Confirm password: "pass"
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 8 characters"
     * @returns void
     */
    public function test_update_my_account_with_a_too_short_confirmation_password()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_confirmation_password' => 'pass',
            'user_id' => $user->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_confirmation_password' => 'You must enter at least 8 characters'
        ]);
    }

    /**
     * Test Conception Number: 118
     * Update my account with a different password and confirmation password
     * First name: /
     * Last name: /
     * Pseudo: /
     * Password: "password"
     * Confirm password: "password1"
     * Initials: /
     * Expected result: Receiving an error:
     *                                     "These passwords are differents"
     * @returns void
     */
    public function test_update_my_account_with_a_different_password_and_confirmation_password()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_password' => 'password',
            'user_confirmation_password' => 'password1',
            'user_id' => $user->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_confirmation_password' => 'These passwords are differents'
        ]);
    }

    /**
     * Test Conception Number: 119
     * Update my account with a too short pseudo
     * First name: /
     * Last name: /
     * Pseudo: "a"
     * Password: /
     * Confirm password: /
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 2 characters"
     * @returns void
     */
    public function test_update_my_account_with_a_too_short_pseudo()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_pseudo' => 'a',
            'user_id' => $user->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_pseudo' => 'You must enter at least 2 characters'
        ]);
    }

    /**
     * Test Conception Number: 120
     * Update my account with a too long pseudo
     * First name: /
     * Last name: /
     * Pseudo: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Password: /
     * Confirm password: /
     * Initials: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_update_my_account_with_a_too_long_pseudo()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_pseudo' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non',
            'user_id' => $user->id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'user_pseudo' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 121
     * Update my account with an already used pseudo
     * First name: /
     * Last name: /
     * Pseudo: "other"
     * Password: /
     * Confirm password: /
     * Initials: /
     * Expected result: Receiving an error:
     *                                     "This username is already used"
     * @returns void
     */
    public function test_update_my_account_with_an_already_used_pseudo()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        if (User::all()->where('user_pseudo', '==', 'other')->count() == 0) {
            $countUsers = User::all()->count();
            $response = $this->post('register', [
                'user_firstName' => 'other',
                'user_lastName' => 'other',
                'user_pseudo' => 'other',
                'user_password' => 'password',
                'user_confirmation_password' => 'password'
            ]);
            $response->assertStatus(200);
            $this->assertEquals($countUsers + 1, User::all()->count());
            $this->assertDatabaseHas('users', [
                'user_firstName' => 'other',
                'user_lastName' => 'other',
                'user_pseudo' => 'other'
            ]);
            $this->assertTrue(Hash::check('password', User::all()->where('user_pseudo', '==', 'other')->first()->password));
        }
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_pseudo' => 'other',
            'user_id' => $user->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_pseudo' => 'This username is already used'
        ]);
    }

    /**
     * Test Conception Number: 122
     * Update my account with a formation (EQ) date before a previous one
     * First EQ formation date: 01/01/2023
     * Second EQ formation date: 01/01/2022
     * Expected result: Receiving an error:
     *                                      "You have to entered a formation equipment date that is after the previous formation equipment date"
     * @returns void
     */
    public function test_update_my_account_with_a_formation_EQ_date_before_a_previous_one()
    {
        $this->test_update_the_eq_formation_date_of_another_with_a_correct_date();
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_formationEqDate' => Carbon::now()->subYear(),
            'user_id' => $user->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_formationEqDate' => 'You have to entered a formation equipment date that is after the previous formation equipment date'
        ]);
    }

    /**
     * Test Conception Number: 123
     * Update my account with a formation (MME) date before a previous one
     * First MME formation date: 01/01/2023
     * Second MME formation date: 01/01/2022
     * Expected result: Receiving an error:
     *                                      "You have to entered a formation mme date that is after the previous formation mme date"
     * @returns void
     */
    public function test_update_my_account_with_a_formation_MME_date_before_a_previous_one()
    {
        $this->test_update_the_mme_formation_date_of_another_with_a_correct_date();
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_formationMmeDate' => Carbon::now()->subYear(),
            'user_id' => $user->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'user_formationMmeDate' => 'You have to entered a formation mme date that is after the previous formation mme date'
        ]);
    }

    /**
     * Test Conception Number: 124
     * Update my account with a correct data
     * First name: "threeMe"
     * Last name: "threeMe"
     * Pseudo: "threeMe"
     * Password: "password1"
     * Confirmation password: "password1"
     * Initials: /
     * EQ formation date: Carbon::now()
     * MME formation date: Carbon::now()
     * Expected result: The User is correctly saved and correctly updated in the database
     * @returns void
     */
    public function test_update_my_account_with_correct_data()
    {
        if (User::all()->where('user_pseudo', '==', 'three')->count() == 0) {
            $this->test_add_a_user_with_correct_data();
        }
        $user = User::all()->where('user_pseudo', '==', 'three')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_firstName' => 'threeMe',
            'user_lastName' => 'threeMe',
            'user_pseudo' => 'threeMe',
            'user_password' => 'password1',
            'user_confirmation_password' => 'password1',
            'user_formationEqDate' => Carbon::now(),
            'user_formationMmeDate' => Carbon::now(),
            'user_id' => $user->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'user_firstName' => 'threeMe',
            'user_lastName' => 'threeMe',
            'user_pseudo' => 'threeMe',
            'user_formationEqDate' => Carbon::now()->format('Y-m-d'),
            'user_formationMmeDate' => Carbon::now()->format('Y-m-d')
        ]);
        $this->assertTrue(Hash::check('password1', User::all()->where('user_pseudo', '==', 'threeMe')->first()->password));
    }

    /**
     * Test Conception Number: 125
     * Consult if the equipment formation is still valid, with a date set at Carbon::now()->subYear()->subYear()
     * Expected result: False
     * @returns void
     */
    public function test_consult_if_the_equipment_formation_is_still_valid_false()
    {
        $countUsers = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'other0',
            'user_lastName' => 'other0',
            'user_pseudo' => 'other0',
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countUsers + 1, User::all()->count());
        $this->assertDatabaseHas('users', [
            'user_firstName' => 'other0',
            'user_lastName' => 'other0',
            'user_pseudo' => 'other0'
        ]);
        $this->assertTrue(Hash::check('password', User::all()->where('user_pseudo', '==', 'other0')->first()->password));

        $user = User::all()->where('user_pseudo', '==', 'other0')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_formationEqDate' => Carbon::now()->subYear()->subYear(),
            'user_id' => $user->id
        ]);
        $response->assertStatus(200);
        $response = $this->get('/user/get/formationEqOk/'.$user->id);
        $this->assertEquals('false', $response->content());
    }

    /**
     * Test Conception Number: 126
     * Consult if the equipment formation is still valid, with a date set at Carbon::now()
     * Expected result: True
     * @returns void
     */
    public function test_consult_if_the_equipment_formation_is_still_valid_true()
    {
        $countUsers = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'other1',
            'user_lastName' => 'other1',
            'user_pseudo' => 'other1',
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countUsers + 1, User::all()->count());
        $this->assertDatabaseHas('users', [
            'user_firstName' => 'other1',
            'user_lastName' => 'other1',
            'user_pseudo' => 'other1'
        ]);
        $this->assertTrue(Hash::check('password', User::all()->where('user_pseudo', '==', 'other1')->first()->password));
        $user = User::all()->where('user_pseudo', '==', 'other1')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_formationEqDate' => Carbon::now(),
            'user_id' => $user->id
        ]);
        $response->assertStatus(200);
        $response = $this->get('/user/get/formationEqOk/'.$user->id);
        $this->assertEquals('true', $response->content());
    }

    /**
     * Test Conception Number: 127
     * Consult if the equipment formation is still valid, without date
     * Expected result: False
     * @returns void
     */
    public function test_consult_if_the_equipment_formation_is_still_valid_without_date()
    {
        $countUsers = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'other2',
            'user_lastName' => 'other2',
            'user_pseudo' => 'other2',
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countUsers + 1, User::all()->count());
        $this->assertDatabaseHas('users', [
            'user_firstName' => 'other2',
            'user_lastName' => 'other2',
            'user_pseudo' => 'other2'
        ]);
        $this->assertTrue(Hash::check('password', User::all()->where('user_pseudo', '==', 'other2')->first()->password));
        $user = User::all()->where('user_pseudo', '==', 'other2')->first();
        $response = $this->get('/user/get/formationEqOk/'.$user->id);
        $this->assertEquals('false', $response->content());
    }

    /**
     * Test Conception Number: 128
     * Consult if the mme formation is still valid, with a date set at Carbon::now()->subYear()->subYear()
     * Expected result: False
     * @returns void
     */
    public function test_consult_if_the_mme_formation_is_still_valid_false()
    {
        $countUsers = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'other3',
            'user_lastName' => 'other3',
            'user_pseudo' => 'other3',
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countUsers + 1, User::all()->count());
        $this->assertDatabaseHas('users', [
            'user_firstName' => 'other3',
            'user_lastName' => 'other3',
            'user_pseudo' => 'other3'
        ]);
        $this->assertTrue(Hash::check('password', User::all()->where('user_pseudo', '==', 'other3')->first()->password));
        $user = User::all()->where('user_pseudo', '==', 'other3')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_formationMmeDate' => Carbon::now()->subYear()->subYear(),
            'user_id' => $user->id
        ]);
        $response->assertStatus(200);
        $response = $this->get('/user/get/formationMmeOk/'.$user->id);
        $this->assertEquals('false', $response->content());
    }

    /**
     * Test Conception Number: 129
     * Consult if the mme formation is still valid, with a date set at Carbon::now()
     * Expected result: True
     * @returns void
     */
    public function test_consult_if_the_mme_formation_is_still_valid_true()
    {
        $countUsers = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'other4',
            'user_lastName' => 'other4',
            'user_pseudo' => 'other4',
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countUsers + 1, User::all()->count());
        $this->assertDatabaseHas('users', [
            'user_firstName' => 'other4',
            'user_lastName' => 'other4',
            'user_pseudo' => 'other4'
        ]);
        $this->assertTrue(Hash::check('password', User::all()->where('user_pseudo', '==', 'other4')->first()->password));
        $user = User::all()->where('user_pseudo', '==', 'other4')->first();
        $response = $this->post('/user/update/myAccount/'.$user->id, [
            'user_formationMmeDate' => Carbon::now(),
            'user_id' => $user->id
        ]);
        $response->assertStatus(200);
        $response = $this->get('/user/get/formationMmeOk/'.$user->id);
        $this->assertEquals('true', $response->content());
    }

    /**
     * Test Conception Number: 130
     * Consult if the mme formation is still valid, without date
     * Expected result: False
     * @returns void
     */
    public function test_consult_if_the_mme_formation_is_still_valid_without_date()
    {
        $countUsers = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'other5',
            'user_lastName' => 'other5',
            'user_pseudo' => 'other5',
            'user_password' => 'password',
            'user_confirmation_password' => 'password'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countUsers + 1, User::all()->count());
        $this->assertDatabaseHas('users', [
            'user_firstName' => 'other5',
            'user_lastName' => 'other5',
            'user_pseudo' => 'other5'
        ]);
        $this->assertTrue(Hash::check('password', User::all()->where('user_pseudo', '==', 'other5')->first()->password));
        $user = User::all()->where('user_pseudo', '==', 'other5')->first();
        $response = $this->get('/user/get/formationMmeOk/'.$user->id);
        $this->assertEquals('false', $response->content());
    }

    /**
     * Test Conception Number: 131
     * Consul the User list (need be run alone)
     * Expected result: The correct list
     * @returns void
     */
    public function test_consult_the_user_list()
    {
        $this->test_add_a_user_with_correct_data();
        $response = $this->get('/users/send');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'user_firstName',
                'user_lastName',
                'user_initials',
                'user_signaturePath',
                'user_pseudo',
                'user_password',
                'user_menuUserAcessRight',
                'user_resetUserPasswordRight',
                'user_updateDataInDraftRight',
                'user_validateDescriptiveLifeSheetDataRight',
                'user_validateOtherDataRight',
                'user_updateDataValidatedButNotSignedRight',
                'user_updateDescriptiveLifeSheetDataSignedRight',
                'user_makeQualityValidationRight',
                'user_makeTechnicalValidationRight',
                'user_makeEqOpValidationRight',
                'user_updateEnumRight',
                'user_deleteEnumRight',
                'user_addEnumRight',
                'user_deleteDataNotValidatedLinkedToEqOrMmeRight',
                'user_deleteDataValidatedLinkedToEqOrMmeRight',
                'user_deleteDataSignedLinkedToEqOrMmeRight',
                'user_deleteEqOrMmeRight',
                'user_updateInformationRight',
                'user_personTrainedToGeneralPrinciplesOfEqManagementRight',
                'user_formationEqDate',
                'user_personTrainedToGeneralPrinciplesOfMMEManagementRight',
                'user_formationMmeDate',
                'user_makeEqRespValidationRight',
                'user_makeReformRight',
                'user_declareNewStateRight',
                'user_makeMmeOpValidationRight',
                'user_makeMmeRespValidationRight'
            ]
        ]);
    }

}
