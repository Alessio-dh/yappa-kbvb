<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 08/12/2017
 * Time: 16:35
 */

namespace App\Controller;

use App\Entity\MemberEntry;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class AdminController extends Controller
{
    /**
     * @Route("/admin",name="admin")
     */
    public function index(){
        $templatesNeeded = [];
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
}