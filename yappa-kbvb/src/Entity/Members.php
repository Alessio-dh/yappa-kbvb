<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 05/12/2017
 * Time: 19:23
 */

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="members")
 * @ORM\Entity
 */
class Members
{
    /**
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(name="id_membership",type="integer")
     */
    protected $id_membership;

    /**
     * @ORM\Column(name="birthdate",type="datetime")
     */
    protected $birthdate;

    /**
     * @return string
     */
    public function getIdMembership()
    {
        return $this->id_membership;
    }

    /**
     * @param string $id_membership
     */
    public function setIdMembership($id_membership)
    {
        $this->id_membership = $id_membership;
    }

    /**
     * @return string
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * @param string $birthdate
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;
    }


}