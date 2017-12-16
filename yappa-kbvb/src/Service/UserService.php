<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 14/12/2017
 * Time: 13:08
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    protected $em;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em,UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param $username
     * @param $password (plain)
     * @return bool  if is stored correctly
     */
    public function addUser($username,$password){
        if($this->userAlreadyIn($username)){
            return false;
        }else{
            $user=new User();
            $password = $this->passwordEncoder->encodePassword($user, $password);
            $user->setUsername($username);
            $user->setPassword($password);
            $this->em->persist($user);
            $this->em->flush();

            return true;
        }
    }

    public function userAlreadyIn($username){
        $user = $this->em->getRepository(User::class)->findOneBy(array('username'=>$username));
        if($user == null){return false;}
        return true;
    }
}