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

class SFSFormType extends AbstractType {

    var $form_name = "";
    var $entity_metadata = array();
    var $controller = null;

    public function __construct(\Symfony\Bundle\FrameworkBundle\Controller\Controller $controller, $entity_class_object) {
        $this->entity_metadata = $controller->getDoctrine()->getManager()->getClassMetadata(get_class($entity_class_object));
        $this->controller = $controller;
        $this->form_name = $this->entity_metadata->table["name"];
        //echo "<h1>Name: " . $this->form_name . "</h1>";
    }

    public function buildForm(FormBuilderInterface $generador, array $opciones) {
        $this->builder = $generador;

        foreach ($this->entity_metadata->fieldMappings as $value) {
            $column_type = $value["type"];
            $column_name = $value["columnName"];
            $column_nullable = $value["nullable"];
            $column_length = $value["length"];

            $options = array();
            //Carga un valor predeterminado para la tabla actual.
            $pred_value = $this->controller->getRequest()->get("default_$column_name", null);
            if($pred_value !== null){
                $options["data"] = $pred_value;
            }
            $input_type = "text";
            switch ($column_type) {
                case "boolean":
                    $input_type = "checkbox";
                    $opciones["label"] = "Selecione estado";
                    break;
                case "integer":
                    $input_type = "number";
                    break;
                case "string":
                    $options["max_length"] = $column_length;
                case "text":
                    $input_type = "text";
                    break;
                case "date":
                    $input_type = "date";
                    $options["years"] = range(1900, date('Y') + 10);
                    break;
                case "datetime":
                    $input_type = "datetime";
                    $options["years"] = range(1900, date('Y') + 10);
                    break;
            }
            switch (strtolower($column_name)) {
                case "precio":
                case "valor":
                case "costo":
                case "price":
                    $input_type = "money";
                    $options["divisor"] = 100;
                    break;
                //Generalmente son reemplazadas
                case "estado":
                case "archivo":
                case "archivos":
                case "file":
                case "files":
                case "tags":
                case "imagen":
                case "imagenes":
                case "descripcion":
                case "contenido":
                case "id":
                    $input_type = "hidden";
                    break;
                case "dia":
                    $input_type = "choice";
                    $options["choices"] = array(
                        7 => "Domingo",
                        1 => "Lunes",
                        2 => "Martes",
                        3 => "Miércoles",
                        4 => "Jueves",
                        5 => "Viernes",
                        6 => "Sábado",
                    );
                    break;
            }

            if ($column_nullable === null || $column_nullable === "" || $column_type === "boolean") {
                $options["required"] = false;
            } else {
                $options["required"] = true;
            }
            $generador->add($column_name, $input_type, $options);
        }
    }

    public function getName() {
        return $this->form_name;
    }

}
