<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 05/12/2017
 * Time: 20:41
 */

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="items")
 * @ORM\Entity
 */
class Items
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
     * @var integer $description
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    /**
     * @var integer $image_link
     *
     * @ORM\Column(name="image_link", type="text")
     */
    protected $image_link;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getImageLink()
    {
        return $this->image_link;
    }

    public function __toString() {
        return (string)$this->description;
    }
}