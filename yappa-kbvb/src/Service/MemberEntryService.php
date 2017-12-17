<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 16/12/2017
 * Time: 14:25
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MemberEntry;

class MemberEntryService
{
    private $em;
    private $memberService;

    public function __construct(EntityManagerInterface $em,MemberService $memberService)
    {
        $this->em = $em;
        $this->memberService = $memberService;
    }

    public function alreadyEntered($memberId,$birthDate){
        $member =  $this->em
            ->getRepository(MemberEntry::class)
            ->findOneBy(array('member_id' => $memberId,'birthdate'=>$birthDate,'item'=>[1,2,3,4]));

        if($member != null ){return true;}
        return false;
    }

    public function enteredNoChoice($memberId,$birthDate){
        $member =  $this->em
            ->getRepository(MemberEntry::class)
            ->findOneBy(array('member_id' => $memberId,'birthdate'=>$birthDate,'item'=>null));

        if($member != null){return $member->getId();}
        return null;
    }

    public function addMembersEntry($memberEntry){
        $date = \DateTime::createFromFormat('Y-m-d H:i:s',$memberEntry->getYear().'-'.$memberEntry->getMonth().'-'.$memberEntry->getDay().' 00:00:00');
        $memberEntry->setBirthDate($date);
        $dateEntered = new \DateTime();
        $memberEntry->setEnteredAt($dateEntered);

        $this->em->persist($memberEntry);
        $this->em->flush();
    }

    public function setItemId($MemberEntryId,$itemId){
        $memberEntry = $this->em->getReference('App\Entity\MemberEntry',$MemberEntryId);
        $memberEntry->setItem($itemId);
        $this->em->persist($memberEntry);
        $this->em->flush();
    }
}