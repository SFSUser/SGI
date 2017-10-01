<?php

namespace Acme\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebServicios
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class WebServicios
{
    
    /**
     * @ORM\ManyToOne(targetEntity="WebPortafolio", inversedBy="refwebservicios")
     * @ORM\JoinColumn(name="categoria", referencedColumnName="id")
     **/
    public $refcategoria;
    
    
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
     * @var integer
     *
     * @ORM\Column(name="categoria", type="integer")
     */
    private $categoria;

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
     * @ORM\Column(name="keywords", type="text")
     */
    private $keywords;
    

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
     * @return WebServicios
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
     * @param integer $categoria
     * @return WebServicios
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return integer 
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set contenido
     *
     * @param string $contenido
     * @return WebServicios
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
     * @return WebServicios
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
     * Set keywords
     *
     * @param string $keywords
     * @return WebServicios
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
}
