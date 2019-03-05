<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModuleReminderAssignerTest extends TestCase
{
    /**
     * A basic test using registered user.
     *
     * @return void
     */
    public function testAssignReminderForInfusionsoftContact()
    {

        $response = $this->post('/api/module_reminder_assigner', ['contact_email' => '5c7d32e41490e@test.com']);
        $response->assertStatus(201);
        $response->assertJson([
            'message'=> 'Reminder assigned successfully',
            'response' => true
        ]);
    }

    /**
     * A basic test using unregistered user.
     *
     * @return void
     */
    public function testAssignReminderForNonInfusionsoftContact()
    {
        $response = $this->post('/api/module_reminder_assigner', ['contact_email' => 'chitest@test.com']);
        $response->assertStatus(422);
        $response->assertJson([
            'message'=> 'Failed to assign reminder',
            'response' =>false
        ]);
    }
}
