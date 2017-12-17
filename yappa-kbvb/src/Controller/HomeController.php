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
use App\Form\ItemSelectionType;
use App\Form\MemberEntryType;
use App\Service\ItemService;
use App\Service\MemberEntryService;
use App\Service\MemberService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class HomeController extends Controller
{

    public function index(Request $request,MemberEntryService $meService,MemberService $memberService)
    {
        $session = new Session();
        $form = $this->createForm(MemberEntryType::class,new MemberEntry());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $memberEntry = $form->getData();
            $date = \DateTime::createFromFormat('Y-m-d H:i:s',$memberEntry->getYear().'-'.$memberEntry->getMonth().'-'.$memberEntry->getDay()
                .' 00:00:00');
            if($memberService->isMember($memberEntry->getMemberId(),$date)){
                if($meService->alreadyEntered($memberEntry->getMemberId(),$date)){
                    $form->addError(new FormError('Je hebt al een keuze gemaakt'));
                }else{
                    $idPreviousEnter = $meService->enteredNoChoice($memberEntry->getMemberId(),$date);
                    if($idPreviousEnter != null){
                        $session->set('checkForRedirecting', $idPreviousEnter);
                        return $this->redirectToRoute('keuze',array('id'=>$idPreviousEnter));
                    }

                    $meService->addMembersEntry($memberEntry);

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
    public function itemSelect(Request $request,MemberEntryService $memberEntryService,$id = null){
        $session = new Session();
        if($id == null){
            return $this->redirectToRoute('home');
        }

        if(!$session->has('checkForRedirecting')){
            return $this->redirectToRoute('home');
        }elseif ($session->get('checkForRedirecting') != $id){
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ItemSelectionType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $betweenvar = $form->getData();
            if($betweenvar != null){
                $memberEntryService->setItemId($id,(int)$betweenvar['item']->getId());
                $session->set('stepsDone',$session->get('stepsDone')+1);
                return $this->redirectToRoute('proficiat');
            }
        }

        return $this->render('Layouts/Main_Layout.html.twig',array(
            'form' => $form->createView(),
            'templateName' => 'item_select',
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
        }

        return $this->redirectToRoute('home');
    }
}