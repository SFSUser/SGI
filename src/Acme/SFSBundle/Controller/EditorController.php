<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\ExpressionLanguage\Expression;

class EditorController extends Controller {

    public static function callEvent(Controller $c, $entity, $id, $function_name) {
        UtilsController::includeSFSUtil("SFSEvents");
        
        \SFSEvents::callEntityEvent($c, $entity, $id, $function_name);
        
        /*
        $clas = "\\Acme\\SiteBundle\\Controller\\DefaultController";
        if (class_exists($clas)) {
            $default = new $clas();
            if (method_exists($default, $function)) {
                $default->$function($c, $entity, $id);
            }
        }*/
    }

    /**
     * @Route("secured/editor/remove", name="remove_editor")
     * @Template()
     */
    public function removeAction() {
        $id = $this->getRequest()->get("id", "");
        $entity = $this->getRequest()->get("entity", "");
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");
        self::callEvent($this, $entity, $id, "onRemoveEditor");
        $results = \EntityUtils::removeElement($this->getDoctrine(), $entity, $id);

        return new \Symfony\Component\HttpFoundation\Response(json_encode($results));
    }

    /**
     * @Route("/secured/ajax/editor", name="url_ajaxeditor", defaults={"_format": "json"})
     * @Route("/ajax/editor", name="url_entityajax", defaults={"_format": "json"})
     * @Template()
     */
    public function ajaxeditorAction() {
        $request = $this->getRequest();
        $id = $request->get("id", 0) + 0;
        $bundle = $request->get("bundle", "");
        $entity = $request->get("entity", "");
        $action = $request->get("action", "get");
        $c_order = $request->get("consult_order", array("id" => "ASC"));
        $c_conds = $request->get("consult_conditions", array());
        $action = strtolower($action);
        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();
        $repository = $doctrine->getRepository("Acme$bundle" . "Bundle:$entity");
        $entity_path = "\\Acme\\$bundle" . "Bundle\\Entity\\$entity";
        $exec_action = "get";

        $element = null;
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("ImgurImage");
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("Utils");
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EntityUtils");

        $element = $repository->find($id);
        $types = array();

        if ($action == "create" || $action === "update" || $action === "save") {
            if ($element === null) {
                $exec_action = "created";
                $element = new $entity_path();
            } else {
                $exec_action = "updated";
            }

            $entity_metadata = $doctrine->getManager()->getClassMetadata(get_class($element));
            foreach ($entity_metadata->fieldMappings as $value) {
                $column_type = $value["type"];
                $types[] = $column_type;
                $column_name = $value["columnName"];
                $column_nullable = $value["nullable"];
                $column_length = $value["length"];
                $getter = "get" . ucwords($column_name);
                $setter = "set" . ucwords($column_name);

                $predefined = null;
                if (method_exists($element, $getter)) {
                    $predefined = $element->$getter();
                }

                if ($predefined === null) {
                    switch ($column_type) {
                        case "boolean":
                            $predefined = false;
                            break;
                        case "datetime":
                            $predefined = new \DateTime();
                            break;
                        default:
                        case "string":
                        case "text":
                            $predefined = "";
                            break;
                        case "number":
                        case "integer":
                            $predefined = 0;
                            break;
                    }
                }

                $value = $request->get($column_name, $predefined);
                
                if($column_type === "datetime" && !($value instanceof \DateTime)){
                    //&& !($value instanceof DateTime)){
                    //echo $value;
                    $value = new \DateTime($value);
                }
                
                if ($column_length > 0) {
                    $value = substr($value, 0, $column_length);
                    if ($value === false) {
                        $value = "";
                    }
                }
                if (method_exists($element, $setter)) {
                    $element->$setter($value);
                }
                $manager->persist($element);
            }
            $manager->flush();
        }
        if ($action === "delete" || $action === "remove") {
            $exec_action = "deleted";
            $manager->remove($element);
            $manager->flush();
        }

        if ($action === "getall") {
            $data = $repository->findBy($c_conds, $c_order);
            $data = \EntityUtils::entityObjectsToArray($doctrine, $data);
            return new \Symfony\Component\HttpFoundation\JsonResponse(array(
                "data" => $data
            ));
        }

        return new \Symfony\Component\HttpFoundation\Response(json_encode(array(
                    "id" => $element->getId(),
                    "data" => \EntityUtils::entityObjectToArray($doctrine, $element),
                    "exec_action" => $exec_action,
                    "types" => $types
        )));
    }

}
