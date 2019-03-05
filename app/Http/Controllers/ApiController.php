<?php

namespace App\Http\Controllers;

use App\Http\Helpers\InfusionsoftHelper;
use App\Http\Requests\AssignModuleReminderRequest;
use App\Module;
use App\Traits\InfusionsoftTrait;
use App\Traits\TagTrait;
use App\User;
use Illuminate\Support\Facades\Response;


class ApiController extends Controller
{

    use InfusionsoftTrait, TagTrait;


    public function moduleReminderAssigner(AssignModuleReminderRequest $request)
    {
        // lets validate
        $user = $request->validated();

        $email = $user['contact_email'];
        $client = $this->getContactData($email);
        $message = 'Failed to assign reminder';
        $status = 422;
        $response = false;

        if ($client) {
            $response = (new Module())->assignModule($client);
            if ($this->validateTagResponse($response, $email)) {
                // lets validate if the tag already exist
                $response = true;
                $message = $response['tag'] . ' assigned successfully';
//                                $message = 'Reminder assigned successfully';
                $status = 201;
            }
        }


        return Response::json(compact('response', 'message'), $status);

    }


    private function exampleCustomer()
    {

        $infusionsoft = new InfusionsoftHelper();

        $uniqid = uniqid();

        $infusionsoft->createContact([
            'Email'     => $uniqid . '@test.com',
            "_Products" => 'ipa,iea',
        ]);

        $user = User::create([
            'name'     => 'Test ' . $uniqid,
            'email'    => $uniqid . '@test.com',
            'password' => bcrypt($uniqid),
        ]);

        // attach IPA M1-3 & M5
        $user->completed_modules()->attach(Module::where('course_key', 'ipa')->limit(3)->get());
        $user->completed_modules()->attach(Module::where('name', 'IPA Module 5')->first());


        return $user;
    }
}
