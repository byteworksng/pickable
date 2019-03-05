<?php

namespace App;

use App\Traits\InfusionsoftTrait;
use App\Traits\TagTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Module extends Model
{

    use TagTrait, InfusionsoftTrait;

    public function assignModule(Contact $client)
    {

        $registeredCourses = explode(',', $client->products);

        // fetch next courses
        $nextCourse = $this->nextCourse($registeredCourses, $client->email);

        $tag = $this->makeTag($nextCourse);
        $tagId = $this->fetchTagId($tag);

        return [
            'success' => $this->addTag($client['Id'], $tagId),
            'tag'     => $tag,
            'tagId'   => $tagId,
        ];
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
        return self::where('course_key', strtolower($course))
                   ->pluck('name');
    }

    private function nextCourse(array $registeredCourses, string $email): string
    {
        $course = '';

        // we assume fifo
        if (count($registeredCourses) > 0) {
            $activeCourse = array_shift($registeredCourses);
            $course = $this->processCourse(
                $this->loadCourseModules($activeCourse),
                $this->fetchCompletedModules($email)
            );

            !empty($course) ?: $course = $this->nextCourse($registeredCourses, $email);

        }


        return $course;
    }

    private function processCourse(
        Collection $courseModules,
        Collection $completedModules
    ) {
        $nextCourse = false;

        // assumption is latest is last record inserted
        $last = $completedModules->last();
        $remainingCourse = $courseModules->diff($completedModules);

        $key = $courseModules->search($last);


        if (false !== $key && $remainingCourse->has($key + 1)) {
            // lets switch to the next module
            $nextCourse = $remainingCourse[$key + 1];
        }

        if (false === $key && count($remainingCourse) > 0) {
            // lets switch to the next course and first module
            $nextCourse = $remainingCourse[0];
        }

        return $nextCourse;
    }

}
