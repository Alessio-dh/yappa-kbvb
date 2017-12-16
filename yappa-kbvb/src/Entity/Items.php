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
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    /**
     * @ORM\Column(name="image_link", type="text")
     */
    protected $image_link;

    /**
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getImageLink()
    {
        return $this->image_link;
    }

    public function setImageLink($imageLink)
    {
        $this->image_link = $imageLink;
    }

    public function __toString() {
        return (string)$this->description;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active){
        $this->active = $active;
    }


}