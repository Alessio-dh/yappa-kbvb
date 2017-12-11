<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 08/12/2017
 * Time: 16:35
 */

namespace App\Controller;

use App\Entity\Items;
use App\Entity\MemberEntry;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
    public function index(){
        $templatesNeeded = array('Admin/Charts/users-not-selected-item.html.twig',
                                'Admin/Charts/members-entered-last-days.html.twig');
        $totalEnters = $this->getAmountOfEnters();
        $amountNotSelected = $this->getNotSelectedItemUsers();

        $amountSelected = $totalEnters - $amountNotSelected;

        $dataForChartNotSelected = ['labels'=>array('Selected an item','Did not select an item'),
                                    'data' => array($amountSelected,$amountNotSelected)];

        $dataForChartEntries = $this->getDailyEntriesLastDays(30);

        return $this->render('Layouts/admin_layout.html.twig',array(
            'templateNames' => $templatesNeeded,
            'dataChartNotSelected' => $dataForChartNotSelected,
            'dataForChartEntries' => $dataForChartEntries
        ));
    }

    /**
     * @Route("/admin/items/{success}", name="itemCRUD",defaults={"success" = null})
     */
    public function itemAdjustments(Request $request,$success){
        $items = $this->getItems();

        $formDelete = $this->createFormBuilder(null)
            ->add('id', TextType::class, array(
                'attr'=>array(
                    'class'  => 'form-control idOfItem',
                    'hidden'=>true
                ),
                'label'=>false,
                'required' => true
            ))
            ->add('delete', SubmitType::class, array('label' => 'Delete','attr'=>array('class'=>'btn btn-danger')))
            ->getForm();

        $formAddNewItem= $this->createFormBuilder(null)
            ->add('description', TextType::class, array(
                'attr'=>array(
                    'class'  => 'form-control',
                    'placeholder' => "Fill in the description"
                ),
                'required' => true
            ))
            ->add('image', FileType::class, array(
                'attr'=>array(
                    'class'  => 'form-control'
                ),
                'multiple'=>false,
                'required' => true
            ))
            ->add('active', CheckboxType::class, array(
                'attr'=>array(
                    'class'  => 'form-control',
                    'checked' => true
                ),
                'required' => true
            ))
            ->add('add', SubmitType::class, array('label' => 'Add','attr'=>array('class'=>'btn btn-success form-control')))
            ->getForm();

        $formDelete->handleRequest($request);
        $formAddNewItem->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $data = $formDelete->getData();
            $item = $this->getItem($data['id']);
            $em->remove($item);
            $em->flush();
            $items = $this->getItems();
        }

        if ($formAddNewItem->isSubmitted() && $formAddNewItem->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $item = $formAddNewItem->getData();

            $file = $item['image'];
            $dateNow = new \DateTime();
            $dateNow = $dateNow->format("YmdHis");
            $newFileName = rand(1,9999).$dateNow.rand(1,9999).'.'.$file->guessExtension();

            $file->move(
                $this->getParameter('item_image_directory'),
                $newFileName
            );

            $newItem = new Items();

            $newItem->setDescription($item['description']);
            $newItem->setImagelink($newFileName);
            $newItem->setActive($item['active']);

            $em->persist($newItem);
            $em->flush();
            $items = $this->getItems();
        }

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
    public function editItem(Request $request,$id = null){
        if($id === null){
            throw new NotFoundHttpException("The item was not found");
        }else{
            $this->succes = null;
            $item = $this->getItem($id);

            $form = $this->createFormBuilder(null)
                ->add('description', TextType::class, array(
                    'attr'=>array(
                        'class'  => 'form-control',
                        'value' => $item->getDescription()
                    ),
                    'required' => false
                ))
                ->add('image', FileType::class, array(
                    'attr'=>array(
                        'class'  => 'form-control'
                    ),
                    'multiple'=>false,
                    'required' => false
                ))
                ->add('active', CheckboxType::class, array(
                    'attr'=>array(
                        'class'  => 'form-control',
                        'checked' => $item->getActive() == '1'? true : false,
                    ),
                    'required' => false
                ))
                ->add('save', SubmitType::class, array('label' => 'Save','attr'=>array('class'=>'btn btn-lg btn-success')))
                ->getForm();

            $formAddNewItem= $this->createFormBuilder(null)
                ->add('description', TextType::class, array(
                    'attr'=>array(
                        'class'  => 'form-control',
                        'placeholder' => "Fill in the description"
                    ),
                    'required' => true
                ))
                ->add('image', FileType::class, array(
                    'attr'=>array(
                        'class'  => 'form-control'
                    ),
                    'multiple'=>false,
                    'required' => true
                ))
                ->add('active', CheckboxType::class, array(
                    'attr'=>array(
                        'class'  => 'form-control',
                        'checked' => true
                    ),
                    'required' => true
                ))
                ->add('add', SubmitType::class, array('label' => 'Add','attr'=>array('class'=>'btn btn-success form-control')))
                ->getForm();

            $formDelete = $this->createFormBuilder(null)
                ->add('id', TextType::class, array(
                    'attr'=>array(
                        'class'  => 'form-control idOfItem',
                        'hidden'=>true
                    ),
                    'label'=>false,
                    'required' => true
                ))
                ->add('delete', SubmitType::class, array('label' => 'Delete','attr'=>array('class'=>'btn btn-danger')))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $itemChanged = $form->getData();
                $em = $this->getDoctrine()->getManager();
                try{
                    if($itemChanged['image'] ===null){

                        $item->setDescription($itemChanged['description']);
                        $item->setImagelink($item->getImageLink());
                        $item->setActive($itemChanged['active']);

                        $em->persist($item);
                        $em->flush();

                    }else{
                        $file = $itemChanged['image'];
                        $dateNow = new \DateTime();
                        $dateNow = $dateNow->format("YmdHis");
                        $newFileName = rand(1,9999).$dateNow.rand(1,9999).'.'.$file->guessExtension();

                        $file->move(
                            $this->getParameter('item_image_directory'),
                            $newFileName
                        );

                        // Can unlink old file here but not needed because we will not be working with that many items

                        $item->setDescription($itemChanged['description']);
                        $item->setImagelink($newFileName);
                        $item->setActive($itemChanged['active']);

                        $em->persist($item);
                        $em->flush();
                    }
                    $items = $this->getItems();
                    $successMessage = true;
                }catch (Exception $ex){
                    $successMessage = false;
                }

                return $this->redirectToRoute('itemCRUD',array('success' => $successMessage));
            }

            $formDelete->handleRequest($request);

            if ($formDelete->isSubmitted() && $formDelete->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $data = $formDelete->getData();
                $item = $this->getItem($data['id']);
                $em->remove($item);
                $em->flush();
            }

            $formAddNewItem->handleRequest($request);

            if ($formAddNewItem->isSubmitted() && $formAddNewItem->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $item = $formAddNewItem->getData();

                $file = $item['image'];
                $dateNow = new \DateTime();
                $dateNow = $dateNow->format("YmdHis");
                $newFileName = rand(1,9999).$dateNow.rand(1,9999).'.'.$file->guessExtension();

                $file->move(
                    $this->getParameter('item_image_directory'),
                    $newFileName
                );

                $newItem = new Items();

                $newItem->setDescription($item['description']);
                $newItem->setImagelink($newFileName);
                $newItem->setActive($item['active']);

                $em->persist($newItem);
                $em->flush();
                $items = $this->getItems();
            }

            $items = $this->getItems();

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

    private function getNotSelectedItemUsers(){
        $member =  $this->getDoctrine()
            ->getRepository(MemberEntry::class)
            ->findBy(array('item'=>null));

        $amount = count($member);

        if($amount != null){
            return $amount;
        }else{
            return 0;
        }
    }

    private function getAmountOfEnters(){
        $member =  $this->getDoctrine()
            ->getRepository(MemberEntry::class)
            ->findAll();

        $amount = count($member);

        if($amount != null){
            return $amount;
        }else{
            return 0;
        }
    }

    private function getDailyEntriesLastDays($amountOfDaysBack){
        $today = new \DateTime();
        $back = new \DateTime("-".$amountOfDaysBack." days");
        $back = $back->setTime(0,0,0);

        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("select u from App\Entity\MemberEntry u where u.entered_at BETWEEN :back AND :today")
        ->setParameters(array(
            'today'=>$today,
            'back'=>$back
        ));

        $amountDiff = $today->diff($back)->days;
        $days = array_fill(0,$amountDiff,0);
        $labels = array_fill(0,$amountDiff,"");

        $result = $q->getResult();

        for ($i = 0 ; $i<$amountDiff;$i++){
            $btwvar = new \DateTime("-".$i." days");
            $labels[$i] = $btwvar->format('d-m-Y');
        }
        foreach ($result as $entry){
            $index = array_search($entry->getEnteredAt()->format('d-m-Y'),$labels);
            $days[$index]++;
        }

        return array("labels"=>$labels,"data"=>$days);
    }

    private function getItems(){
        $items =  $this->getDoctrine()->getManager()
            ->getRepository(Items::class)
            ->findAll();
        return $items;
    }

    private function getItem($id){
        $item =  $this->getDoctrine()->getManager()
            ->getRepository(Items::class)
            ->findOneBy(array('id'=>$id));
        return $item;
    }
}