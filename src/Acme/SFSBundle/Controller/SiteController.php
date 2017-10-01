<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;

class SiteController extends Controller {

    /**
     * @Route("/secured/mensajes", name="url_mensajes")
     
    public function mensajesAction() {
        $meta = "";
        return $this->render('AcmeSFSBundle:Site:mensajes.html.twig', array('template_parent' => "AcmeSiteBundle:SFS:mensajes.html.twig"));
    }

    /**
     * @Route("/secured/{id}/stats", name="url_stats")
     
    public function statsAction($id) {
        $meta = "";
        return $this->render('AcmeSFSBundle:Site:stats.html.twig', array(
                    'template_parent' => "AcmeSiteBundle:SFS:stats.html.twig",
                    "ga_id" => $id
        ));
    }

    /**
     * @Route("/missing", name="url_missing")
     
    public function missingAction() {
        $msg = $this->getRequest()->get("msg");
        return $this->render('AcmeSFSBundle:Site:missing.html.twig', array(
                    'template_parent' => "AcmeSiteBundle:SFS:missing.html.twig",
                    "message" => $msg
        ));
    }
    
    /**
     * @Route("/secured/accounts", name="url_accounts")
     
    public function accountsAction() {
        $msg = $this->getRequest()->get("msg");
        return $this->render('AcmeSFSBundle:Site:accounts.html.twig', array(
                    'template_parent' => "AcmeSiteBundle:SFS:accounts.html.twig"
        ));
    }

    /**
     * @Route("/seccion/{seccion}", name="url_seccion")
     * @Route("/secciones/{id}", name="url_seccion_element")
     
    public function seccionAction() {
        $seccion = $this->getRequest()->get("seccion");
        $id = $this->getRequest()->get("id") + 0;

        UtilsController::includeSFSUtil("EntityUtils");

        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSSeccion");

        if ($id === 0) {
            $data = $repo->findBy(array(
                "identificador" => $seccion
            ));
        } else {
            $data = $repo->findBy(array(
                "id" => $id
            ));
        }

        if (!empty($data)) {
            $data = $data[0];
            $data = \EntityUtils::entityObjectToArray($this->getDoctrine(), $data);
        } else {
            $data = null;
        }

        return $this->render('AcmeSFSBundle:Site:seccion.html.twig', array(
                    'template_parent' => "AcmeSiteBundle:SFS:seccion.html.twig",
                    "data" => $data,
                    "identificador" => $seccion
        ));
    }
*/
}
