<?php


namespace App\Controller;

use App\Entity\Lang;
use App\Entity\SystemSettings;
use App\Tools\BookContentLoader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppController extends AbstractController
{
    /** @var EntityManagerInterface  */
    public $entityManager;
    /** @var TranslatorInterface */
    public $translator;
    /** @var RequestStack  */
    public $requestStack;
    /** @var HttpFoundation\Request|null  */
    public $request;
    /** @var string */
    public $systemDefaultLanguageCode;
    /** @var SettingsController */
    public $systemSettings;
    /** @var Lang */
    public $systemDefaultLanguage;
    /** @var string */
    public $siteLocale;
    /** @var string */
    public $requestedLocale;
    /** @var BookContentLoader */
    public $bookContentLoader;
    /** @var array  */
    protected $queryParameters;

    protected $parameterBag;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator, RequestStack $requestStack, ParameterBagInterface $parameterBag)
    {

        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->parameterBag = $parameterBag;
        $this->request = $this->requestStack->getCurrentRequest();
        $this->systemSettings = new SettingsController($entityManager, $parameterBag);
        $this->queryParameters = array_keys($this->request->query->all());
    }

    /**
     * @return $this
     */
    public function initialize()
    {
        $this->systemDefaultLanguage = $this->systemSettings->getDefaultLanguage();
        $this->systemDefaultLanguageCode = $this->systemDefaultLanguage->getIsoCode();
        $this->siteLocale = $this->getParameter('locale');
        $this->bookContentLoader = new BookContentLoader($this->entityManager);

        return $this;
    }
}
