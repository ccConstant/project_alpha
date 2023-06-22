<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EqStateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Conception Number: 1
     * Try to add a new state as drafted with no values
     * Remarks: /
     * Start Date : /
     * End Date : /
     * Name : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a remark about the state"
     * @returns void
     */
    public function test_add_state_no_values()
    {
        $response = $this->post('/state/verif');
        $response->assertStatus(302);
        $response->assertInvalid([
            'state_remarks' => 'You must enter a remark about the state',
        ]);
    }

    /**
     * Test Conception Number: 2
     * Try to add a new state as drafted with too short remarks
     * Remarks: "in"
     * Start Date : /
     * End Date : /
     * Name : /
     * Expected Result: Receiving an error :
     *                                          "You must enter at least three characters"
     * @returns void
     */
    public function test_add_state_short_remarks()
    {
        $response = $this->post('/state/verif', [
            'state_remarks' => 'in',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'state_remarks' => 'You must enter at least three characters',
        ]);
    }

    /**
     * Test Conception Number: 3
     * Try to add a new state as drafted with too long remarks
     * Remarks: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Start Date : /
     * End Date : /
     * Name : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_state_long_remarks()
    {
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'state_remarks' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 4
     * Try to add a new state as drafted with only a correct remark
     * Remarks: "Remarks"
     * Start Date : /
     * End Date : /
     * Name : /
     * Expected Result: Receiving an error :
     *                                          "You must choose a name for your state"
     * @returns void
     */
    public function test_add_state_only_remarks()
    {
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You must choose a name for your state',
        ]);
    }

    /**
     * Test Conception Number: 4
     * Try to add a new state as drafted with no dates
     * Remarks: "Remarks"
     * Start Date : /
     * End Date : /
     * Name : "Name"
     * Expected Result: Receiving an error :
     *                                          "You must entered a start date for your state"
     * @returns void
     */
    public function test_add_state_no_dates()
    {
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Name',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_startDate' => 'You must entered a start date for your state',
        ]);
    }

    /**
     * Test Conception Number: 5
     * Try to add a new state as drafted with a too old start date
     * Remarks: "Remarks"
     * Start Date: Today - 2 Months
     * End Date: /
     * Name: "Name"
     * Expected Result: Receiving an error :
     *                                          "You can't enter a date that is older than one month"
     * @returns void
     */
    public function test_add_state_old_start_date()
    {
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Name',
            'state_startDate' => Carbon::now()->subMonths(2),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_startDate' => 'You can\'t enter a date that is older than one month',
        ]);
    }
}
