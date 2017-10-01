<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;

class SchemaController extends Controller {

    public function addEvent($entity, $event_name, $id) {
        $usr = $this->get('security.context')->getToken()->getUser();
        $user_name = $usr->getUsername();

        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();
        $event = new \Acme\SFSBundle\Entity\SFSSchemaEvent();
        $event->setEntity($entity);
        $event->setAccount($user_name);
        $event->setEvent($event_name);
        $event->setIdentifier($id);
        $manager->persist($event);
        $manager->flush();
    }

    public static function getEntityElement($entity) {
        $parts = self::getEntityParts($entity);
        $entity_path = "\\Acme\\" . $parts["bundle"] . "Bundle\\Entity\\" . $parts["entity"];
        return new $entity_path();
    }

    public static function getEntityParts($entity) {
        $bundle = "";
        $entity_name = "";
        $entity_parts = explode(":", $entity);
        if (count($entity_parts) > 1) {
            $bundle = $entity_parts[0];
            $bundle = str_replace("Acme", "", $bundle);
            $bundle = str_replace("Bundle", "", $bundle);
            $entity_name = $entity_parts[1];
        }

        return array(
            "bundle" => $bundle,
            "entity" => $entity_name
        );
    }

    /**
     * @Route("/services/search/form", name="url_search_input")
     * @Template()
     */
    public function fieldSearchAction() {//$name, $entity, $key, $search_in) {
        $r = $this->getRequest();
        $name = $r->get("name");
        $entity = $r->get("entity");
        $key = $r->get("key");
        $search_in = $r->get("search_in", array());
        UtilsController::includeSFSUtil("EntityUtils");
        UtilsController::includeSFSUtil("Utils");
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository($entity);


        $entity_parts = self::getEntityParts($entity);
        $element = self::getEntityElement($entity);
        $entity_metadata = $doctrine->getManager()->getClassMetadata(get_class($element));

        //Columnas especificadas en el codigo
        $column_names = array();
        //Listado predeterminado de campos disponibles
        $column_names_allow = array();
        $column_names_final = array();

        foreach ($entity_metadata->fieldMappings as $value) {
            $col_name = $value["columnName"];
            $column_names[] = $col_name;
            switch ($col_name) {
                case "id":
                case "nombre":
                case "apellido":
                case "documento":
                case "nit":
                case "name":
                case "title":
                case "titulo":
                    $column_names_allow[] = $col_name;
                    break;
            }
        }

        foreach ($search_in as $x) {
            //\Utils::containsArrayInsensitive($x, $column_names)
            if (in_array($x, $column_names)) {
                $column_names_final[] = $x;
            }
        }

        if (empty($column_names_final)) {
            $column_names_final = $column_names_allow;
        }

        $token = \Utils::generateRandomPass(10);
        return $this->render("AcmeSFSBundle:Schema:field_search.html.twig", array(
                    "name" => $name,
                    "token" => $token,
                    "entity" => $entity,
                    "entity_name" => $entity_parts["entity"],
                    "bundle" => $entity_parts["bundle"],
                    "key" => $key,
                    "search_in" => $column_names_final
        ));
    }

