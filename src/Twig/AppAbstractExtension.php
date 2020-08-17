<?php


namespace App\Twig;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\Environment;

class AppAbstractExtension extends AbstractExtension
{
    /** @var EntityManagerInterface  */
    public $entityManager;
    /** @var Environment  */
    public $twigEnvironment;
    /** @var FormInterface  */
    public $form;
    public $request;

    public function __construct(EntityManagerInterface $entityManager, Environment $twigEnvironment, RequestStack $request)
    {
        $this->entityManager = $entityManager;
        $this->twigEnvironment = $twigEnvironment;
        $this->request = $request->getCurrentRequest();
    }
}
