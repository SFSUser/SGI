<?php

namespace Acme\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class NoticiasController extends Controller {

    /**
     * @Route("/noticias/{id}", name="noticia_url")
     */
    public function noticiaAction($id) {
        $doctrine = $this->getDoctrine();

        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");
        $manager = $doctrine->getManager();
        $repo = $doctrine->getRepository("AcmeWebBundle:WebNoticias");

        //$id = $this->getRequest()->get("id", "");
        $data = $repo->find($id);
        $data->incrementarVisitas();
        $manager->flush();
        $data = \EntityUtils::entityObjectToArray($doctrine, $data);

        if (empty($data)) {
            return $this->redirect($this->generateUrl("url_missing", array("msg" => "La noticia no fue encontrada.")), 301);
        }

        return $this->render('AcmeSiteBundle:Web:noticia.html.twig', array(
                    'data' => $data,
                    "template_parent" => "AcmeWebBundle:Sections:noticia.html.twig"
        ));
    }

    /**
     * @Route("/noticias", name="noticias_url")
     * @Route("/noticias/")
     */
    public function noticiasAction() {
        $doctrine = $this->getDoctrine();
        
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");
        $repo = $doctrine->getRepository("AcmeWebBundle:WebNoticias");

        $id = $this->getRequest()->get("id", "");
        $data = \EntityUtils::queryUnicValues($doctrine, "AcmeWebBundle:WebNoticias", "categoria");
        //$data = \EntityUtils::entityObjectsToArray($doctrine, $data);
        if (empty($data)) {
            return $this->redirect($this->generateUrl("url_missing", array("msg" => "La sección de noticias esta vacía.")), 301);
        }

        return $this->render('AcmeSiteBundle:Web:noticias.html.twig', array(
                    'data' => $data,
                    "template_parent" => "AcmeWebBundle:Sections:noticias.html.twig"
        ));
    }

    /**
     * @Route("/ajax/noticias", name="ajax_noticias_url")
     */
    public function ajaxnoticiasAction() {
        $doctrine = $this->getDoctrine();


        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");
        $repo = $doctrine->getRepository("AcmeWebBundle:WebNoticias");
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("PaginatorUtils");

        $cols = array();
        $category = $this->getRequest()->get("category", "");
        $find = $this->getRequest()->get("find", "");

        //////
        $paginator = new \PaginatorUtils($this);
        //$paginator->count=1;
        $query = $doctrine->getRepository("AcmeWebBundle:WebNoticias")->createQueryBuilder("c");


        if ($category != "") {
            $query->andWhere('c.categoria = :categoria');
            $query->setParameter('categoria', $category);
        }
        if ($find != "") {
            $query->orWhere('c.titulo like :find');
            $query->orWhere('c.categoria like :find');
            $query->orWhere('c.contenido like :find');
            $query->setParameter('find', "%$find%");
        }

        $query->add('orderBy', 'c.fecha DESC');

        //Only count
        $buttons = "No hubo botones :(";
        //$query->select("count(c.id)");
        $buttons = $paginator->getButtonList($query, "c", "button");

        //Select all elements
        //$query->select("c");
        $data = $paginator->getQueriedPaginated($query, "c"); //$query->getQuery()->getResult();
        $data = \EntityUtils::entityObjectsToArray($doctrine, $data);

        return $this->render('AcmeWebBundle:Noticias:noticias_query.html.twig', array('data' => $data, "buttons" => $buttons));
    }

}