    /**
     * @Route("/services/search/autocomplete", name="url_search_service")
     * @Template()
     */
    public function fieldSearchServiceAction() {
        UtilsController::includeSFSUtil("EntityUtils");
        UtilsController::includeSFSUtil("Utils");
        $entity = $this->getRequest()->get("entity", null);

        if ($entity === null) {
            return new \Symfony\Component\HttpFoundation\Response("{No entity}");
        }
        $entity_parts = explode(":", $entity);
        $entity_bundle = $entity_parts[0];
        $entity_name = "";
        if (count($entity_parts) > 1) {
            $entity_name = $entity_parts[1];
        }

        $search = $this->getRequest()->get("search", "");
        $id = $this->getRequest()->get("id", null);
        $key = $this->getRequest()->get("key", "id");
        $search_in = $this->getRequest()->get("search_in", array());
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        //$repo = $doctrine->getRepository($entity);
        $qb = $em->createQueryBuilder();

        $qb->select('e')->from($entity, 'e');
        //$conteo = 0;
        if ($id !== null) {
            $qb->where("e.$key = $id");
        } else if ($search !== "") {
            foreach ($search_in as $x) {
                //++$conteo;
                $qb->orWhere("e.$x like :param$x");
                if (is_numeric($search)) {
                    $qb->setParameter("param$x", "$search");
                } else {
                    $qb->setParameter("param$x", "%$search%");
                }
            }
        }
        $query = $qb->getQuery();
        $result = $query->getResult();
        //->orderBy('e.name', 'ASC')
        $data = array();

        foreach ($result as $x) {
            $label = \EntityUtils::getElementString($x);
            $getter = "get" . ucfirst($key);
            $id = 0;
            //echo $getter;
            if (method_exists($x, $getter)) {
                $id = $x->$getter();
            }
            $data[] = array(
                "label" => $label,
                "key" => $id
            );
        }

        if (empty($data)) {
            return new \Symfony\Component\HttpFoundation\Response("<b>No se obtuvieron resultados</b>");
        }

        $template_print = "AcmeSiteBundle:Schemas:$entity_name" . "_search.html.twig";
        //echo "<h1>'$template_print' - '$entity'</h1>";
        if (!$this->get('templating')->exists($template_print)) {
            $template_print = "AcmeSFSBundle:Schema:field_search_options.html.twig";
        }

        $ajax_value = \EntityUtils::entityObjectsToArray($this->getDoctrine(), $result);

        $html_out = $this->render($template_print, array(
                    "basic_data" => $data,
                    "data" => $result))->getContent();

        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            "html_out" => $html_out,
            "ajax_data" => $ajax_value
        ));

        //return new \Symfony\Component\HttpFoundation\JsonResponse(array(
        //    "result" => 1,
        //    "data" => $data
        //));
    }

    public function fieldImageAction($name, $image = "http://i.imgur.com/lY8kuJ4.png") {
        UtilsController::includeSFSUtil("Utils");
        $token = \Utils::generateRandomPass(10);
        return $this->render("AcmeSFSBundle:Schema:field_image.html.twig", array(
                    "name" => $name,
                    "token" => $token,
                    "image" => $image
        ));
    }

    public function fieldAllEntityOptionAction() {

        $fixed = array();
        $em = $this->getDoctrine()->getManager();
        $meta = $em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $x) {
            $parts = $x->getName();
            $parts = explode("\\", $parts);

            $entity = $parts[3];
            $bundle = str_replace("Bundle", "", $parts[1]);
            $name = "$bundle:" . $parts[3];
            $fixed[] = array(
                "label" => $name
            );
        }

        return $this->render("AcmeSFSBundle:Schema:field_options.html.twig", array(
                    "data" => $fixed
        ));
    }

    public function fieldOptionAction($entity, $label, $id = "id") {
        if ($id == null) {
            $el = self::getEntityElement($entity);
            $sd = array();
            if (method_exists($el, "getSearchData")) {
                $sd = $el->getSearchData();
            }
            if (isset($sd[$label])) {
                $sd = $sd[$label];
            } else {
                $sd = array();
            }
            return $this->render("AcmeSFSBundle:Schema:field_options_default.html.twig", array(
                        "data" => $sd
            ));
        }

        UtilsController::includeSFSUtil("EntityUtils");

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository($entity);
        $data = $repo->findAll();
        //$data = \EntityUtils::entityObjectsToArray($doctrine, $data);

        $fixed = array();

        foreach ($data as $x) {
            $id_t = $x->getId();
            $label_t = "(Sin etiqueta)";
            $method_label = "get" . ucfirst($label);
            $method_id = "get" . ucfirst($id);

            if (method_exists($x, $method_label)) {
                $label_t = $x->$method_label();
            } else if (method_exists($x, "__toString")) {
                $label_t = $x->__toString();
            }

            if (method_exists($x, $method_id)) {
                $id_t = $x->$method_id();
            }

            $fixed[$id_t] = $label_t;
        }

        return $this->render("AcmeSFSBundle:Schema:field_options_default.html.twig", array(
                    "data" => $fixed
        ));
    }

    /**
     * @Route("/schema/editor/", name="schema_editor")
     * @Template()
     */
    public function editorAction() {
        $export = $this->getRequest()->get("export", "query");
        $id = $this->getRequest()->get("id", 0) + 0;
        $bundle = $this->getRequest()->get("bundle");
        $conds = $this->getRequest()->get("conds");
        $entity = $this->getRequest()->get("entity");

        if (!$this->get('security.context')->isGranted("ROLE_ADMIN") && !$this->getUser()->hasRole("ROLE_$bundle" . "_$entity" . "_LECTURA")) {
            return new \Symfony\Component\HttpFoundation\Response("");
        }

        $entity2 = $this->getRequest()->get("entity2");
        $id2 = $this->getRequest()->get("id2");

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository("Acme$bundle" . "Bundle:$entity");
        $entity_path = "\\Acme\\$bundle" . "Bundle\\Entity\\$entity";

        $object_created = true;
        $element = $repo->find($id);
        if ($element === null) {
            $object_created = false;
            $element = new $entity_path();
        }

        $include_template = false;
        $include_template_name = "Acme$bundle" . "Bundle:Schemas:$entity.html.twig";
        if ($this->get('templating')->exists($include_template_name)) {
            $include_template = true;
        }
        $include_sub_template = false;
        $include_sub_template_name = "Acme$bundle" . "Bundle:Schemas:$entity" . "_sub.html.twig";
        if ($this->get('templating')->exists($include_sub_template_name)) {
            $include_sub_template = true;
        }

        $form_object = null;
        $form_type = "\\Acme\\$bundle" . "Bundle\\Form\\Type\\$entity" . "Type";
        if (class_exists($form_type)) {
            $form_object = new $form_type();
        } else {
            $form_object = new \Acme\SFSBundle\Form\Type\SFSFormType($this, $element);
        }
        $form = $this->createForm($form_object, $element);
        UtilsController::includeSFSUtil("EntityUtils");
        $element_object = $element;
        $element = \EntityUtils::entityObjectToArray($doctrine, $element);

        if ($export === "print") {
            //unset($element["columns"]);

            $template_print = "AcmeSiteBundle:Schemas:$entity" . "_print.html.twig";
            if (!$this->get('templating')->exists($template_print)) {
                $template_print = "AcmeSFSBundle:Schema:editor_print.html.twig";
            }

            return $this->render($template_print, array(
                        //"form" => $form->createView(),
                        "bundle" => $bundle,
                        "entity" => $entity,
                        //"entity2" => $entity2,
                        "id" => $id,
                        //"id2" => $id2,
                        //"conds" => $conds,
                        "element" => $element,
                        "element_object" => $element_object,
                        //"include_template" => $include_template,
                        //"include_template_name" => $include_template_name,
                        //"include_sub_template" => $include_sub_template,
                        //"include_sub_template_name" => $include_sub_template_name,
                        "$bundle$entity" . "_created" => $object_created,
                            //"element_created" => $object_created
            ));
        }

        return $this->render("AcmeSFSBundle:Schema:editor.html.twig", array(
                    "form" => $form->createView(),
                    "bundle" => $bundle,
                    "entity" => $entity,
                    "entity2" => $entity2,
                    "id" => $id,
                    "id2" => $id2,
                    "conds" => $conds,
                    "element" => $element,
                    "include_template" => $include_template,
                    "include_template_name" => $include_template_name,
                    "include_sub_template" => $include_sub_template,
                    "include_sub_template_name" => $include_sub_template_name,
                    "$bundle$entity" . "_created" => $object_created,
                    "element_created" => $object_created
        ));
    }

    /**
     * @Route("/schema/list", name="schema_list")
     * @Template()
     */
    public function listAction($bundle, $entity, $conds = array(), $ress = array(), $tabular = true) {

        $entity2 = $this->getRequest()->get("entity2");
        $id2 = $this->getRequest()->get("id2");

        if (!$this->get('security.context')->isGranted("ROLE_ADMIN") && !$this->getUser()->hasRole("ROLE_$bundle" . "_$entity" . "_LECTURA")) {
            return new \Symfony\Component\HttpFoundation\Response("");
        }

        $doctrine = $this->getDoctrine();
        $entity_path = "\\Acme\\$bundle" . "Bundle\\Entity\\$entity";
        $element = new $entity_path();
        $entity_metadata = $doctrine->getManager()->getClassMetadata(get_class($element));

        $columns = array();

        $assoc_list = array();
        foreach ($entity_metadata->associationMappings as $value) {
            $rel_entity = $value["targetEntity"];
            if (isset($value["targetToSourceKeyColumns"])) {
                foreach ($value["targetToSourceKeyColumns"] as $column) {
                    $assoc_list[$column] = $rel_entity;
                }
            }
            //$columnName = $value["columnName"];
            //echo "<pre><code>";
            //print_r($value);
            //echo "</code></pre>";
        }

        foreach ($entity_metadata->fieldMappings as $value) {

            $column_type = $value["type"];
            $column_name = $value["columnName"];
            $column_nullable = $value["nullable"];
            $column_length = $value["length"];
            $search_data = array();
            $related_entity = null;
            if (isset($assoc_list[$column_name])) {
                $related_entity = self::reformatEntity($assoc_list[$column_name]);
            }

            if (method_exists($element, "getSearchData")) {
                $data = $element->getSearchData();
                if (isset($data[$column_name])) {
                    $search_data = $data[$column_name];
                }
            }
            $columns[] = array(
                "name" => $column_name,
                "type" => $column_type,
                "search_data" => $search_data,
                "entity" => $related_entity
            );
        }

        $entity2_name = explode(":", $entity2);
        if (count($entity2_name) > 1) {
            $entity2_name = $entity2_name[1];
        }

        return $this->render("AcmeSFSBundle:Schema:list.html.twig", array(
                    "bundle" => $bundle,
                    "entity" => $entity,
                    "entity2" => $entity2,
                    "entity2_name" => $entity2_name,
                    "id2" => $id2,
                    "columns" => $columns,
                    "conds" => $conds,
                    "ress" => $ress,
                    "tabular" => $tabular
        ));
    }

    /**
     * @Route("/schema/list/get", name="get_schema_list")
     */
    public function ajaxlistAction() {
        UtilsController::includeSFSUtil("Utils");
        UtilsController::includeSFSUtil("EntityUtils");
        UtilsController::includeSFSUtil("PaginatorUtils");

        $export = $this->getRequest()->get("export", "query");
        $order = $this->getRequest()->get("order", array());
        $conds = $this->getRequest()->get("conds", array());
        $ress = $this->getRequest()->get("ress", array());
        $filters = $this->getRequest()->get("filter", array());
        $bundle = $this->getRequest()->get("bundle", "");
        $entity = $this->getRequest()->get("entity", "");
        $entity2 = $this->getRequest()->get("entity2", "");
        $id2 = $this->getRequest()->get("id2", 0) + 0;
        $repo_name = "Acme$bundle" . "Bundle:$entity";
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository($repo_name);

        $data = null;
        $data_joined = null;
        if ($entity2 !== "") {
            $result = null;
            if ($entity2 !== "" && $id2 > 0) {
                $repo2 = $doctrine->getRepository($entity2);
                $result = $repo2->find($id2);
                //echo 'gets!';
            }
            if ($result !== null) {
                $entity_metadata = $doctrine->getManager()->getClassMetadata(get_class($result));
                foreach ($entity_metadata->associationMappings as $value) {
                    if (isset($value["joinTable"])) {
                        //TestEmpesa
                        $sourceEntity = $value["sourceEntity"];
                        $sourceEntity = self::reformatEntity($sourceEntity);
                        //Testuser
                        $targetEntity = $value["targetEntity"];
                        $targetEntity = self::reformatEntity($targetEntity);

                        $fieldName = $value["fieldName"];
                        //echo "Adedd! $fieldName, orign $sourceEntity target $targetEntity";
                        $data_joined = $result->$fieldName; //->add($element);
                        /*
                          $repo_source = $sourceEntity;

                          foreach ($value["joinTable"] as $x) {

                          } */
                    }
                }
            }
        }

        //Obtener los datos

        $qb = $doctrine->getManager()->createQueryBuilder();
        //$qb = new \Doctrine\DBAL\Query\QueryBuilder();
        $qb->select("e");
        $qb->from($repo_name, "e");



        foreach ($order as $key => $value) {
            $key = \Utils::underscoreToCamelCase($key, false);
            $qb->orderBy("e.$key", $value);
        }

        //Aplica las condiciones
        $param_count = 0;
        foreach ($conds as $key => $value) {
            $key = \Utils::underscoreToCamelCase($key, false);
            ++$param_count;
            $qb->andWhere("e.$key = ?$param_count");
            $qb->setParameter($param_count, $value);
        }

        //Aplica los filtros correspondientess.
        foreach ($filters as $column => $filter) {
            $column = \Utils::underscoreToCamelCase($column, false);
            foreach ($filter as $filter_name => $options) {
                ++$param_count;
                switch ($filter_name) {
                    case "search":
                        $qb->andWhere("e.$column like ?$param_count");
                        $qb->setParameter($param_count, "%$options%");
                        ++$param_count;
                        /* $qb->orWhere("e.$column like ?$param_count");
                          $qb->setParameter($param_count, "%$options");
                          ++$param_count;
                          $qb->orWhere("e.$column like ?$param_count");
                          $qb->setParameter($param_count, "$options%");
                          ++$param_count;
                          $qb->orWhere("e.$column like ?$param_count");
                          $qb->setParameter($param_count, "$options"); */
                        break;
                    case "mayor":
                        ++$param_count;
                        $qb->andWhere("e.$column > ?$param_count");
                        $qb->setParameter($param_count, $options);

                        break;
                    case "minor":
                        ++$param_count;
                        $qb->andWhere("e.$column < ?$param_count");
                        $qb->setParameter($param_count, $options);

                        break;
                    case "equal":
                        //Filtro para verdadero y falso, no falla pues debe ser entero o fecha
                        //Si no es asi fallaría.
                        if ($options === "true" || $options === "false") {
                            $options = $options === "true";
                        }
                        ++$param_count;
                        $qb->andWhere("e.$column = ?$param_count");
                        $qb->setParameter($param_count, $options);

                        break;
                    case "between":
                        if (!isset($options["first"]) || !isset($options["last"])) {
                            return;
                        }
                        $first = $options["first"];
                        $last = $options["last"];
                        ++$param_count;
                        $qb->andWhere("e.$column >= ?$param_count AND e.$column <= ?" . ($param_count + 1));
                        $qb->setParameter($param_count, $first);
                        ++$param_count;
                        $qb->setParameter($param_count, $last);
                        break;
                }
            }
        }

        //Paginador
        $paginator = new \PaginatorUtils($this);
        $paginator->callback = "SchemaList_$bundle" . "$entity.paginate";
        $paginator_buttons = $paginator->getButtonList(clone $qb, "e");
        $paginator->addPaginatedQuery($qb);
        $data_object = $qb->getQuery()->getResult();

        $sel_str = "COUNT(e.id) AS conteo_ingnorar,";
        foreach ($ress as $key => $value) {
            $column = \Utils::underscoreToCamelCase($key, false);
            switch ($value) {
                case "sum":
                    $sel_str .= "SUM(e.$column) AS $key,";
                    break;
                case "avg":
                    $sel_str .= "AVG(e.$column) AS $key,";
                    break;
            }
        }
        $qb->select(rtrim($sel_str, ","));
        $qb->setFirstResult(0);
        $qb->setMaxResults(null);
        $ress_data = $qb->getQuery()->getResult();
        if (count($ress_data) > 0) {
            $ress_data = $ress_data[0];
        } else {
            $ress_data = array();
        }


        //$data = $repo->findBy($conds, $order);
        //Obtener despues los botones de paginado, ya que afecta la consulta

        $data = \EntityUtils::entityObjectsToArray($doctrine, $data_object);

        if ($data_joined === null) {
            $data_joined = array();
        }

        $data_joined = \EntityUtils::entityObjectsToArray($doctrine, $data_joined);

        //En esta parte se quitan los elementos añadidos del resultado original
        $data_final = array();
        foreach ($data as &$x) {
            $in = false;
            foreach ($data_joined as $y) {
                if ($x["id"] === $y["id"]) {
                    $in = true;
                }
            }
            $x["joined"] = $in;
            if (!$in) {
                $data_final[] = $x;
            }
        }

        if ($export === "excel") {
            $response = new \Symfony\Component\HttpFoundation\Response();

            $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', "attachment; filename=$entity.xls");
            $response->send();

            $text = \EntityUtils::getExcelText($data);

            return new \Symfony\Component\HttpFoundation\Response($text);
        }
        if ($export === "print") {
            $template_print = "AcmeSiteBundle:Schemas:$entity" . "_list_print.html.twig";
            if (!$this->get('templating')->exists($template_print)) {
                $template_print = "AcmeSFSBundle:Schema:list_print.html.twig";
            }
            return $this->render($template_print, array(
                        "bundle" => $bundle,
                        "entity" => $entity,
                        "entity2" => $entity2,
                        "id2" => $id2,
                        "data" => $data,
                        "element_objects" => $data_object,
                        "data_joined" => $data_joined,
                        "resultados" => $ress_data
            ));
        }

        return $this->render("AcmeSFSBundle:Schema:list_query.html.twig", array(
                    "buttons" => $paginator_buttons,
                    "bundle" => $bundle,
                    "entity" => $entity,
                    "entity2" => $entity2,
                    "id2" => $id2,
                    "data" => $data,
                    "data_joined" => $data_joined,
                    "resultados" => $ress_data
        ));
    }

    public static function reformatEntity($entity) {
        $entity = str_replace("\\Entity\\", ":", $entity);
        $entity = str_replace("\\", "", $entity);
        return $entity;
    }

    public static function manytomany($doctrine, &$element, $entity2, $id2, $add = true) {
        //Si no hay ninguna entidad relacionada, ignorar el resto de siguiente código
        $result = null;
        if ($entity2 !== "" && $id2 > 0) {
            $repo2 = $doctrine->getRepository($entity2);
            $result = $repo2->find($id2);
            //echo 'gets!';
        }
        if ($result !== null) {
            $entity_metadata2 = $doctrine->getManager()->getClassMetadata(get_class($result));
            foreach ($entity_metadata2->associationMappings as $value) {
                if (isset($value["joinTable"])) {
                    //TestEmpesa
                    $sourceEntity = $value["sourceEntity"];
                    $sourceEntity = self::reformatEntity($sourceEntity);
                    //Testuser
                    $targetEntity = $value["targetEntity"];
                    $targetEntity = self::reformatEntity($targetEntity);

                    $fieldName = $value["fieldName"];
                    //echo "Adedd! $fieldName, orign $sourceEntity target $targetEntity";
                    if ($add) {
                        if (!$result->$fieldName->contains($element)) {
                            $result->$fieldName->add($element);
                        }
                    } else {
                        //$copy = $result->$fieldName->clear();
                        //$result->$fieldName = new \Doctrine\Common\Collections\ArrayCollection();
                        $result->$fieldName->removeElement($element);
                        //foreach($copy as $value){
                        //    $result->$fieldName->add($value);
                        //}
                    }
                    /*
                      $repo_source = $sourceEntity;

                      foreach ($value["joinTable"] as $x) {

                      } */
                }
            }
        }
    }

    /**
     * @Route("/schema/ajax/join/editor", name="schema_ajaxjoineditor")
     * @Template()
     */
    public function ajaxjoineditorAction() {
        $request = $this->getRequest();
        $id = $request->get("id", 0) + 0;
        $entity2 = $request->get("entity2", "");
        $id2 = $request->get("id2", "");
        $bundle = $request->get("bundle", "");
        $entity = $request->get("entity", "");
        $action = $request->get("action", "add");

        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();
        $repository = $doctrine->getRepository("Acme$bundle" . "Bundle:$entity");

        $element = $repository->find($id);

        if ($element == null) {
            return new \Symfony\Component\HttpFoundation\JsonResponse(array(
                "result" => 0
            ));
        }

        switch ($action) {
            case "add":
                self::manytomany($doctrine, $element, $entity2, $id2);
                break;
            case "remove":
                self::manytomany($doctrine, $element, $entity2, $id2, false);
                break;
        }

        $manager->flush();

        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            "result" => 1,
            "action" => $action,
            "id" => $id,
            "id2" => $id2,
            "entity" => $entity,
            "entity2" => $entity2
        ));
    }

    /**
     * @Route("/schema/ajax/editor", name="schema_ajaxeditor")
     * @Template()
     */
    public function ajaxeditorAction() {
        $request = $this->getRequest();
        $id = $request->get("id", 0) + 0;
        $entity2 = $request->get("entity2", "");
        $id2 = $request->get("id2", "");

        $bundle = $request->get("bundle", "");
        $entity = $request->get("entity", "");
        $action = $request->get("action", "get");
        $c_order = $request->get("consult_order", array("id" => "ASC"));
        $c_conds = $request->get("consult_conditions", array());
        $action = strtolower($action);
        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();
        $entity_full_name = "Acme$bundle" . "Bundle:$entity";
        $repository = $doctrine->getRepository($entity_full_name);
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
            UtilsController::includeSFSUtil("Utils");

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
                //echo "<h1>($column_name) Get:$value</h1>";

                if (strstr($column_type, "date") !== FALSE && !($value instanceof \DateTime)) {
                    $value = new \DateTime($value);
                }
                if ($column_type === "boolean" && !is_bool($value)) {
                    $value = strtolower($value) == "true" ? true : false;
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
            }

            //Obtener todas las asocianes con otras tablas
            foreach ($entity_metadata->associationMappings as $value) {

                //Formatear la ruta del entity para obtener el repositorio.
                $entity = $value["targetEntity"];
                $entity = self::reformatEntity($entity);

                $repo = $doctrine->getRepository($entity);
                //El nombre del campo de la entidad actual.
                $fieldName = $value["fieldName"];
                //echo"<pre><code>";print_r($value);echo "</code></pre>";
                //Procesar Columnas Unidas directamente.
                if (isset($value["joinColumns"])) {
                    foreach ($value["joinColumns"] as $x) {
                        //echo"<pre><code>";print_r($x);echo "</code></pre>";

                        $setter_column = "set" . \Utils::underscoreToCamelCase($x["name"]);
                        //echo "|$joined_column : " . $x["name"] . "|";
                        $id_join = $request->get($x["name"], $x["name"]); //$element->$joined_column();
                        $referenced_column = \Utils::underscoreToCamelCase($x["referencedColumnName"]);
                        $res = $repo->findOneBy(array($x["referencedColumnName"] => $id_join));
                        //echo "<h1>$setter_column FOUND:" . $fieldName . ($res !== null ? "SI" : "NO") . ":$id_join</h1>";
                        //echo "|" . $res->getId() . "|";
                        $element->$fieldName = $res;
                        //$element->$setter_column($res->getId());
                    }
                }

                //Procesar Tabla Unida: (Many to Many)

                /**/
                //$element->$fieldName = null;
            }

            //////////aca esaabdbasdhajsbhdasd
            self::manytomany($doctrine, $element, $entity2, $id2);

            //echo "<h1>Before: " . $element->getConcepto() ." AND " . ($element->refconcepto->getNombre()) . "</h1>";



            $manager->persist($element);
            $manager->flush();
        }

        $last_id = $element->getId();

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

        $this->addEvent($entity_full_name, $exec_action, $last_id);

        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            "id" => $last_id,
            "data" => \EntityUtils::entityObjectToArray($doctrine, $element),
            "exec_action" => $exec_action,
            "types" => $types
        ));
    }

    /*     * *************************** GENERAL EDITOR *********************************** */

    /**
     */
    public function addpageAction($bundle, $entity) {
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("EditorManager");
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("Utils");

        //$doctrine = $this->getDoctrine();
        $params = array(
            //"categorias" => self::getCategories($doctrine),
            //"tipos" => self::getTipos($doctrine),
            "section" => "add"
        );

        self::callEvent($this, $entity, $this->getRequest()->get("id"), "onModifyEditor");

        $class_name = "\\Acme\\" . $bundle . "Bundle\\Entity\\$entity";
        $producto = new $class_name();

        return \EditorManager::proccess(
                        $this, $bundle, $entity, $producto, $params);
    }

}
