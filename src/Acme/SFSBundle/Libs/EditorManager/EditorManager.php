<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/**
 * Se manejan tres metodos principales:
 * 
 * crear: Crea el elemento y muestra el formulario
 * doors: lista de elementos creados
 * eliminar: eliminar el elemento
 * 
 */
/**
 * LAS ACCIONES OBLIGATORIAS SON:
 * 
 * add_(nombre)         Agrega el elemento
 * remove_(nombre)      Elimina el elemento
 * (nombre)             Redirecciona al eliminar
 * 
 * 
 *  */

/**
 * Clase que simplifica la edicion de proceso de guardado de formularios...
 *
 * @author SFS
 */
class EditorManager {

    /**
     * 
     * @param \Symfony\Bundle\FrameworkBundle\Controller\Controller $controller
     * El controlador asociado en el se ejecutara la accion del editor
     * 
     * @param type $repository
     * Repositorio, entidad de base de datos en que se va operar
     * 
     * @param type $product_object
     * Objeto base que representa el elemento en la base de datos
     * 
     * @param type $form_object
     * Objeto que representa los campos del formulario
     * 
     * @param type $manager_controller_redirect
     * Redirecciona al controlador especificado al crear el elemento
     * 
     * @param type $template
     * Plantailla que se utilizara para imprimir el formulario:
     * Variables: form, msg, id, errores
     * 
     * @return type
     * Resultado de la edicion
     */
    public static function proccess(\Symfony\Bundle\FrameworkBundle\Controller\Controller $controller, $bundle, $entity, $product_object, $other_params = array()) {
        $template = "AcmeSiteBundle:SFS:editor.html.twig";
        $repository = "Acme$bundle" . "Bundle:$entity";
        $form_object = new \Acme\SFSBundle\Form\Type\SFSFormType($controller, $product_object);
        $msg = "On blank item > ";
        $errores = array();
        //Cambiar esto

        $manager_controller_redirect = "add_editor"; // . $form_object->getName();
        $request = $controller->getRequest();
        $product = $product_object;
        $id = $request->get("id");
        $redirect = $request->get("redirect", null);
        $mode = $request->get("_mode");

        //Databases
        //Cambiar esto
        $querier = $controller->getDoctrine()->getRepository($repository);
        $manager = $controller->getDoctrine()->getManager();

        //Get and edit
        if ($id != null) {
            $result = $querier->find($id);
            if ($result != null) {
                $msg .= "On get item > ";
                $product = $result;
            }
        }

        //Update / Create
        //Cambiar esto
        $form = $controller->createForm($form_object
                , $product);
        if ($request->getMethod() == "POST") {
            //$request->attributes->remove("id");
            $form->handleRequest($request);
            if (!$form->isValid()) {
                $errores = $form->getErrors();
            } else {
                $data = $form->getData();
                if ($id == null) {
                    $manager->persist($data);
                    $manager->flush();
                    $id = $data->getId();
                    $msg .= "On create item";
                    //Cambiar esto
                    //Save and create
                    if ($mode == "1") {
                        return $controller->redirect($controller->generateUrl($manager_controller_redirect, array("bundle" => $bundle, "entity" => $entity, "redirect" => $redirect)));
                    }
                    return $controller->redirect($controller->generateUrl($manager_controller_redirect, array("id" => $id, "bundle" => $bundle, "entity" => $entity, "redirect" => $redirect)));
                } else {
                    $manager->persist($data);
                    $manager->flush();
                    $msg .= "On update item";

                    //Save and create
                    if ($mode == "1") {
                        return $controller->redirect($controller->generateUrl($manager_controller_redirect, array("bundle" => $bundle, "entity" => $entity, "redirect" => $redirect)));
                    }
                }
            }
        }

        //Cambiar esto...
        $params = array(
            "form" => $form->createView(),
            "errores" => $errores,
            "msg" => $msg,
            "id" => $id,
            "template_parent" => $template,
            "space_name" => $form_object->getName(),
            "section" => "crear_" . $form_object->getName(),
            "entity_path" => $repository,
            "bundle" => $bundle,
            "entity" => $entity,
            "back_redirect" => $redirect
        );
        foreach ($other_params as $key => $value) {
            $params[$key] = $value;
        }
        return $controller->render("AcmeSFSBundle:Utils:editor.html.twig", $params);
    }

    public static function proccessList(\Symfony\Bundle\FrameworkBundle\Controller\Controller $controller, $bundle, $entity) {
        $template = "AcmeSiteBundle:SFS:list.html.twig";
        $repository = "Acme$bundle" . "Bundle:$entity";

        $doctrine = $controller->getDoctrine();
        $repo = $doctrine->getRepository($repository);

        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("PaginatorUtils");


        $paginator = new \PaginatorUtils($controller);
        //$paginator->count=1;
        $query = $repo->createQueryBuilder("c");

        /*
        if ($category != "") {
            $query->andWhere('c.categoria = :categoria');
            $query->setParameter('categoria', $category);
        }
        if ($find != "") {
            $query->orWhere('c.titulo like :find');
            $query->orWhere('c.categoria like :find');
            $query->orWhere('c.descripcion like :find');
            $query->setParameter('find', "%$find%");
        }
         */

        $query->add('orderBy', 'c.id DESC');

        //Only count
        $buttons = "No hubo botones :(";
        //$query->select("count(c.id)");
        $buttons = $paginator->getButtonList($query, "c", "button");

        //Select all elements
        //$query->select("c");
        $data = $paginator->getQueriedPaginated($query, "c"); //$query->getQuery()->getResult();
        $data = \EntityUtils::entityObjectsToArray($doctrine, $data);


        return $controller->render("AcmeSFSBundle:Utils:list.html.twig", array(
                    "data" => $data,
                    "buttons" => $buttons,
                    "template_parent" => $template,
                    "bundle" => $bundle,
                    "entity" => $entity,
                    "space_name" => $entity
        ));
    }
}
