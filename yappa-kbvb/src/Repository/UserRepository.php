<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 08/12/2017
 * Time: 21:08
 */

namespace App\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}