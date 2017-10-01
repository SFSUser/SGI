<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EntityUtils
 *
 * @author SFS
 */
class EntityUtils {

    public static function convertArrayFromCollection($el) {
        $elements = array();
        foreach ($el as $x) {
            $elements[] = $x;
        }
        return $elements;
    }

    public static function getExcelTextFromResultSet($data) {
        $column = "";
        $text = "";
        $first = true;
        foreach ($data as $x) {
            foreach ($x as $k => $v) {
                if ($first) {
                    $k = str_replace("_", " ", $k);
                    $column .= $k . "\t";
                }
                $text .= $v . "\t";
            }
            $first = false;
            $text .= "\r";
        }
        $text = $column . "\r" . $text;
        return $text;
    }

    public static function getExcelText($data) {
        $text = "";
        $first = "";
        foreach ($data as $x) {
            foreach ($x["columns"] as $column) {
                if ($first !== null) {
                    $first .= $column["column_name"] . "\t";
                }
                $text .= $column["list_formatted"] . "\t";
            }
            if ($first !== null) {
                $text = $first . "\n" . $text;
                $first = null;
            }
            $text .= "\n";
        }
        return $text;
    }

    public static function getEntityElement($doctrine, $entity, $id) {
        $repo = $doctrine->gerRepository($entity);
        return $repo->find($id);
    }

    public static function entityObjectsToArray($doctrine, $array) {
        $elements = array();
        foreach ($array as $value) {
            $elements[] = self::entityObjectToArray($doctrine, $value);
        }
        return $elements;
    }

    public static function getElementString($element, $pred = "(Elemento sin nombre)") {
        $field = $element;
        $ref_name = "";
        if (method_exists($field, "__toString")) {
            $ref_name = $field->__toString();
        } else if (method_exists($field, "getNombre")) {
            $ref_name = $field->getNombre();
        } else if (method_exists($field, "getName")) {
            $ref_name = $field->getName();
        } else if (method_exists($field, "getTitulo")) {
            $ref_name = $field->getTitulo();
        } else if (method_exists($field, "getTitle")) {
            $ref_name = $field->getTitle();
        } else {
            $ref_name = $pred;
        }
        return $ref_name;
    }

