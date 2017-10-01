<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\RedirectResponse;
// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Form\Form;

class UtilsController extends Controller {

  

    /**
     * @Route("/imgur", name="imgur_url_upload")
     * @Template()
     */
    public function imgurAction() {
        $client_id = "YOURCLIENTIDHERE";

        $image = $this->getRequest()->get("url");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => $image));

        $reply = curl_exec($ch);
        curl_close($ch);

        $reply = json_decode($reply);
        $newimgurl = $reply->data->link;

        $returns = array();
        return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
    }

    public static function includeSFSUtil($class_name) {
        $build_path = __DIR__ . "/../../SFSBundle/Libs/" . $class_name . "/" . $class_name . ".php";
//echo "<h1>$build_path</h1>";
        require_once $build_path;
    }

    public function imagenesAction($title, $section, $identifier, $form_id, $input_id, $resource_values, $is_multiple) {
        return $this->render("AcmeSFSBundle:Utils:imagenes.html.twig", array(
                    "title_desc" => $title,
                    "section_js" => $section,
                    "identifier" => $identifier,
                    "resources" => $resource_values != "" ? $resource_values : "[]",
                    "level_path" => "/",
                    "name" => $input_id,
                    "form" => $form_id,
                    "multiple" => $is_multiple ? "true" : "false"
        ));
    }

    public function imagenAction($input_id, $form_id) {
        return $this->render("AcmeSFSBundle:Utils:imagen.html.twig", array(
                    "name" => $input_id,
                    "form" => $form_id
        ));
    }

    public function mapsAction($input_id, $form_id) {
        return $this->render("AcmeSFSBundle:Utils:maps.html.twig", array(
                    "name" => $input_id,
                    "form" => $form_id
        ));
    }

    /**
     * Simplifica la insercción de botones de edicion
     * 
     * @param type $controller_name
     * Nombre del controlador, deben estar definidas las acciones:
     * add_{controlador}        Nuevo elemento
     * remove_{controlador}     Eliminar elemento
     *                          Ajax: #{controlador}_element_{index}
     *                          Redirect: /{controlador}
     * {controlador}            Lista de elementos
     * 
     * @param type $id
     * Indice del elmento en el que se va a operar.
     * 
     * @return type
     */
    public function modifyAction($bundle, $entity, $id, $label = true) {
        return $this->render("AcmeSFSBundle:Utils:modify.html.twig", array(
                    "edit_url" => "add_editor",
                    "remove_url" => "remove_editor",
                    "entity_path" => "Acme$bundle" . "Bundle:$entity",
                    "redirect_url" => $entity,
                    "index" => $id,
                    "element" => $entity . "_element_" . $id,
                    "label" => $label,
                    "bundle" => $bundle,
                    "entity" => $entity
        ));
    }

    /*
      public function modifyAction($controller_name, $id, $label = true) {
      return $this->render("AcmeSFSBundle:Utils:modify.html.twig", array(
      "edit_url" => "add_" . $controller_name,
      "remove_url" => "remove_" . $controller_name,
      "redirect_url" => $controller_name,
      "index" => $id,
      "element" => $controller_name . "_element_" . $id,
      "label" => $label
      ));
      } */

    public function niceditorAction($input_id, $form_id) {
        return $this->render("AcmeSFSBundle:Utils:niceditor.html.twig", array(
                    "name" => $input_id,
                    "form" => $form_id
        ));
    }

    public function tinyeditorAction($input_id, $form_id) {
        return $this->render("AcmeSFSBundle:Utils:tinyeditor.html.twig", array(
                    "name" => $input_id,
                    "form" => $form_id
        ));
    }

    public function tagsAction($input_id) {
        return $this->render("AcmeSFSBundle:Utils:tags.html.twig", array(
                    "name" => $input_id
//,"tags" => $tags
        ));
    }

    public function taggerAction($input_id, $tags = "") {
        return $this->render("AcmeSFSBundle:Utils:tagger.html.twig", array(
                    "name" => $input_id,
                    "tags" => $tags
        ));
    }

    public function colorsAction($input_id) {
        return $this->render("AcmeSFSBundle:Utils:color.html.twig", array(
                    "name" => $input_id
        ));
    }

    public function autocompleteAction($input_id, $tags) {
        return $this->render("AcmeSFSBundle:Utils:autocomplete.html.twig", array(
                    "name" => $input_id,
                    "tags" => $tags
        ));
    }

    public function socialAction($share_link, $site_link, $description = "") {
        return $this->render("AcmeSFSBundle:Utils:social.html.twig", array(
                    "share_site_link" => $site_link,
                    "share_post_link" => $share_link,
                    "share_desc" => $description
        ));
    }

    public function facebookcommentsAction($url = "") {
        if ($url === "") {
            $url = $this->getRequest()->getUri();
        } else {
            $url = "http://" . $this->getRequest()->getHttpHost() . $url;
        }
//echo $url;
        return $this->render("AcmeSFSBundle:Utils:facebook_comments.html.twig", array("url" => $url));
    }

    /* Styles */

    public function headerAction($header = "") {
        return $this->render("AcmeSFSBundle:Utils:headers.html.twig", array(
                    "header" => $header
        ));
    }

}
