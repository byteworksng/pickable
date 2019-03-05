<?php

namespace Tests\Feature;

use App\Http\Helpers\InfusionsoftHelper;
use App\Module;
use App\Traits\TagTrait;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModuleReminderAssignerTest extends TestCase
{
    use TagTrait;
    /**
     * A test for next module same course.
     * @vcr testAssignReminderNextModuleSameCourse
     * @return void
     */
    public function testAssignReminderNextModuleSameCourse()
    {

        $user = factory(User::class)->create();

        // attach IPA M1-3 & M5
        $user->completed_modules()->attach(Module::where('course_key', 'ipa')->limit(3)->get());
        $user->completed_modules()->attach(Module::where('name', 'IPA Module 5')->first());

        $expectedTag  = 'IPA Module 6';


        $response = $this->post('/api/module_reminder_assigner', ['contact_email' => $user->email]);
        $response->assertStatus(201);
        $response->assertJson([
            'message'=> $this->makeTag($expectedTag) . ' assigned successfully',
            'response' => true
        ]);
    }

    /**
     * A test for next module same course.
     * @vcr testAssignReminderNextCourseAfterLastModule
     * @return void
     */
    public function testAssignReminderNextCourseAfterLastModule()
    {

        $user = factory(User::class)->create();

        // attach IPA M1-3 & M5
        $user->completed_modules()->attach(Module::where('course_key', 'ipa')->limit(3)->get());
        $user->completed_modules()->attach(Module::where('name', 'IPA Module 7')->first());

        $expectedTag  = 'IEA Module 1';


        $response = $this->post('/api/module_reminder_assigner', ['contact_email' => $user->email]);
        $response->assertStatus(201);
        $response->assertJson([
            'message'=> $this->makeTag($expectedTag) . ' assigned successfully',
            'response' => true
        ]);
    }


    /**
     * A test for next module same course.
     * @vcr testAssignReminderLastCourseLastModule
     * @return void
     */
    public function testAssignReminderLastCourseLastModule()
    {

        $user = factory(User::class)->create();

        // attach IPA M1-3 & M5
        $user->completed_modules()->attach(Module::where('course_key', 'ipa')->limit(7)->get());
        $user->completed_modules()->attach(Module::where('course_key', 'iea')->limit(7)->get());


        $response = $this->post('/api/module_reminder_assigner', ['contact_email' => $user->email]);
        $response->assertStatus(201);
        $response->assertJson([
            'message'=> $this->makeTag() . ' assigned successfully',
            'response' => true
        ]);
    }

    /**
     * A basic test using unregistered user.
     * @vcr testAssignReminderForNonInfusionsoftContact
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

    /**
     * An email validation test.
     * @vcr testShouldFailEmailValidation
     * @return void
     */
    public function testShouldFailEmailValidation()
    {
        $response = $this->post('/api/module_reminder_assigner', ['contact_email' => ['email' =>'chitest@test.com']]);
        //expect redirect
        $response->assertStatus(302);
    }


}
