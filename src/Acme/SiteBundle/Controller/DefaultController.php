<?php

namespace Acme\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller {


    public static function onContact(Controller $c, $data) {
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("Utils");
        $categoria = $data->getCategoria();
        $titulo = strip_tags($data->getTitulo());
        $contenido = $data->getContenido();

        $to = "comercial@sgisas.com, comercial2@sgisas.com, gerencia@sgisas.com";

        switch (strtolower($categoria)) {
            case "orden":
                $to = "orden@sgisas.com";
                break;
        }

        $status = \Utils::sendTwigEmail(
                        $c, "gerencia@sgisas.com", $to, "mirrow@ipscdo.com", //Correo de prueba, para verificar si llego a destinatario
                        "($categoria) IPSCDO - $titulo", "AcmeSiteBundle:Mail:contact.html.twig", array(
                    "message" => $contenido
        ));
    }

    /**
     * @Route("/validar")
     * @Route("/verificar")
     * @Route("/verificar/certificado", name="url_verify")
     */
    public function indexAction() {
        $id = $this->getRequest()->get("id", "");
        return $this->render('AcmeSiteBundle:Default:verificar.html.twig', array('id' => $id));
    }

    public function frontAcercaAction() {
        $menu = $this->getRequest()->get("menu", false) !== false;
        $template = $menu ? "AcmeSiteBundle:Fragments:acerca_menu.html.twig" : 'AcmeSiteBundle:Fragments:acerca.html.twig';
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository("AcmeWebBundle:WebAcerca");
        $data = $repo->findAll();
        $data = \EntityUtils::entityObjectsToArray($doctrine, $data);
        return $this->render($template, array('data' => $data));
    }
    public function frontPortafolioAction() {
        $menu = $this->getRequest()->get("menu", false) !== false;
        $template = $menu ? "AcmeSiteBundle:Fragments:portafolio_menu.html.twig" : 'AcmeSiteBundle:Fragments:portafolio.html.twig';
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository("AcmeWebBundle:WebPortafolio");
        $data = $repo->findAll();
        $data = \EntityUtils::entityObjectsToArray($doctrine, $data);
        return $this->render($template, array('data' => $data));
    }
    public function frontNoticiasAction() {
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository("AcmeWebBundle:WebNoticias");
        $data = $repo->findAll();
        $data = \EntityUtils::entityObjectsToArray($doctrine, $data);
        return $this->render('AcmeSiteBundle:Fragments:noticias.html.twig', array('data' => $data));
    }
//    public function frontAcercaAction() {
//        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");
//        $doctrine = $this->getDoctrine();
//        $repo = $doctrine->getRepository("AcmeWebBundle:WebAcerca");
//        $data = $repo->findAll();
//        $data = \EntityUtils::entityObjectsToArray($doctrine, $data);
//        return $this->render('AcmeSiteBundle:Fragments:acerca.html.twig', array('data' => $data));
//    }
}
