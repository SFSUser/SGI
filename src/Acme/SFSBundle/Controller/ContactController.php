<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ContactController extends Controller {

    /**
     * @Route("contact_box", name="contact_box")
     * @Template()
     */
    public function contactAction() {
        return $this->render("AcmeSFSBundle:Contact:form.html.twig", array(
        ));
    }

    /**
     * @Route("contacts", name="contacts")
     * @Template()
     */
    public function indexAction() {
        return $this->render("AcmeSFSBundle:Contact:index.html.twig");
    }

    /**
     * @Route("/secured/getcontactlist", name="get_contactlist")
     * @Template()
     */
    public function getlistAction() {
        UtilsController::includeSFSUtil("EntityUtils");
        $state = $this->getRequest()->get("state", 7) + 0;
        $conds = array();




        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSContact");
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("PaginatorUtils");


        $query = $repo->createQueryBuilder("c");
        $paginator = new \PaginatorUtils($this);
        $query->add('orderBy', 'c.id DESC');
        if ($state !== 7) {
            $query->andWhere("c.estado = $state");
            /*$conds = array(
                "estado" => $state
            );*/
        }

        /* $mensajes = $repo->findBy(
          $conds, array("id" => "DESC")
          ); */
        
        $buttons = $paginator->getButtonList($query, "c", "button");
        $mensajes = $paginator->getQueriedPaginated($query, "c");
        $mensajes = \EntityUtils::entityObjectsToArray($this->getDoctrine(), $mensajes);
        return $this->render("AcmeSFSBundle:Contact:list.html.twig", array(
                    "mensajes" => $mensajes,
                    "conds" => $conds,
                    "buttons" => $buttons
        ));
    }

    /**
     * @Route("/secured/getcontact", name="get_contact")
     * @Template()
     */
    public function getAction() {
        $id = $this->getRequest()->get("id") + 0;
        $state = $this->getRequest()->get("state", 1) + 0;
        $json = $this->getRequest()->get("json", "false");
        UtilsController::includeSFSUtil("EntityUtils");
        $manager = $this->getDoctrine()->getManager();
        $result = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSContact")->find($id);
        $result_files = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSUploads")->findBy(array(
            "identifier" => "SFSContact_" . $id
        ));
        if ($result !== null) {
            if ($json !== "false") {
                $result->setEstado($state);
            } else if ($result->getEstado() === 0) {
                $result->setEstado(1);
            }
            $manager->flush();
        }
        $data = \EntityUtils::entityObjectToArray($this->getDoctrine(), $result);
        $result_files = \EntityUtils::entityObjectsToArray($this->getDoctrine(), $result_files);
        if ($json !== "false") {
            return new \Symfony\Component\HttpFoundation\JsonResponse(array(
                "data" => $data,
                "files" => $result_files
            ));
        }
        return $this->render("AcmeSFSBundle:Contact:contact.html.twig", array(
            "data" => $data,
            "files" => $result_files
            
        ));
    }
    
    public static function addContact($controller, $categoria, $titulo, $contenido, $file_token = "awhdebcbaedyhy"){
        $manager = $controller->getDoctrine()->getManager();
        $contact = new \Acme\SFSBundle\Entity\SFSContact();
        
        $contact->setEstado(0);
        $contact->setCategoria($categoria);
        $contact->setTitulo($titulo);
        $contact->setContenido($contenido);
        $contact->setFecha(new \DateTime("now"));
        $manager->persist($contact);
        $manager->flush();

        //Reidentifier atached files
        $new_identifier = "SFSContact_" . $contact->getId();
        $repo = $controller->getDoctrine()->getRepository("AcmeSFSBundle:SFSUploads");
        $attached_files = $repo->findBy(array(
            "identifier" => $file_token
        ));
        foreach($attached_files as $x){
            $x->setIdentifier($new_identifier);
        }
        $manager->flush();

        UtilsController::includeSFSUtil("SFSEvents");
        \SFSEvents::callContactEvent($controller, $contact);
    }

    /**
     * @Route("addcontact", name="add_contact")
     * @Template()
     */
    public function addAction() {
        $request = $this->getRequest();
        //$template = $request->get("template", "");
        //$params = $request->get("params", array());
        $contenido = $request->get("contenido", "Sin contenido");
        $titulo = $request->get("titulo", "Sin título");
        $categoria = $request->get("categoria", "General");
        $file_token = $request->get("file_token", "XASDADACSCVDASFASD");
        
        self::addContact($this, $categoria, $titulo, $contenido, $file_token);
        
        $returns = array("result" => 1);
        return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
    }

    /**
     * @Route("/secured/removecontact", name="remove_contact")
     * @Template()
     */
    public function removeAction() {
        $request = $this->getRequest();
        $id = $request->get("id");
        $element = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSContact")->find($id);

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($element);
        $manager->flush();

        $returns = array("result" => 1);
        return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
    }

}
