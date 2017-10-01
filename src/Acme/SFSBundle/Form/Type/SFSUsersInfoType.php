<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of productosType
 *
 * @author SFS
 */

namespace Acme\SFSBundle\Form\Type;

use Symfony\Component\Form\BaseType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

//Una clase que permite ser un molde para un formulario

class SFSUsersInfoType extends AbstractType {
/*

  `usuario` varchar(100) ,
  `nombre` varchar(100) ,
  `apellido` varchar(100) ,
  `direccion` varchar(100) ,
  `telefono` varchar(100) ,
  `movil` varchar(100) ,
  `region` varchar(100) ,
  `pais` varchar(100) ,
  `ciudad` varchar(100) ,
  `sexo` varchar(100) ,
  `nacimiento` date,  
  `avatar` varchar(700) , */
    public function buildForm(FormBuilderInterface $generador, array $opciones) {
        $generador->add("usuario");
        $generador->add("nombre");
        $generador->add("apellido");
        $generador->add("direccion");
        $generador->add("telefono");
        $generador->add("movil");
        $generador->add("region");
        $generador->add("pais");
        $generador->add("ciudad");
        $generador->add("sexo");
        $generador->add("nacimiento");
        $generador->add("avatar");
    }

    public function getName() {
        return "SFSUsersInfo";
    }

}
