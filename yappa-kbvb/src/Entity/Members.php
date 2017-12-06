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
     * @var integer $id
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @var string $id_membership
     *
     * @ORM\Column(name="id_membership")
     */
    protected $id_membership;

    /**
     * @var string $birthdate
     *
     * @ORM\Column(name="birthdate",type="datetime")
     */
    protected $birthdate;
}