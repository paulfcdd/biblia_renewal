<?php


namespace App\Controller;


use App\Entity\EntityInterface;
use App\Entity\Lang;
use App\Entity\SystemSettings;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SystemSettingsRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SettingsController
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var SystemSettingsRepository  */
    private $objectRepository;
    private $parameterBag;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->objectRepository = $entityManager->getRepository(SystemSettings::class);
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return Lang|object|null
     */
    public function getDefaultLanguage()
    {
        /** @var  SystemSettings $settings */
        $settings = $this->getByParameterName('default_lang');
        $isoCode = $settings ? $settings->getValue() : $this->parameterBag->get('locale');

        return $this->entityManager
            ->getRepository(Lang::class)
            ->getLangIdByCode($isoCode);
    }

    /**
     * @param string $parameterName
     * @return EntityInterface
     */
    private function getByParameterName(string $parameterName)
    {
        return $this->objectRepository->findOneByName($parameterName);
    }
}
