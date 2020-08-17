<?php


namespace App\Controller\Admin;


use App\Entity\BookGroup;
use App\Entity\BookGroupTranslation;
use App\Entity\Lang;
use App\Form\BookGroupTranslationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookGroupAdminController extends AppAdminController
{
    public function renderBookGroupTitleForm(int $bookGroupId, string $langCode, Request $request)
    {
        /** @var  BookGroup $bookGroup */
        $bookGroup = $this->getEntityRepository(BookGroup::class)->findOneBy(['id' => $bookGroupId]);
        /** @var Lang $lang */
        $lang = $this->getEntityRepository(Lang::class)->getLangIdByCode($langCode, false);
        /** @var BookGroupTranslation $bookGroupTranslation */
        $bookGroupTranslation = $this->getEntityRepository(BookGroupTranslation::class)->findOneBy([
            'bookGroup' => $bookGroup->getId(),
            'lang' => $lang->getId()
        ]);

        if (!$bookGroupTranslation) {
            $formAction = $this->generateUrl('create_book_title_translation', [
                'langCode' => $langCode,
                'bookGroupId' => $bookGroupId,
            ]);
        } else {
            $formAction = $this->generateUrl('update_book_title_translation', [
                'bookGroupTranslation' => $bookGroupTranslation->getId(),
                'langCode' => $langCode,
                'bookGroupId' => $bookGroupId
            ]);
        }

        $form = $this->createForm(BookGroupTranslationType::class, $bookGroupTranslation, [
            'action' => $formAction,
        ])->handleRequest($request);

        return $this->render('admin/partials/book_group_title_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(
     *     path="/admin/update-book-title-translation/{bookGroupTranslation}",
     *     name="update_book_title_translation",
     *     methods={"POST"}
     * )
     * @param BookGroupTranslation $bookGroupTranslation
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateBookTitleTranslation(BookGroupTranslation $bookGroupTranslation, Request $request)
    {
        $form = $this->createForm(BookGroupTranslationType::class, $bookGroupTranslation)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Перевод успешно сохранен');

            return $this->redirectToRoute('easyadmin', [
                'entity' => 'BookGroup',
                'action' => 'edit',
                'id' => $bookGroupTranslation->getBookGroup()->getId(),
                'code' => $request->query->get('langCode')
            ]);
        }

        $this->addFlash('error', 'Ошибка при сохранении перевода');

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'BookGroup',
            'action' => 'edit',
            'id' => $bookGroupTranslation->getBookGroup()->getId(),
            'code' => $request->query->get('langCode')
        ]);

    }

    /**
     * @Route(
     *     path="/admin/create-book-title-translation",
     *     name="create_book_title_translation",
     *     methods={"POST"}
     * )
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createBookTranslation(Request $request)
    {
        $bookGroup = $this->entityManager->getRepository(BookGroup::class)->findOneBy(['id' => $request->query->get('bookGroupId')]);
        $lang = $this->entityManager->getRepository(Lang::class)->getLangIdByCode($request->query->get('langCode'));
        $form = $this->createForm(BookGroupTranslationType::class, null)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookGroupTitle = $form->getData();
            $bookGroupTitle->setBookGroup($bookGroup)->setLang($lang);
            $this->entityManager->persist($bookGroupTitle);
            $this->entityManager->flush();

            $this->addFlash('success', 'Перевод успешно сохранен');

            return $this->redirectToRoute('easyadmin', [
                'entity' => 'BookGroup',
                'action' => 'edit',
                'id' => $bookGroup->getId(),
                'code' => $request->query->get('langCode')
            ]);
        }

        $this->addFlash('error', 'Ошибка при сохранении перевода');

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'BookGroup',
            'action' => 'edit',
            'id' => $bookGroup->getId(),
            'code' => $request->query->get('langCode')
        ]);
    }
}
