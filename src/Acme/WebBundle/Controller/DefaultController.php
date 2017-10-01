<?php

namespace Acme\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller {

    function renderSection($section, $parmeters = array()) {
        $template_site = "AcmeSiteBundle:Web:$section.html.twig";
        $template_web = "AcmeWebBundle:Sections:$section.html.twig";
        $params = array('template_parent' => $template_web);
        $params = array_merge($params, $parmeters);
        return $this->render($template_site, $params);
    }

    /**
     * @Route("/web/sitemap", name="web_sitemap_url", defaults={"_format": "xml"})
     * @Template()
     */
    public function sitemapAction() {
        $doctrine = $this->getDoctrine();
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");

        $sitemap["acerca_id_url"] = \EntityUtils::sitemapQuery($doctrine, "AcmeWebBundle:WebAcerca", "titulo");
        $sitemap["portafolio_id_url"] = \EntityUtils::sitemapQuery($doctrine, "AcmeWebBundle:WebServicios", "titulo");
        $sitemap["url_seccion_element"] = \EntityUtils::sitemapQuery($doctrine, "AcmeSFSBundle:SFSSeccion", "titulo");
        $sitemap["noticia_url"] = \EntityUtils::sitemapQuery($doctrine, "AcmeWebBundle:WebNoticias", "titulo");
        //$sitemap["url_empleo"] = \EntityUtils::sitemapQuery($doctrine, "AcmeSiteBundle:IPSCDOEmpleos", "titulo");

        return $this->render('AcmeWebBundle:Default:sitemap.html.twig', array(
                    "sitemap" => $sitemap
        ));
    }
    
    /**
     * @Route("/", name="url_main")
     * @Route("/", name="url_home")
     */
    public function indexAction() {
        return $this->renderSection("home");
    }

    /**
     * @Route("/secured/{id}/stats", name="url_stats")
     */
    public function statsAction($id) {
        return $this->renderSection("stats", array("ga_id" => $id));
    }

    /**
     * @Route("/missing", name="url_missing")
     */
    public function missingAction() {
        $msg = $this->getRequest()->get("msg");
        return $this->renderSection("missing", array("message" => $msg));
    }

    /**
     * @Route("/secured/mensajes", name="url_mensajes")
     */
    public function mensajesAction() {
        return $this->renderSection("mensajes");
    }

    /**
     * @Route("/secured/accounts", name="url_accounts")
     */
    public function accountsAction() {
        return $this->renderSection("accounts");
    }
    /**
     * @Route("/secured/events", name="url_events")
     */
    public function eventsAction() {
        return $this->renderSection("events");
    }

    /**
     * @Route("/seccion/{seccion}", name="url_seccion")
     * @Route("/secciones/{id}", name="url_seccion_element")
     */
    public function seccionAction() {
        $seccion = $this->getRequest()->get("seccion");
        $id = $this->getRequest()->get("id") + 0;

        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");

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

        return $this->renderSection("seccion", array(
                    "data" => $data,
                    "identificador" => $seccion
        ));
    }

    /**
     * @Route("/admin/login", name="adminlogin")
     * @Template()
     */
    public function loginAction() {
        $request = $this->getRequest();
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->renderSection("login", array(
                    "last" => $this->getRequest()->getSession()->get(SecurityContext::LAST_USERNAME),
                    "error" => $error,
                    "section" => "admin"
        ));
    }

    /**
     * @Route("/secured/{bundle}/{entity}/add", name="add_editor")
     * @Route("/secured/{bundle}/{entity}/list", name="list_editor")
     */
    public function listpageAction($bundle, $entity) {
        //\Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EditorManager");
        //$template = "AcmeSiteBundle:SFS:list.html.twig";
        $template_parent = "Acme$bundle" . "Bundle:$entity";
        $id = $this->getRequest()->get("id", 0) + 0;
        $back = $this->getRequest()->get("redirect", "");

        return $this->renderSection("editor", array(
                    "bundle" => $bundle,
                    "entity" => $entity,
                    "id" => $id,
                    "redirect" => $back
        ));
    }
}
