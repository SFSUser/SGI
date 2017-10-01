<?php

class ImgurImage {

    static function checkGetArray(&$result) {
        if ($result == null | (count($result) <= 0)) {
            return Array();
        }
        return $result;
    }

    static function getParam($request, $value, $default) {
        if (!isset($request[$value]))
            return $default;
        return $request[$value];
    }

    static function jsonEncode($value) {
        return json_encode($value);
    }

    static function jsonDecode($text) {
        return json_decode($text, true);
    }

    var $predefined = true;
    var $json = "";
    var $object = "";
    var $delete_page = "";
    var $imgur_page = "";
    /*
      b	Big Square	160x160	No
      t	Small Thumbnail	160x160	Yes
      m	Medium Thumbnail	320x320	Yes
      h	Huge Thumbnail	1024x1024	Yes
     *                      */
    var $big_square = "http://i.imgur.com/xEEt9kWb.jpg";
    var $medium_thumbnail = "http://i.imgur.com/xEEt9kWt.jpg";
    var $small_thumbnail = "http://i.imgur.com/xEEt9kWm.jpg";
    var $huge_thumbnail = "http://i.imgur.com/xEEt9kWh.jpg";
    var $large_thumbnail = "http://i.imgur.com/xEEt9kWl.jpg";
    var $original = "http://i.imgur.com/xEEt9kW.jpg";
    var $small_square = "http://i.imgur.com/xEEt9kWs.jpg";
    var $animated = "";
    var $bandwidth = "";
    var $caption = "";
    var $datetime = "";
    var $deletehash = "";
    var $hash = "";
    var $height = "";
    var $name = "";
    var $size = "";
    var $title = "";
    var $type = "";
    var $views = "";
    var $width = "";
    var $x = "";
    var $y = "";
    var $w = "";
    var $h = "";
    var $style = "";
    var $others = "";

    //{"upload":{"links":{"original":"/img/pred_logo.png"},"jcrop":{"x":0,"y":0,"w":256,"h":256,"style":""}}}
    function getJcropImage($width, $height, $name, $others = "", $img_others = "") {
        $html .= sprintf('<div style="overflow:hidden; width: %s; height: %s;" id="prev_container_%s" %s>', $width, $height, $name, $others);
        $html .= sprintf('<img class="image-square" src="%s" id="image_preview_%s" style="%s" %s/>', $this->original, $name, $this->style, $img_others);
        $html .= '</div>';
        return $html;
    }

    /**
     * Convierte el codigo JSON que representa en una imagen en un objeto que facilita la consulta de sus propiedades
     * 
     * @param type $json
     * Codigo JSON que representa una sola imagen simple
     */
    function setJson($json) {
        $this->json = $json;
        $imgur = $this->object = self::jsonDecode($json);

        //echo "<h1>" . $imgur["upload"]["links"]["original"] . "</h1>";

        if (empty($imgur) || !isset($imgur["upload"]))
            return;

        if (isset($imgur["upload"]["links"]["imgur_page"])) {
            $this->predefined = false;
        }

        $this->delete_page = $imgur["upload"]["links"]["delete_page"];
        $this->imgur_page = $imgur["upload"]["links"]["imgur_page"];
        $this->large_thumbnail = self::getParam($imgur["upload"]["links"], "large_thumbnail", "/img/pred_logo.png");
        $this->original = self::getParam($imgur["upload"]["links"], "original", "/img/pred_logo.png");
        $this->small_square = self::getParam($imgur["upload"]["links"], "small_square", "/img/pred_logo.png");

        $image = explode(".", $this->original);
        if (!$this->predefined) {
            $url = $image[0] . "." . $image[1] . "." . $image[2];
            $this->small_square = $url . "s." . $image[3];
            $this->big_square = $url . "b." . $image[3];
            $this->small_thumbnail = $url . "t." . $image[3];
            $this->medium_thumbnail = $url . "m." . $image[3];
            $this->large_thumbnail = $url . "l." . $image[3];
            $this->huge_thumbnail = $url . "h." . $image[3];
            //Otros tamaños de la imagen
            /*
            $this->big_square = $image[0] . "b." . $image[1];
            $this->small_thumbnail = $image[0] . "t." . $image[1];
            $this->medium_thumbnail = $image[0] . "m." . $image[1];
            $this->huge_thumbnail = $image[0] . "h." . $image[1];
             */
        }

        $this->animated = $imgur["upload"]["image"]["animated"];
        $this->bandwidth = $imgur["upload"]["image"]["bandwidth"];
        $this->caption = $imgur["upload"]["image"]["caption"];
        $this->datetime = $imgur["upload"]["image"]["datetime"];
        $this->deletehash = $imgur["upload"]["image"]["deletehash"];
        $this->hash = $imgur["upload"]["image"]["hash"];
        $this->height = $imgur["upload"]["image"]["height"];
        $this->name = $imgur["upload"]["image"]["name"];
        $this->size = $imgur["upload"]["image"]["size"];
        $this->title = $imgur["upload"]["image"]["title"];
        $this->type = $imgur["upload"]["image"]["type"];
        $this->views = $imgur["upload"]["image"]["views"];
        $this->width = $imgur["upload"]["image"]["width"];


        if (isset($imgur["upload"]["jcrop"])) {
            $this->x = $imgur["upload"]["jcrop"]["x"];
            $this->y = $imgur["upload"]["jcrop"]["y"];
            $this->w = $imgur["upload"]["jcrop"]["w"];
            $this->h = $imgur["upload"]["jcrop"]["h"];
            $this->style = self::getParam($imgur["upload"]["jcrop"], "style", "width: 150px; height: 150px; margin-left: 0px; margin-top: 0px;");
        }
    }

