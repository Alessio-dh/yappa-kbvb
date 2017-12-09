<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 08/12/2017
 * Time: 16:12
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements AdvancedUserInterface
{
    /**
     * @ORM\Column(name="id",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="username",type="string", length=255, unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(name="password",type="string", length=255)
     */
    protected $password;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $isActive = true;

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($pass){$this->password = $pass;}
    public function setUsername($username){$this->username = $username;}
    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    public function getRoles()
    {
        return array('ROLE_USER','ROLE_ADMIN');
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->IsActive();
    }
}