<?php

namespace Acme\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PortafolioController extends Controller {
/**
     * @Route("/contacto", name="contacto_url")
     */
    public function contactoAction() {
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");
        $r = $this->getRequest();
        $doctrine = $this->getDoctrine();
        
        return $this->render('AcmeSiteBundle:Web:contacto.html.twig', array(
            "template_parent" => "AcmeWebBundle:Sections:contacto.html.twig"
        ));
    }
    
    /**
     * @Route("/acerca/{id}", name="acerca_id_url")
     * @Route("/acerca", name="acerca_url")
     */
    public function acercaAction($id = 0) {
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");
        $r = $this->getRequest();
        $doctrine = $this->getDoctrine();
        $repo_acerca = $doctrine->getRepository("AcmeWebBundle:WebAcerca");
        $acerca = $repo_acerca->findAll();
        $acerca_first = count($acerca) > 0 ? $acerca[0] : null;
        $s = $r->get("id", 0);
        if($s > 0){
            $acerca_first = $repo_acerca->find($s);
        }
        $acerca = \EntityUtils::entityObjectsToArray($doctrine, $acerca);
        $acerca_first = \EntityUtils::entityObjectToArray($doctrine, $acerca_first);
        
        return $this->render('AcmeSiteBundle:Web:acerca.html.twig', array(
            "acerca" => $acerca,
            "acerca_first" => $acerca_first,
                    "template_parent" => "AcmeWebBundle:Sections:acerca.html.twig"
        ));
    }
    /**
     * @Route("/portafolio/seccion/{ca}", name="portafolio_ca_url")
     * @Route("/portafolio/{id}", name="portafolio_id_url")
     * @Route("/portafolio", name="portafolio_url")
     */
    public function indexAction($id = 0, $ca = 0) {
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("Utils");
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");
        $r = $this->getRequest();
        $c = $r->get("ca", 0);
        $s = $r->get("id", 0);
        
        $doctrine = $this->getDoctrine();
        $data = array();
        $repo_servicios = $doctrine->getRepository("AcmeWebBundle:WebServicios");
        $repo_portafolio = $doctrine->getRepository("AcmeWebBundle:WebPortafolio");
        $servicios = $repo_servicios->findAll();
        $portafolio = $repo_portafolio->findAll();
        
        $first_portafolio = count($portafolio) > 0 ? $portafolio[0] : null;
        
        if($c > 0){
            $sp = $repo_portafolio->find($c);
            $first_portafolio = $sp !== null ? $sp : $first_portafolio;
        }
        
        $first_portafolio_service = count($first_portafolio->refwebservicios) > 0 ? $first_portafolio->refwebservicios[0] : null;
        $servicio = null;
        
        if ($s > 0) {
            $servicio = $repo_servicios->find($s);
        } else {
            $servicio = $first_portafolio_service;
        }
        
        $servicios_array = \EntityUtils::entityObjectsToArray($doctrine, $servicios);
        $servicio_array = \EntityUtils::entityObjectToArray($doctrine, $servicio);
        //$servicios = $doctrine->getRepository("AcmeWebBundle:WebPortafolio");

        return $this->render('AcmeSiteBundle:Web:portafolio.html.twig', array(
                    'portafolio_first' => $first_portafolio,
                    'portafolio' => $portafolio,
                    "servicio" => $servicio,
                    "servicios_array" => $servicios_array,
                    "servicio_array" => $servicio_array,
                    "servicio_tags" => \Utils::jsonDecode($servicio->getKeywords()),
                    "template_parent" => "AcmeWebBundle:Sections:portafolio.html.twig"
        ));
        //return $this->render('AcmeWebBundle:Portafolio:index.html.twig', array('data' => $data));
    }

}