    /**
     * Obtiene una imagen IMGUR a partir del resultado de una base de datos de registro
     * 
     * @param type $resource_item
     * Registro de la base de datos
     * 
     * @return \ImgurImage
     * Objeto IMGUR
     */
    static function getImage($resource_item) {
        $image = new ImgurImage($resource_item["value"]);
        return $image;
    }

    /**
     * Obtiene una imagen simple a partir de una cadena de texto que contiene el codigo JSON
     * 
     * @param type $json
     * Codigo JSON que representa la imagen
     * 
     * @return \ImgurImage
     * Objeto de la imagen
     */
    static function getImageJson($json) {
        $image = new ImgurImage($json);
        return $image;
    }

    /**
     * Obtiene la primera imagen de forma segura, obtiene una imagen predeterminada si no se encuentra.
     * 
     * @param type $json
     * String con codigo JSON que representa el array de imagenes
     */
    static function getFirstImageJson($json) {
        $imagenes = self::getImagesJson($json);
        if (count($imagenes) > 0) {
            return $imagenes[0];
        }
        return new ImgurImage();
    }

    /**
     * Obtiene una imagen de forma segura y aleatoria, obtiene una imagen predeterminada si no se encuentra.
     * 
     * @param type $json
     * String con codigo JSON que representa el array de imagenes
     */
    static function getRandomImageJson($json) {
        $imagenes = self::getImagesJson($json);
        if (count($imagenes) > 0) {
            return $imagenes[rand(0, count($imagenes) - 1)];
        }
        return new ImgurImage();
    }

    /**
     * Obtiene todas las imagenes asociadas a string con codigo JSON
     * 
     * @param type $json
     * Codigo JSON que contiene el array de imagenes
     * 
     * @return \ImgurImage
     * Objeto que representa el array de imagenes
     */
    static function getImagesJson($json) {
        $resource_items = self::jsonDecode($json);
        $resource_items = self::checkGetArray($resource_items);
        $images = array();
        foreach ($resource_items as $item) {
            $image = new ImgurImage(self::jsonEncode($item));
            if (!$image->predefined) {
                $images[] = $image;
            }
        }
        return $images;
    }

    /**
     * Obtiene imagenes desde el resultado multiple de una consulta a la base de datos de recursos
     * 
     * @param type $resource_items
     * Resultado de la base de datos de recursos
     * 
     * @return \ImgurImage
     * Array de objetos de imagenes IMGUR
     */
    static function getImages($resource_items) {
        $resource_items = self::checkGetArray($resource_items);
        $images = array();
        foreach ($resource_items as $item) {
            $images[] = new ImgurImage(json_encode($item));
        }
        return $images;
    }

    /**
     * Crea un objeto IMGUR a partir de un string con codigo JSON de una imagen simple
     * 
     * @param type $json
     * String de codigo JSON que representa una imagen
     */
    function ImgurImage($json = "{}") {
        $this->setJson($json);
    }

    function __toString() {
        return $this->original;
    }

}

?>
