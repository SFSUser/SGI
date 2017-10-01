<?php

namespace Acme\SFSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SFSRoles
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SFSRoles
{
    public function __toString() {
        return $this->nombre;
    }
    
     // ...
    /**
     * @ORM\OneToMany(targetEntity="SFSTableRoles", mappedBy="refrole", cascade={"persist", "remove"}))
     **/
    public $refSFSTableRoles;
    
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
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="identificador", type="string", length=50)
     */
    private $identificador;


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
     * Set nombre
     *
     * @param string $nombre
     * @return SFSRoles
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set identificador
     *
     * @param string $identificador
     * @return SFSRoles
     */
    public function setIdentificador($identificador)
    {
        $this->identificador = $identificador;

        return $this;
    }

    /**
     * Get identificador
     *
     * @return string 
     */
    public function getIdentificador()
    {
        return $this->identificador;
    }
}
