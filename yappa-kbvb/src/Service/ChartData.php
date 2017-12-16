<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 13/12/2017
 * Time: 16:53
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MemberEntry;

class ChartData
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getDailyEntriesLastDays($amountOfDaysBack){
        $today = new \DateTime();
        $back = new \DateTime("-".$amountOfDaysBack." days");
        $back = $back->setTime(0,0,0);

        $q = $this->em->createQuery("select u from App\Entity\MemberEntry u where u.entered_at BETWEEN :back AND :today")
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

    public function getAmountEnteredVSnotEntered(){
        $member =  $this->em
            ->getRepository(MemberEntry::class)
            ->findBy(array('item'=>null));

        $memberTotal =  $this->em
            ->getRepository(MemberEntry::class)
            ->findAll();

        $amountEntered = count($memberTotal);
        $amountNotSelected = count($member);

        $amountSelected = $amountEntered - $amountNotSelected;

         return ['labels'=>array('Selected an item','Did not select an item'),
                                    'data' => array($amountSelected,$amountNotSelected)];
    }
}