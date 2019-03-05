<?php
/**
 * Created by IntelliJ IDEA.
 * User: chibuzorogbu
 * Date: 2019-03-05
 * Time: 14:39
 */

namespace App\Traits;


use App\Http\Helpers\InfusionsoftHelper;
use App\Tag;
use App\TagCategory;

trait TagTrait
{

    public function validateTagResponse(array $response, string $email): bool
    {
        $isValid = false;

        if (true === $response['success']) {
            $isValid = true;
        }

        if (false === $response['success']) {
            $contactData = $this->getContactData($email);
            if (isset($contactData->groups)) {
                $isValid = false !== strpos($contactData->groups, (string)$response['tagId']);
            }
        }


        return $isValid;
    }

    public function fetchTagId($tag)
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

    public function fetchTags()
    {
        $infusionsoft = new InfusionsoftHelper();
        $tags = $infusionsoft->getAllTags();

        if ($tags){
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
        }

        return $tags;
    }

    public function makeTag($message = false)
    {
        return $message ? 'Start ' . $message . ' Reminders' : 'Module reminders completed';
    }

    public function addTag($cliendId, $tagId): bool
    {
        $infusionsoft = new InfusionsoftHelper();
        return $infusionsoft->addTag($cliendId, $tagId);
    }


}