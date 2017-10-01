<?php

namespace Acme\SFSBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * SFSUsers
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SFSAccounts implements AdvancedUserInterface, EquatableInterface, \Serializable {

    /**
     * @ORM\ManyToMany(targetEntity="SFSRoles")
     * @ORM\JoinTable(name="join_AccountRoles",
     *      joinColumns={@ORM\JoinColumn(name="parent_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="child_id", referencedColumnName="id")}
     *      )
     * */
    public $rolings;

    public function hasRole($role) {
        return (in_array($role, $this->getRoles()));
    }

    public function __construct() {
        $this->rolings = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created = new \DateTime();
        $this->expiration = new \DateTime();
        $this->lastAccess = new \DateTime();
        $this->accountStatus = 1;
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

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
     * @ORM\Column(name="avatar", type="string", length=500)
     */
    private $avatar;

    public function getAvatar() {
        return $this->avatar;
    }

    public function setAvatar($avatar) {
        $this->avatar = $avatar;
    }

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
     * @ORM\Column(name="roling", type="string", length=200)
     */
    //private $roling;

    /**
     * @ORM\Column(name="salt", type="string", length=40)
     */
    //private $salt;


    /**
     * @var integer
     *
     * @ORM\Column(name="access_level", type="integer")
     */
    //private $accessLevel;

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
    //private $confirmKey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_access", type="datetime")
     */
    private $lastAccess;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiration", type="date")
     */
    private $expiration;
    function getExpiration() {
        return $this->expiration;
    }

    function setExpiration($expiration) {
        $this->expiration = $expiration;
        return $this;
    }

        /**
     * @var \DateTime
     *
     * @ORM\Column(name="confirmed", type="datetime")
     */
    //private $confirmed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="date")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="session_token", type="string", length=100)
     */
    //private $sessionToken;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return SFSUsers
     */
    public function setUser($user) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return SFSUsers
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return SFSUsers
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword() {
        return $this->password;
    }

    /*     * ************** */
    /*
      public function getSalt()
      {
      return $this->salt;
      }

      public function setSalt($salt)
      {
      $this->salt = $salt;
      return $this;
      }
     * */
    /*     * ************** */

    /*     * ************************** */

    /**
     * Set accessLevel
     *
     * @param integer $accessLevel
     * @return SFSUsers
     */
//    public function setAccessLevel($accessLevel)
//    {
//        $this->accessLevel = $accessLevel;
//
//        return $this;
//    }

    /**
     * Get accessLevel
     *
     * @return integer 
     */
//    public function getAccessLevel()
//    {
//        return $this->accessLevel;
//    }

    /**
     * Set accountStatus
     *
     * @param integer $accountStatus
     * @return SFSUsers
     */
    public function setAccountStatus($accountStatus) {
        $this->accountStatus = $accountStatus;

        return $this;
    }

    /**
     * Get accountStatus
     *
     * @return integer 
     */
    public function getAccountStatus() {
        return $this->accountStatus;
    }

    /**
     * Set confirmKey
     *
     * @param string $confirmKey
     * @return SFSUsers
     */
//    public function setConfirmKey($confirmKey)
//    {
//        $this->confirmKey = $confirmKey;
//
//        return $this;
//    }

    /**
     * Get confirmKey
     *
     * @return string 
     */
//    public function getConfirmKey()
//    {
//        return $this->confirmKey;
//    }

    /**
     * Set lastAccess
     *
     * @param \DateTime $lastAccess
     * @return SFSUsers
     */
    public function setLastAccess($lastAccess) {
        $this->lastAccess = $lastAccess;
        return $this;
    }

    /**
     * Get lastAccess
     *
     * @return \DateTime 
     */
    public function getLastAccess() {
        return $this->lastAccess;
    }

    /**
     * Set confirmed
     *
     * @param \DateTime $confirmed
     * @return SFSUsers
     */
//    public function setConfirmed($confirmed)
//    {
//        $this->confirmed = $confirmed;
//
//        return $this;
//    }

    /**
     * Get confirmed
     *
     * @return \DateTime 
     */
//    public function getConfirmed()
//    {
//        return $this->confirmed;
//    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return SFSUsers
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set sessionToken
     *
     * @param string $sessionToken
     * @return SFSUsers
     */
//    public function setSessionToken($sessionToken)
//    {
//        $this->sessionToken = $sessionToken;
//
//        return $this;
//    }

    /**
     * Get sessionToken
     *
     * @return string 
     */
//    public function getSessionToken()
//    {
//        return $this->sessionToken;
//    }

    public function eraseCredentials() {
        
    }

    public function getRoles() {
        $roles = array();

        foreach ($this->rolings as $x) {
            $roles[] = "ROLE_" . $x->getIdentificador();
            foreach ($x->refSFSTableRoles as $y) {
                $table = $y->getTabla();
                $table = str_replace(":", "_", $table);

                $roles[] = "ROLE_" . $table;
                if ($y->getLectura()) {
                    $roles[] = "ROLE_" . $table . "_LECTURA";
                } else {
                    $roles[] = "ROLE_" . $table . "_NOT_LECTURA";
                }
                if ($y->getEscritura()) {
                    $roles[] = "ROLE_" . $table . "_ESCRITURA";
                } else {
                    $roles[] = "ROLE_" . $table . "_NOT_ESCRITURA";
                }
                if ($y->getCrear()) {
                    $roles[] = "ROLE_" . $table . "_CREAR";
                } else {
                    $roles[] = "ROLE_" . $table . "_NOT_CREAR";
                }
                if ($y->getModificar()) {
                    $roles[] = "ROLE_" . $table . "_MODIFICAR";
                } else {
                    $roles[] = "ROLE_" . $table . "_NOT_MODIFICAR";
                }
                if ($y->getEliminar()) {
                    $roles[] = "ROLE_" . $table . "_ELIMINAR";
                } else {
                    $roles[] = "ROLE_" . $table . "_NOT_ELIMINAR";
                }
            }
        }
        return $roles;
        //return explode(",", $this->roling);
    }

    public function getSalt() {
        return null;
    }

    public function getUsername() {
        return $this->user;
    }

    public function isEqualTo(UserInterface $user) {
        if ($this->password !== $user->getPassword()) {
            return false;
        }
        /*
          if ($this->salt !== $user->getSalt()) {
          return false;
          } */

        if ($this->user !== $user->getUsername()) {
            return false;
        }
        
        return true;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->user,
            $this->password,
            $this->avatar,
            $this->accountStatus,
            $this->email,
            $this->created,
            $this->lastAccess,
            $this->expiration
                // see section on salt below
                // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized) {
        list (
                $this->id,
                $this->user,
                $this->password,
                $this->avatar,
                $this->accountStatus,
                $this->email,
                $this->created,
                $this->lastAccess,
                $this->expiration
                // see section on salt below
                // $this->salt
                ) = unserialize($serialized);
    }

    public function isAccountNonExpired() {
        $now = new \DateTime();
        if($this->getExpiration() < $now && ($this->getExpiration() > $this->getCreated())){
            //echo "<h1 style='margin-top:200px'>Ha expirado</h1>";
            return false;
        }

        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return $this->accountStatus == 1;
    }

}
