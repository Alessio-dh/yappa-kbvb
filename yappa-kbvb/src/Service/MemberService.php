<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 16/12/2017
 * Time: 14:29
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Members;

class MemberService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function isMember($memberId,$birthDate){
        $member =  $this->em
            ->getRepository(Members::class)
            ->findOneBy(array('id_membership' => $memberId,'birthdate'=>$birthDate));

        if($member != null ){return true;}
        return false;
    }

    public function addMember($id,$birthdate){
        $member = new Members();
        $member->setIdMembership($id);
        $member->setBirthdate(new \DateTime($birthdate." 00:00:00"));

        $this->em->persist($member);
        $this->em->flush();
    }
}