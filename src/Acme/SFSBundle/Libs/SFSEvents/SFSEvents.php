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
class SFSEvents {
    public static function callContactEvent($c, $data){
        $class = "\\Acme\\SiteBundle\\Controller\\DefaultController";
        if (class_exists($class)) {
            $default = new $class();
            if (method_exists($default, "onContact")) {
                $default->onContact($c, $data);
            }
        }
        
    }
    public static function callEntityEvent($controller, $entity, $id, $function_name) {
        $class = "\\Acme\\SiteBundle\\Controller\\DefaultController";
        if (class_exists($class)) {
            $default = new $class();
            if (method_exists($default, $function_name)) {
                $default->$function_name($controller, $entity, $id, $function_name);
            }
        }
    }
}
