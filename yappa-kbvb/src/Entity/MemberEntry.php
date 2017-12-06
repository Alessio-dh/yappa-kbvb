<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 05/12/2017
 * Time: 18:11
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="members_entered")
 * @ORM\Entity
 */
class MemberEntry
{

    protected $memberEntry;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="item",type="integer")
     */
    protected $item;


    protected $day;
    protected $month;
    protected $year;

    /**
     * @ORM\Column(name="member_id",type="integer")
     */
    protected $member_id;

    /**
     * @ORM\Column(name="entered_at",type="datetime")
     */
    protected $entered_at;

    /**
     * @ORM\Column(name="birthdate",type="datetime")
     */
    protected $birthdate;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getMemberEntry()
    {
        return $this->memberEntry;
    }

    /**
     * @param mixed $memberEntry
     */
    public function setMemberEntry($memberEntry)
    {
        $this->memberEntry = $memberEntry;
    }

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * @return mixed
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param mixed $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getMemberId()
    {
        return $this->member_id;
    }

    /**
     * @param mixed $member_id
     */
    public function setMemberId($member_id)
    {
        $this->member_id = $member_id;
    }

    /**
     * @return mixed
     */
    public function getEnteredAt()
    {
        return $this->entered_at;
    }

    /**
     * @param mixed $entered_at
     */
    public function setEnteredAt($entered_at)
    {
        $this->entered_at = $entered_at;
    }

    /**
     * @return mixed
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * @param mixed $birthdate
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;
    }



}