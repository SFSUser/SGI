<?php

namespace Acme\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SFSNoticias
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class WebNoticias
{
    
    public function __construct() {
        $this->fecha = new \DateTime();
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
     * @ORM\Column(name="titulo", type="string", length=200)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="categoria", type="string", length=50)
     */
    private $categoria;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="contenido", type="text")
     */
    private $contenido;

    /**
     * @var string
     *
     * @ORM\Column(name="imagenes", type="text")
     */
    private $imagenes;

    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="string", length=500)
     */
    private $tags;
    
    /**
     *
     * @ORM\Column(name="visitas", type="integer")
     */
    private $visitas;
    function getVisitas() {
        return $this->visitas;
    }

    function setVisitas($visitas) {
        $this->visitas = $visitas;
    }
    function incrementarVisitas() {
        ++$this->visitas;
    }

    
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
     * @return SFSNoticias
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

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

    /**
     * Set categoria
     *
     * @param string $categoria
     * @return SFSNoticias
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return string 
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return SFSNoticias
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set contenido
     *
     * @param string $contenido
     * @return SFSNoticias
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

    /**
     * Set imagenes
     *
     * @param string $imagenes
     * @return SFSNoticias
     */
    public function setImagenes($imagenes)
    {
        $this->imagenes = $imagenes;

        return $this;
    }

    /**
     * Get imagenes
     *
     * @return string 
     */
    public function getImagenes()
    {
        return $this->imagenes;
    }

    /**
     * Set tags
     *
     * @param string $tags
     * @return SFSNoticias
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string 
     */
    public function getTags()
    {
        return $this->tags;
    }
}
