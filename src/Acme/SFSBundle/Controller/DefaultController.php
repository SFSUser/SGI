<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\RedirectResponse;
// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

class DefaultController extends Controller {

    public function indexAction() {
        return $this->render('AcmeSFSBundle:Default:index.html.twig');
    }

    public static $first_dbname = "";
    public static $last_dbname = "";

    public static function changeDB($doctrine, $db) {
        $dcn = $doctrine->getDefaultConnectionName();
        $con = $doctrine->getManager()->getConnection();
        $con->close();
        $reflectionConn = new \ReflectionObject($con);
        $reflectionParams = $reflectionConn->getProperty('_params');
        $reflectionParams->setAccessible(true);
        $params = $reflectionParams->getValue($con);
        if (self::$first_dbname === "") {
            self::$first_dbname = $params['dbname'];
        }
        self::$last_dbname = $params['dbname'] = $db;
        $reflectionParams->setValue($con, $params);
        $reflectionParams->setAccessible(false);
        $doctrine->resetManager($dcn);
    }

    public static function getDBNameFromDomain() {
        $db_name_f = getEnv("SERVER_NAME");
        $db_name_f = strtolower($db_name_f);
        $db_name_f = strtr($db_name_f, array(
            '.' => '_',
            '-' => '_',
        ));
        return $db_name_f;
    }

    /**
     * @Route("/create_schema", name="_create_schema")
     */
    public function createschemaAction() {
        $db_name = self::getDBNameFromDomain();

        $txt_log = "<h1>Base de datos creada...</h1>"
                . "<b>Database original:</b> " . self::$first_dbname . "<br>"
                . "<b>Changed database:</b> " . self::$last_dbname . "<br>"
                . "<hr>"
                . "<b>Próximo paso:</b> <a href='/create_entities'>Generar entidades</a>";

        self::changeDB($this->getDoctrine(), self::$first_dbname);
        if ($db_name !== "") {
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->exec('CREATE DATABASE IF NOT EXISTS `' . $db_name . '`;');
        }

        return new \Symfony\Component\HttpFoundation\Response($txt_log);
    }

    /**
     * @Route("/create_entities")
     * @Route("/create_db", name="_create_db")
     */
    public function createdbAction() {
        $kernel = $this->container->get('kernel');
        $app = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);

        //$db_name = self::getDBNameFromDomain();

        $input = new \Symfony\Component\Console\Input\StringInput('doctrine:schema:update --force ');
        $output = new \Symfony\Component\Console\Output\StreamOutput(fopen('php://temp', 'w'));

        $app->doRun($input, $output);

        rewind($output->getStream());
        $response = stream_get_contents($output->getStream());

        return new \Symfony\Component\HttpFoundation\Response("<h1>Entidades actualizadas...</h1>"
                . "<b>Database original:</b> " . self::$first_dbname . "<br>"
                . "<b>Changed database:</b> " . self::$last_dbname . "<br>"
                . "<hr><pre><code>" . $response . "</code></pre>");
    }

    /**
     * @Route("/install_assets", name="_install_assets")
     */
    public function assetsAction() {
        $kernel = $this->container->get('kernel');
        $app = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);

        $input = new \Symfony\Component\Console\Input\StringInput('assets:install');
        $output = new \Symfony\Component\Console\Output\StreamOutput(fopen('php://temp', 'w'));

        $app->doRun($input, $output);

        rewind($output->getStream());
        $response = stream_get_contents($output->getStream());

        return new \Symfony\Component\HttpFoundation\Response("Parece que ya <br><br><pre><code>" . $response . "</code></pre>");
    }

    /**
     * @Route("/openshift", name="openshift_url")
     */
    public function mysqlAction() {
        //$var = getEnv("OPENSHIFT_MYSQL_DB_HOST") . "";

        $html = "";
        //$html .= "<b>Select DB</b>: " . $this->getDoctrine()->getConnection()->get . "<br>";
        $html .= "<b>SSH Hostname</b>: " . getEnv("OPENSHIFT_APP_DNS") . "<br>";
        $html .= "<b>SSH Username</b>: " . getEnv("OPENSHIFT_GEAR_UUID") . "<br>";
        $html .= "<b>MySQL Hostname</b>: " . getEnv("OPENSHIFT_MYSQL_DB_HOST") . "<br>";
        $html .= "<b>MySQL Server Port</b>: " . getEnv("OPENSHIFT_MYSQL_DB_PORT") . "<br>";
        $html .= "<b>MySQL Username</b>: " . getEnv("OPENSHIFT_MYSQL_DB_USERNAME") . "<br>";
        $html .= "<b>MySQL Password</b>: " . getEnv("OPENSHIFT_MYSQL_DB_PASSWORD") . "<br>";
        $html .= "<b>MySQL Database</b>: " . getEnv("OPENSHIFT_APP_NAME") . "<br>";
        //$html .= "<b>MI IP</b>: " . \Utils::getRealRemoteIP() . "<br>"; //$this->getRequest()->getHttpHeader ('addr','remote');
        //$html .= "<b>MI IP</b>: " . $this->getRequest()->getHttpHeader('addr', 'remote') . "<br>"; //$this->getRequest()->getHttpHeader ('addr','remote');

        return new \Symfony\Component\HttpFoundation\Response($html);
    }

}
