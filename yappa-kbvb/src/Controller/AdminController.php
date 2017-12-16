<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 08/12/2017
 * Time: 16:35
 */

namespace App\Controller;

use App\Entity\Items;

use App\Form\ItemDeleteType;
use App\Service\ChartData;
use App\Service\ItemService;
use App\Form\ItemType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class AdminController extends Controller
{
    private $succes;
    /**
     * @Route("/admin",name="admin")
     */
    public function index(ChartData $chartData){
        $templatesNeeded = array('Admin/Charts/users-not-selected-item.html.twig',
                                'Admin/Charts/members-entered-last-days.html.twig');

        return $this->render('Layouts/admin_layout.html.twig',array(
            'templateNames' => $templatesNeeded,
            'dataChartNotSelected' => $chartData->getAmountEnteredVSnotEntered(),
            'dataForChartEntries' => $chartData->getDailyEntriesLastDays(30)
        ));
    }

    /**
     * @Route("/admin/items/{success}", name="itemCRUD",defaults={"success" = null})
     */
    public function itemAdjustments(Request $request,ItemService $itemService,$success){

        $formDelete = $this->createForm(ItemDeleteType::class,new Items());

        $formAddNewItem= $this->createForm(ItemType::class,new Items());

        $formDelete->handleRequest($request);
        $formAddNewItem->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            $data = $formDelete->getData();
            $itemService->deleteItem($data->getId());
        }

        if ($formAddNewItem->isSubmitted() && $formAddNewItem->isValid()) {
            $itemService->addItem($formAddNewItem->getData());
        }

        $items = $itemService->getItems();

        return $this->render('Layouts/admin_layout.html.twig',array(
            'templateNames' => 'item-crud',
            'items'=>$items,
            'successMessage' => $success,
            'formDelete'=>$formDelete->createView(),
            'formAddNewItem'=>$formAddNewItem->createView()
        ));
    }

    /**
     * @Route("/admin/edit/item/{id}",name="editItem")
     */
    public function editItem(Request $request,ItemService $itemService,$id = null){
        if($id === null){
            throw new NotFoundHttpException("The item was not found");
        }else{
            $this->succes = null;
            $item = $itemService->getItem($id);

            $form = $this->createForm(ItemType::class,$item);

            $formAddNewItem= $this->createForm(ItemType::class,new Items());

            $formDelete = $this->createForm(ItemDeleteType::class,$item);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $successMessage= $itemService->editItem($form->getData());

                return $this->redirectToRoute('itemCRUD',array('success' => $successMessage));
            }

            $formDelete->handleRequest($request);

            if ($formDelete->isSubmitted() && $formDelete->isValid()) {
                $data = $formDelete->getData();
                $itemService->deleteItem($data->getId());
            }

            $formAddNewItem->handleRequest($request);

            if ($formAddNewItem->isSubmitted() && $formAddNewItem->isValid()) {
                $itemService->addItem($formAddNewItem->getData());
            }

            $items = $itemService->getItems();

            return $this->render('Layouts/admin_layout.html.twig',array(
                'templateNames' => 'item-crud',
                'items'=>$items,
                'editItem'=>$id,
                'form'=>$form->createView(),
                'formDelete' => $formDelete->createView(),
                'formAddNewItem'=>$formAddNewItem->createView()
            ));
        }
    }
}