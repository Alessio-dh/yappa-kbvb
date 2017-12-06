<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 05/12/2017
 * Time: 14:23
 */

namespace App\Controller;

use App\Entity\Items;
use App\Entity\MemberEntry;
use App\Entity\Members;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        $memberEntry = new MemberEntry();
        $form = $this->createFormBuilder($memberEntry)
            ->add('day', IntegerType::class,array('label'=>'Dag',
                'attr'=> array(
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 31
                )
            ))
            ->add('month', IntegerType::class,array('label'=>'Maand',
                'attr'=> array(
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 12
                )
            ))
            ->add('year', IntegerType::class,array('label'=>'Jaar',
                'attr'=> array(
                    'class' => 'form-control',
                    'min' => 1900,
                    'max' => date("Y")
                )
            ))
            ->add('member_id', IntegerType::class,array('label'=>'Lidnummer',
                'attr'=> array(
                    'class' => 'form-control',
                )
            ))
            ->add('save', SubmitType::class, array('label' => 'Volgende','attr'=>array('class'=>'btn btn-primary')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $memberEntry = $form->getData();
            if($this->isMember($memberEntry->getMemberId())){
                $em = $this->getDoctrine()->getManager();

                $date = \DateTime::createFromFormat('Y-m-d H:i:s',$memberEntry->getYear().'-'.$memberEntry->getMonth().'-'.$memberEntry->getDay().' 00:00:00');
                $dateEntered = new \DateTime();

                $memberEntry->setBirthDate($date);
                $memberEntry->setEnteredAt($dateEntered);

                $em->persist($memberEntry);
                $em->flush();
                return $this->redirectToRoute('keuze',array('id'=>$memberEntry->getId()));
            }else{
                throw new \Exception('Not a member of the red devils fanclub');
            }
        }

        return $this->render('Layouts/Main_Layout.html.twig',array(
            'form' => $form->createView(),
            'done' => false
        ));
    }

    /**
     * @Route("/keuze/{id}",name="keuze")
     */
    public function itemSelect(Request $request,$id = null){
        if($id == null){
            return $this->redirectToRoute('home');
        }

        $items = $this->getItemsToChoose();

        $form = $this->createFormBuilder(null)
            ->add('item', EntityType::class, array(
                'class'  => Items::class,
                'choices' =>$items
            ))
            ->add('save', SubmitType::class, array('label' => 'Verzenden','attr'=>array('class'=>'btn btn-primary')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $betweenvar = $form->getData();
            if($betweenvar != null){
                $em = $this->getDoctrine()->getManager();
                $memberEntry = $em->getReference('App\Entity\MemberEntry',$id);
                $memberEntry->setItem((int)$betweenvar['item']->getId());
                $em->persist($memberEntry);
                $em->flush();
                return $this->redirectToRoute('proficiat');
            }
        }

        return $this->render('Layouts/Main_Layout.html.twig',array(
            'form' => $form->createView(),
            'done' => false
        ));
    }

    /**
     * @Route("/proficiat",name="proficiat")
     */
    public function showCongratulationsScreen(){
        return $this->render('Layouts/Main_Layout.html.twig',array('done'=>true));
    }

    private function isMember($memberId){
        $member =  $this->getDoctrine()
            ->getRepository(Members::class)
            ->findOneBy(array('id_membership' => $memberId));
        if($member != null ){
            return true;
        }else{
            return false;
        }
    }

    private function getItemsToChoose(){
        $items = $member =  $this->getDoctrine()
            ->getRepository(Items::class)
            ->findAll();
        return $items;
    }
}