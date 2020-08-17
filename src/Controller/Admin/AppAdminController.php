<?php


namespace App\Controller\Admin;


use App\Entity\Lang;
use App\Entity\MainPageLabels;
use App\Entity\SystemSettings;
use App\Form\MainPageLabelsType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\Request;

class AppAdminController extends EasyAdminController
{
    /** @var EntityManagerInterface  */
    public $entityManager;
    /** @var SystemSettings */
    public $defaultLangSettingsParameter;
    /** @var Lang */
    public $defaultLang;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->init();
    }

    public function init()
    {
        $this->defaultLangSettingsParameter = $this->entityManager->getRepository(SystemSettings::class)->findOneBy([
            'name' => 'default_lang'
        ]);
        $this->defaultLang = $this->entityManager->getRepository(Lang::class)->getLangIdByCode($this->defaultLangSettingsParameter->getValue());

        return $this;
    }

    public function getEntityRepository(string $entityFQN)
    {
        return $this->entityManager->getRepository($entityFQN);
    }
}
