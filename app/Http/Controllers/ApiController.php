<?php

namespace App\Http\Controllers;

use App\Http\Helpers\InfusionsoftHelper;
use App\Http\Requests\AssignModuleReminderRequest;
use App\Module;
use App\Tag;
use App\TagCategory;
use App\User;
use Illuminate\Support\Facades\Response;


class ApiController extends Controller
{


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
            $reqisteredCourses = explode(',', $client['_Products']);

            // fetch next courses
            $nextCourse = $this->nextCourse(
                $this->loadCourseModules($reqisteredCourses[0]),
                $this->fetchCompletedModules($email)
            );

            $tag = $this->makeTag($nextCourse);

            $response = $this->addTag($client['Id'], $this->fetchTagId($tag));

            if (false !== $response) {
                $message = 'Reminder assigned successfully';
                $status = 201;
            }

        }


        return Response::json(compact('response', 'message'), $status);

    }


    private function fetchTagId($tag)
    {
        $tagSet = Tag::where('name', $tag)->first();
        $id = $tagSet ? $tagSet->id : false;

        if (!$id) {
            $tags = $this->fetchTags();
            $tag = array_filter($tags->all(), function ($val) use ($tag) {
                return $val['name'] === $tag;
            });

            $tagSet = array_values($tag);
            $id = $tagSet ? $tagSet[0]['id'] : false;
        }

        return $id;

    }

    private function fetchTags()
    {
        $infusionsoft = new InfusionsoftHelper();
        $tags = $infusionsoft->getAllTags();

        foreach ($tags->all() as $tag) {

            $savedTag = Tag::updateOrCreate(['id' => $tag->id], $tag->toArray());

            if ($tag['category']) {
                $tagId = $tag['category']['id'];
                $tagCategories = TagCategory::updateOrCreate(
                    ['id' => $tagId], $tag['category']
                );

                $savedTag->tags_category()
                         ->associate($tagCategories)
                         ->save();
            }

        }


        return $tags;
    }

    private function makeTag($message)
    {
        return $message ? 'Start ' . $message . ' Reminders' : 'Module reminders completed';
    }

    private function fetchCompletedModules($email)
    {
        $user = User::with('completed_modules')
                    ->where('email', $email)
                    ->first();

        return $user->completed_modules->pluck('name');
    }

    private function loadCourseModules($course)
    {
        return Module::where('course_key', strtolower($course))
                     ->pluck('name');
    }

    private function nextCourse(
        \Illuminate\Support\Collection $courseModules,
        \Illuminate\Support\Collection $completedModules
    ) {
        $nextCourse = false;

        // assumption is latest is last record inserted
        $last = $completedModules->last();
        $remainingCourse = $courseModules->diff($completedModules);
        $key = $courseModules->search($last);

        if (false !== $key && $remainingCourse->has($key + 1)) {
            $nextCourse = $remainingCourse[$key + 1];
        }

        return $nextCourse;
    }

    private function getContactData(string $email): ?array
    {
        $infusionsoft = new InfusionsoftHelper();
        $contact = $infusionsoft->getContact($email);

        return false !== $contact ? $contact : null;
    }

    private function addTag($cliendId, $tagId): bool
    {
        $infusionsoft = new InfusionsoftHelper();
        return $infusionsoft->addTag($cliendId, $tagId);
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
