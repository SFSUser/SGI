<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Acme\SFSBundle;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Creates a Doctrine connection from attributes in the Request
 */
class DynamicDoctrineConnection {

    private $request;
    //private $defaultConnection;
    private $doctrine;

    public function __construct(Request $request, Registry $doctrine) {
        $this->request = $request;
        //$this->defaultConnection = $defaultConnection;
        $this->doctrine = $doctrine;
        //$this->onKernelRequest();

        if (getEnv('SERVER_NAME') !== "localhost") {
            $this->setupDB();
        }
        //echo "<h1>__construct</h1>";
    }

    public function setupDB() {

        $is_rhcloud = strpos(getEnv('SERVER_NAME'), 'rhcloud') !== false;
        $db_name = getEnv("OPENSHIFT_APP_NAME");
        $db_name_f = Controller\DefaultController::getDBNameFromDomain();
        if (!$is_rhcloud) {
            $db_name = $db_name_f;
        }
        Controller\DefaultController::changeDB($this->doctrine, $db_name);
    }

    public function onKernelRequest() {
        
    }

}
