<?php


namespace App\Controller\Admin;

use App\Entity\Lang;

class LangAdminController extends AppAdminController
{
    /**
     * @param Lang $entity
     */
    public function updateEntity($entity)
    {
        $this->entityManager->flush();
    }
}
