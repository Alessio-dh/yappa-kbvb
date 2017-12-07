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
use Symfony\Component\Form\FormError;
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
        $session = new Session();
        $session->start();
        $memberEntry = new MemberEntry();
        $form = $this->createFormBuilder($memberEntry)
            ->add('day', IntegerType::class,array('label'=>'Dag',
                'attr'=> array(
                    'class' => 'form-control form-inline',
                    'min' => 1,
                    'max' => 31,
                    'placeholder'=>'Dag'
                )
            ))
            ->add('month', IntegerType::class,array('label'=>'Maand',
                'attr'=> array(
                    'class' => 'form-control form-inline',
                    'min' => 1,
                    'max' => 12,
                    'placeholder'=>'Maand'
                )
            ))
            ->add('year', IntegerType::class,array('label'=>'Jaar',
                'attr'=> array(
                    'class' => 'form-control form-inline',
                    'min' => 1900,
                    'max' => date("Y"),
                    'placeholder'=>'Jaar'
                )
            ))
            ->add('member_id', IntegerType::class,array('label'=>'Lidnummer',
                'attr'=> array(
                    'class' => 'form-control',
                )
            ))
            ->add('save', SubmitType::class, array('label' => 'Volgende','attr'=>array('class'=>'pulseBtn')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $memberEntry = $form->getData();
            $date = \DateTime::createFromFormat('Y-m-d H:i:s',$memberEntry->getYear().'-'.$memberEntry->getMonth().'-'.$memberEntry->getDay().' 00:00:00');
            if($this->isMember($memberEntry->getMemberId(),$date)){
                if($this->alreadyEntered($memberEntry->getMemberId(),$date)){
                    $form->addError(new FormError('Je hebt al een keuze gemaakt'));
                }else{
                    $em = $this->getDoctrine()->getManager();
                    $dateEntered = new \DateTime();

                    $memberEntry->setBirthDate($date);
                    $memberEntry->setEnteredAt($dateEntered);

                    $em->persist($memberEntry);
                    $em->flush();

                    $session->set('stepsDone', 1);
                    $session->set('checkForRedirecting', $memberEntry->getId());
                    return $this->redirectToRoute('keuze',array('id'=>$memberEntry->getId()));
                }
            }else{
                $form->addError(new FormError('De combinatie is niet gevonden in ons systeem, Probeer opnieuw'));
            }
        }

        return $this->render('Layouts/Main_Layout.html.twig',array(
            'form' => $form->createView(),
            'templateName' => 'main_page'
        ));
    }

    /**
     * @Route("/keuze/{id}",name="keuze")
     */
    public function itemSelect(Request $request,$id = null){
        $session = new Session();
        if($id == null){
            return $this->redirectToRoute('home');
        }

        if(!$session->has('checkForRedirecting')){
            return $this->redirectToRoute('home');
        }elseif ($session->get('checkForRedirecting') != $id){
            return $this->redirectToRoute('home');
        }

        $items = $this->getItemsToChoose();

        $form = $this->createFormBuilder(null)
            ->add('item', EntityType::class, array(
                'class'  => Items::class,
                'choices' =>$items
            ))
            ->add('save', SubmitType::class, array('label' => 'Verzenden','attr'=>array('class'=>'pulseBtn')))
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
                $session->set('stepsDone',$session->get('stepsDone')+1);
                return $this->redirectToRoute('proficiat');
            }
        }

        return $this->render('Layouts/Main_Layout.html.twig',array(
            'form' => $form->createView(),
            'templateName' => 'item_select'
        ));
    }

    /**
     * @Route("/proficiat",name="proficiat")
     */
    public function showCongratulationsScreen(){
        $session = new Session();
        if($session->get('stepsDone') === 2){
            $session->remove('stepsDone');
            $session->remove('checkForRedirecting');
            return $this->render('Layouts/Main_Layout.html.twig',array('templateName'=>'congratulations'));
        }else{
            return $this->redirectToRoute('home');
        }
    }

    private function isMember($memberId,$birthDate){
        $member =  $this->getDoctrine()
            ->getRepository(Members::class)
            ->findOneBy(array('id_membership' => $memberId,'birthdate'=>$birthDate));
        if($member != null ){
            return true;
        }else{
            return false;
        }
    }

    private function alreadyEntered($memberId,$birthDate){
        $member =  $this->getDoctrine()
            ->getRepository(MemberEntry::class)
            ->findOneBy(array('member_id' => $memberId,'birthdate'=>$birthDate,'item'=>[1,2,3,4]));
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