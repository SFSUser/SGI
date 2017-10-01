<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SegmentController extends Controller {

    public function oneAction($entity, $column = "id", $pred_value = "(Sin contenido)") {
        UtilsController::includeSFSUtil("EntityUtils");
        $repo = $this->getDoctrine()->getRepository($entity);

        $result = $repo->findBy(array(), array(), 1);
        
        if(count($result) > 0){
            $result = $result[0];
            $result = \EntityUtils::entityObjectToArray($this->getDoctrine(), $result);
        }

        if (isset($result[$column]) && $result[$column] !== "") {
            $result = $result[$column];
        } else {
            $result = $pred_value;
        }

        return new \Symfony\Component\HttpFoundation\Response($result);
    }
    
    public function printAction($identifier, $pred_value = "(Sin contenido)") {
        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSResources");

        $result = $repo->findOneBy(array(
            "identifier" => $identifier
        ));

        if ($result !== null) {
            $result = $result->getValue();
        } else {
            $result = $pred_value;
        }

        return new \Symfony\Component\HttpFoundation\Response($result);
    }

    public function indexAction($identifier, $type = "text", $pred_value = "(Sin contenido)") {
        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSResources");

        $result = $repo->findOneBy(array(
            "identifier" => $identifier
        ));
        return $this->render("AcmeSFSBundle:Segment:segment.html.twig", array(
                    "segment" => $result,
                    "identifier" => $identifier,
                    "type" => $type,
                    "pred_value" => $pred_value
        ));
    }

    /**
     * @Route("/secured/segment/create", name="create_segment", defaults={"_format": "json"})
     * @Template()
     */
    public function createAction() {
        $value = $this->getRequest()->get("value");
        $identifier = $this->getRequest()->get("identifier");

        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSResources");
        $manager = $this->getDoctrine()->getManager();

        $result = $repo->findOneBy(array(
            "identifier" => $identifier
        ));

        if ($result == null) {
            $result = new \Acme\SFSBundle\Entity\SFSResources();
            $result->setIdentifier($identifier);
            $result->setType("segment");
        }
        $result->setValue($value);

        $manager->persist($result);
        $manager->flush();

        $returns = array(
            "identifier" => $identifier,
            "value" => $result->getValue() == "" ? "(Sin contenido)" : $result->getValue()
        );
        return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
    }

}
