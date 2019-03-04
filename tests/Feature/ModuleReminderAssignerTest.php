<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModuleReminderAssignerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testReminderAssigner()
    {
        $response = $this->post('/api/module_reminder_assigner', ['contact_email' => 'chitest@test.com']);
        $response->assertStatus(201);
    }
}