    /**
     * 
     * @param type $doctrine
     * Elemento doctrine para obtener informacion de las clases
     * @param type $element
     * Elemento retornado de una consulta el cual se va a procesar
     * @return array
     * Regresa un arreglo con datos enriquecidos
     */
    public static function entityObjectToArray($doctrine, $element) {
        if ($element === null || empty($element)) {
            return array();
        }
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("ImgurImage");
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("Utils");
        $entity_metadata = $doctrine->getManager()->getClassMetadata(get_class($element));

        $values = array();
        $columns = array();
        foreach ($entity_metadata->fieldMappings as $value) {
            $column_type = $value["type"];
            $column_name = $value["columnName"];
            $column_nullable = $value["nullable"];
            $column_length = $value["length"];
            $getter = "get" . ucwords(Utils::underscoreToCamelCase($column_name));
            if (method_exists($element, $getter)) {
                $column_value = $element->$getter();
            }
            ///if($column_value instanceof DateTime){
            //    $column_value = $column_value->format("Y-m-d H:i:s");
            //}
            $values[$column_name] = $column_value;
            $tostring = $column_value;
            $totable = $tostring;

            switch ($column_type) {
                case "boolean":
                    $totable = $tostring = $values[$column_name . "_formatted"] = $column_value > 0 ? "Si" : "No";
                    break;

                case "integer":
                    $imag_field = "ref$column_name";
                    $ref_name = $column_value;
                    //echo "<tr><td>$imag_field</td></tr>";
                    if (isset($element->$imag_field)) {
                        $field = $element->$imag_field;
                        $ref_name = self::getElementString($field, $ref_name);
                        /*
                          if (method_exists($field, "__toString")) {
                          $ref_name = $field->__toString();
                          } else if (method_exists($field, "getNombre")) {
                          $ref_name = $field->getNombre();
                          } else if (method_exists($field, "getName")) {
                          $ref_name = $field->getName();
                          } else if (method_exists($field, "getTitulo")) {
                          $ref_name = $field->getTitulo();
                          } else if (method_exists($field, "getTitle")) {
                          $ref_name = $field->getTitle();
                          } */
                    }
                    if ($ref_name !== $column_value) {
                        //$ref_name = "$column_value - " . $ref_name;
                        $ref_name = $ref_name;
                    }
                    $totable = $tostring = $ref_name;
                    break;
            }
            if (strstr($column_name, "descrip") || strstr($column_name, "conten")) {
                $first_video = Utils::extractFirstImageURL($column_value);
                $first_image = Utils::extractFirstVideo($column_value);
                if ($first_video != null) {
                    $values[$column_name . "_first_video"] = $values[$column_name . "_first_media"] = $first_video;
                }
                if ($first_image != null) {
                    $values[$column_name . "_first_image"] = $values[$column_name . "_first_media"] = $first_image;
                }
            }

            if ((strstr($column_name, "date")) || $column_value instanceof DateTime || strstr($column_name, "time") || strstr($column_name, "fecha")) {
                //$column_value = new \DateTime();
                //$column_value->
                $values[$column_name . "_friendly"] = Utils::getFriendlyTimeFromDatetime($column_value);
                $values[$column_name . "_formatted"] = $column_value->format('c');
                $values[$column_name . "_formatted_sql"] = $column_value->format('c');
                $values[$column_name . "_date_timeline"] = $column_value->format('Y,m,d');
                $values[$column_name . "_date_sql"] = $column_value->format('Y-m-d');

                //Hora real.
                if ($column_type === "datetime") {
                    $date = $column_value;
                    //$date->setTimezone(new \DateTimeZone("America/Bogota"));
                    $tostring = $totable = $date->format('d/m/Y, h:i:s a');
                } else {
                    $totable = $column_value->format('d/m/Y');
                    $tostring = $column_value->format('Y-m-d');
                }
                $values[$column_name . "_date"] = $column_value->format('d/m/Y');
                $values[$column_name . "_time"] = $column_value->format('H:i:s');
                $values[$column_name . "_year"] = $column_value->format("Y");
                $values[$column_name . "_month"] = $column_value->format("m");
                $values[$column_name . "_day"] = $column_value->format("d");
                $values[$column_name . "_hour"] = $column_value->format("H");
                $values[$column_name . "_minute"] = $column_value->format("i");
                $values[$column_name . "_second"] = $column_value->format("s");
            }

            switch ($column_name) {
                case "archivo":
                case "archivos":
                case "file":
                case "files":
                case "tags":
                    $values[$column_name . "_array"] = \Utils::jsonDecode($column_value);
                    break;
                case "images":
                case "imagenes":
                    $images_json = ImgurImage::getImagesJson($column_value);
                    $images_json_first = ImgurImage::getFirstImageJson($column_value);
                    $images_json_random = ImgurImage::getRandomImageJson($column_value);

                    $values[$column_name . "_array"] = json_decode($column_value);
                    $values[$column_name . "_all"] = $images_json;
                    $tostring = $values[$column_name . "_first"] = $images_json_first;
                    $values[$column_name . "_random"] = $images_json_random;
                    $totable = $images_json_first->small_square; //'<img src="' . $images_json_first->small_square . '"/>';
                    break;
                case "image":
                case "imagen":
                    /*
                      s	Small Square	90x90	No
                      b	Big Square	160x160	No
                      t	Small Thumbnail	160x160	Yes
                      m	Medium Thumbnail	320x320	Yes
                      l	Large Thumbnail	640x640	Yes
                      h	Huge Thumbnail	1024x1024	Yes
                     *                      */
                    if (strstr($column_value, "imgur")) {
                        $image = explode(".", $column_value);
                        //i.imgur.com/asdasd.gif
                        $url = $image[0] . "." . $image[1] . "." . $image[2];
                        $small_square = $url . "s." . $image[3];
                        $big_square = $url . "b." . $image[3];
                        $small_thumbnail = $url . "t." . $image[3];
                        $medium_thumbnail = $url . "m." . $image[3];
                        $large_thumbnail = $url . "l." . $image[3];
                        $huge_thumbnail = $url . "h." . $image[3];


                        $tostring = $values[$column_name . "_original"] = $column_value;
                        $values[$column_name . "_small_square"] = $small_square;
                        $values[$column_name . "_big_square"] = $big_square;
                        $values[$column_name . "_small_thumbnail"] = $small_thumbnail;
                        $values[$column_name . "_medium_thumbnail"] = $medium_thumbnail;
                        $values[$column_name . "_large_thumbnail"] = $large_thumbnail;
                        $values[$column_name . "_huge_thumbnail"] = $huge_thumbnail;
                        $totable = $column_value; //'<img src="' . $small_square . '"/>';
                    }
                    break;
                //Otros tipos...
                default:
                case "":
                    $totable = Utils::getSizedString($tostring, 600);
                    break;
            }

            $columns[] = array(
                "column_name" => $column_name,
                "string_formatted" => $tostring,
                "list_formatted" => strip_tags($totable, "<img>"),
                "value" => $column_value
            );
            $values["columns"] = $columns;
        }
        return $values;
    }

    public static function queryUnicValues($doctrine, $entity, $column) {
        $unics = array();
        $result = $doctrine->getManager()->createQuery("SELECT c.$column FROM $entity c GROUP BY c.$column")->getResult();
        foreach ($result as $value) {
            //echo "<hr>" . $value[$column];
            $unics[] = $value[$column];
        }
        return $unics;
    }

    public static function removeElement(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine, $entity, $id) {
        $element = $doctrine->getRepository($entity)->find($id);
        if ($element == null) {
            return array("results" => 0);
        }
        $manager = $doctrine->getManager();
        $manager->remove($element);
        $manager->flush();
        return array("results" => 1);
    }

    public static function sitemapQuery(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine, $entity, $title_field = "") {
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("Utils");

        $url_title = "";
        if ($title_field != "") {
            $url_title = ", c.$title_field ";
        }
        $result = $doctrine->getManager()->createQuery("SELECT c.id $url_title FROM $entity c")->getScalarResult();

        if ($title_field != "") {
            $returns = array();
            foreach ($result as $value) {
                //echo $value["$title_field"] . "<br>";
                $value["$title_field"] = \Utils::getSanitizedFileName($value["$title_field"]);
                $returns[] = $value;
            }
            return $returns;
        }
        //print_r($result);
        return $result;
    }

}
