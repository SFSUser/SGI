<?php

namespace Acme\SFSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SFSTableRoles
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SFSTableRoles
{
    public function __toString() {
        return $this->tabla;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="SFSRoles", inversedBy="refSFSTableRoles")
     * @ORM\JoinColumn(name="role", referencedColumnName="id")
     **/
    public $refrole;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="role", type="integer")
     */
    private $role;
    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
        return $this;
    }

    
    /**
     * @var string
     *
     * @ORM\Column(name="tabla", type="string", length=100)
     */
    private $tabla;

    /**
     * @var boolean
     *
     * @ORM\Column(name="lectura", type="boolean")
     */
    private $lectura = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="escritura", type="boolean")
     */
    private $escritura = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="crear", type="boolean")
     */
    private $crear = true;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="modificar", type="boolean")
     */
    private $modificar = true;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="eliminar", type="boolean")
     */
    private $eliminar = true;

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
     * Set tabla
     *
     * @param string $tabla
     * @return SFSTableRoles
     */
    public function setTabla($tabla)
    {
        $this->tabla = $tabla;

        return $this;
    }

    /**
     * Get tabla
     *
     * @return string 
     */
    public function getTabla()
    {
        return $this->tabla;
    }

    /**
     * Set lectura
     *
     * @param boolean $lectura
     * @return SFSTableRoles
     */
    public function setLectura($lectura)
    {
        $this->lectura = $lectura;

        return $this;
    }

    /**
     * Get lectura
     *
     * @return boolean 
     */
    public function getLectura()
    {
        return $this->lectura;
    }

    /**
     * Set escritura
     *
     * @param boolean $escritura
     * @return SFSTableRoles
     */
    public function setEscritura($escritura)
    {
        $this->escritura = $escritura;

        return $this;
    }

    /**
     * Get escritura
     *
     * @return boolean 
     */
    public function getEscritura()
    {
        return $this->escritura;
    }
    
    
    
    function getCrear() {
        return $this->crear;
    }

    function getModificar() {
        return $this->modificar;
    }

    function getEliminar() {
        return $this->eliminar;
    }

    function setCrear($crear) {
        $this->crear = $crear;
        return $this;
    }

    function setModificar($modificar) {
        $this->modificar = $modificar;
        return $this;
    }

    function setEliminar($eliminar) {
        $this->eliminar = $eliminar;
        return $this;
    }
}
