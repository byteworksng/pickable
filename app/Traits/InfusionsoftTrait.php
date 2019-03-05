<?php
/**
 * Created by IntelliJ IDEA.
 * User: chibuzorogbu
 * Date: 2019-03-05
 * Time: 14:52
 */

namespace App\Traits;


use App\Contact;
use App\Http\Helpers\InfusionsoftHelper;

trait InfusionsoftTrait
{

    /**
     * @param string $email
     *
     * @return Contact|null
     */
    public function getContactData(string $email): ?Contact
    {
        $infusionsoft = new InfusionsoftHelper();
        $contact = $infusionsoft->getContact($email);

        return false !== $contact ? new Contact($contact) : null;
    }

}