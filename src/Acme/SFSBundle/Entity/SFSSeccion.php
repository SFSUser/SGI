<?php

namespace Acme\SFSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SFSSeccion
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SFSSeccion
{
    /**
     * Get id
     *
     * @return integer 
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @ORM\Column(name="titulo", type="string", length=500)
     */
    private $titulo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="identificador", type="string", length=50)
     */
    private $identificador;

    /**
     * @var string
     *
     * @ORM\Column(name="contenido", type="text")
     */
    private $contenido;


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
     * Set titulo
     *
     * @param string $titulo
     * @return SFSSeccion
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }
    public function setIdentificador($titulo)
    {
        $this->identificador = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo()
    {
        return $this->titulo;
    }
    public function getIdentificador()
    {
        return $this->identificador;
    }

    /**
     * Set contenido
     *
     * @param string $contenido
     * @return SFSSeccion
     */
    public function setContenido($contenido)
    {
        $this->contenido = $contenido;

        return $this;
    }

    /**
     * Get contenido
     *
     * @return string 
     */
    public function getContenido()
    {
        return $this->contenido;
    }
}
