<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
<<<<<<< HEAD
    public function test_example()
=======
    public function test_the_application_returns_a_successful_response()
>>>>>>> 7de7b0ce9cff7518d489a46daf1c8af73a284dd5
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
