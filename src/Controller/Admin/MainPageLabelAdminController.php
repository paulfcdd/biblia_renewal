<?php


namespace App\Controller\Admin;


use App\Entity\Lang;
use App\Entity\MainPageLabels;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageLabelAdminController extends AppAdminController
{
//    /**
//     * @Route(
//     *     path="/admin/save-main-page-lagel-translation/{mainPageLabel}",
//     *     name="save_main_page_label_translation",
//     *     methods={"POST"}
//     * )
//     * @ParamConverter("mainPageLabel", class="App\Entity\MainPageLabels")
//     * @param Request $request
//     * @param MainPageLabels $mainPageLabel
//     * @return Response
//     * @throws \Exception
//     *
//     */
//    public function saveMainPageLabelTranslation(Request $request, MainPageLabels $mainPageLabel)
//    {
//        $requestPostData = $request->request;
//
//        $translatedValue = $requestPostData->get('translated_title');
//        $langId = $requestPostData->get('lang_id');
//
//        if ($langId == $mainPageLabel->getLang()->getId()) {
//            $mainPageLabel->setTitle(trim($translatedValue));
//
//            try{
//                $this->entityManager->flush();
//                $this->addFlash('success', 'Перевод значения ' . $mainPageLabel->getCode() . ' сохранен');
//                return Response::create();
//            } catch (\Exception $exception) {
//                throw new \Exception();
//            }
//        }
//
//        return Response::create('Перевод значения ' . $mainPageLabel->getCode() . ' сохранен', 500);
//
//
//    }
}
