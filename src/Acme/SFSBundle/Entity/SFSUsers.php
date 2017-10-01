<?php

namespace Acme\SFSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SFSUsers
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SFSUsers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=100)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=100)
     */
    private $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="access_level", type="integer")
     */
    private $accessLevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="account_status", type="integer")
     */
    private $accountStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="confirm_key", type="string", length=100)
     */
    private $confirmKey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_access", type="datetime")
     */
    private $lastAccess;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="confirmed", type="datetime")
     */
    private $confirmed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="session_token", type="string", length=100)
     */
    private $sessionToken;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return SFSUsers
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return SFSUsers
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return SFSUsers
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set accessLevel
     *
     * @param integer $accessLevel
     * @return SFSUsers
     */
    public function setAccessLevel($accessLevel)
    {
        $this->accessLevel = $accessLevel;

        return $this;
    }

    /**
     * Get accessLevel
     *
     * @return integer 
     */
    public function getAccessLevel()
    {
        return $this->accessLevel;
    }

    /**
     * Set accountStatus
     *
     * @param integer $accountStatus
     * @return SFSUsers
     */
    public function setAccountStatus($accountStatus)
    {
        $this->accountStatus = $accountStatus;

        return $this;
    }

    /**
     * Get accountStatus
     *
     * @return integer 
     */
    public function getAccountStatus()
    {
        return $this->accountStatus;
    }

    /**
     * Set confirmKey
     *
     * @param string $confirmKey
     * @return SFSUsers
     */
    public function setConfirmKey($confirmKey)
    {
        $this->confirmKey = $confirmKey;

        return $this;
    }

    /**
     * Get confirmKey
     *
     * @return string 
     */
    public function getConfirmKey()
    {
        return $this->confirmKey;
    }

    /**
     * Set lastAccess
     *
     * @param \DateTime $lastAccess
     * @return SFSUsers
     */
    public function setLastAccess($lastAccess)
    {
        $this->lastAccess = $lastAccess;
        return $this;
    }

    /**
     * Get lastAccess
     *
     * @return \DateTime 
     */
    public function getLastAccess()
    {
        return $this->lastAccess;
    }

    /**
     * Set confirmed
     *
     * @param \DateTime $confirmed
     * @return SFSUsers
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return \DateTime 
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return SFSUsers
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set sessionToken
     *
     * @param string $sessionToken
     * @return SFSUsers
     */
    public function setSessionToken($sessionToken)
    {
        $this->sessionToken = $sessionToken;

        return $this;
    }

    /**
     * Get sessionToken
     *
     * @return string 
     */
    public function getSessionToken()
    {
        return $this->sessionToken;
    }
}
