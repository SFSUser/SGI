<?php

namespace Acme\SFSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SFSComments
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SFSComments
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
     * @ORM\Column(name="hilo", type="string", length=100)
     */
    private $hilo;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=100)
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="correo", type="string", length=100)
     */
    private $correo;

    /**
     * @var string
     *
     * @ORM\Column(name="contenido", type="text")
     */
    private $contenido;

    /**
     * @var string
     *
     * @ORM\Column(name="respuesta", type="string", length=20)
     */
    private $respuesta;

    /**
     * @var string
     *
     * @ORM\Column(name="gustan", type="text")
     */
    private $gustan;

    /**
     * @var string
     *
     * @ORM\Column(name="nogustan", type="text")
     */
    private $nogustan;

    /**
     * @var string
     *
     * @ORM\Column(name="votos", type="string", length=20)
     */
    private $votos;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=20)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="modo", type="string", length=20)
     */
    private $modo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;


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
     * Set hilo
     *
     * @param string $hilo
     * @return SFSComments
     */
    public function setHilo($hilo)
    {
        $this->hilo = $hilo;

        return $this;
    }

    /**
     * Get hilo
     *
     * @return string 
     */
    public function getHilo()
    {
        return $this->hilo;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     * @return SFSComments
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set correo
     *
     * @param string $correo
     * @return SFSComments
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo
     *
     * @return string 
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Set contenido
     *
     * @param string $contenido
     * @return SFSComments
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
     * Set respuesta
     *
     * @param string $respuesta
     * @return SFSComments
     */
    public function setRespuesta($respuesta)
    {
        $this->respuesta = $respuesta;

        return $this;
    }

    /**
     * Get respuesta
     *
     * @return string 
     */
    public function getRespuesta()
    {
        return $this->respuesta;
    }

    /**
     * Set gustan
     *
     * @param string $gustan
     * @return SFSComments
     */
    public function setGustan($gustan)
    {
        $this->gustan = $gustan;

        return $this;
    }

    /**
     * Get gustan
     *
     * @return string 
     */
    public function getGustan()
    {
        return $this->gustan;
    }

    /**
     * Set nogustan
     *
     * @param string $nogustan
     * @return SFSComments
     */
    public function setNogustan($nogustan)
    {
        $this->nogustan = $nogustan;

        return $this;
    }

    /**
     * Get nogustan
     *
     * @return string 
     */
    public function getNogustan()
    {
        return $this->nogustan;
    }

    /**
     * Set votos
     *
     * @param string $votos
     * @return SFSComments
     */
    public function setVotos($votos)
    {
        $this->votos = $votos;

        return $this;
    }

    /**
     * Get votos
     *
     * @return string 
     */
    public function getVotos()
    {
        return $this->votos;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return SFSComments
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set modo
     *
     * @param string $modo
     * @return SFSComments
     */
    public function setModo($modo)
    {
        $this->modo = $modo;

        return $this;
    }

    /**
     * Get modo
     *
     * @return string 
     */
    public function getModo()
    {
        return $this->modo;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return SFSComments
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
}
