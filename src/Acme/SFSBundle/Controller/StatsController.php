<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class StatsController extends Controller {

    private $profile_id = "73649671";
    private $email = "sfsusuario@gmail.com";
    private $password = "92111616401";
    
    public function getProfileId() {
        $session = $this->getRequest()->getSession();
        return $this->profile_id; //$_SESSION["ga_url"];
    }

    public function getGa() {
        //require_once __DIR__ . '\..\Libs\stats\gapi.php';
        UtilsController::includeSFSUtil("gapi");
        
        $session = $this->getRequest()->getSession();
        $ga_email = $this->email; // $_SESSION["ga_email"];
        $ga_password = $this->password; //$_SESSION["ga_password"];
        //$ga_profile_id = $session->get("profile_id", "73649671"); //ACA   //$_SESSION["ga_profile_id"];
        //$ga_url = $session->get("url", "/analytics"); //$_SESSION["ga_url"];
        $ga = null;
        try {
            $ga = new \gapi($ga_email, $ga_password);
        } catch (\Exception $x) {
            //$content = "<h2>Ocurrió un error al validar la cuenta de Google Analytics.</h2>";
        }
        return $ga;
    }

    public function consultBrowsersChart() {
        $result = $this->getGa()->requestReportData($this->getProfileId(), array('browser'), array('visitors'));
        $values = array();
        foreach ($result as $x) {
            $values[] = array(
                $x->getBrowser(),
                $x->getVisitors(),
            );
        }
        return array(
            "template" => "AcmeSFSBundle:Stats:consult_browsers_chart.html.twig",
            "values" => array("values" => $values)
        );
    }

    public function consultCountryGeo() {
        $result = $this->getGa()->requestReportData($this->getProfileId(), array('country', "region"), array('visitors'));
        $values = $values_1 = array();
        foreach ($result as $x) {
            $values[] = array(
                $x->getCountry(),
                $x->getVisitors() . "",
            );
            $values_1[] = array(
                $x->getCountry(),
                $x->getRegion(),
                $x->getVisitors(),
            );
        }
        return array(
            "template" => "AcmeSFSBundle:Stats:consult_country_geo.html.twig",
            "values" => array("values" => $values, "values_1" => $values_1)
        );
    }

    public function consultMovilChart() {
        $result = $this->getGa()->requestReportData($this->getProfileId(), array('mobiledevicemodel', 'mobiledevicebranding'), array('visitors'), "mobiledevicemodel");
        $values = $values_1 = array();
        foreach ($result as $x) {
            $values[] = array(
                $x->getMobiledevicemodel(), //getMobiledevicebranding
                $x->getVisitors(),
            );
            $values_1[] = array(
                $x->getMobiledevicemodel(),
                $x->getMobiledevicebranding(),
                $x->getVisitors(),
            );
        }
        return array(
            "template" => "AcmeSFSBundle:Stats:consult_movil_chart.html.twig",
            "values" => array("values" => $values, "values_1" => $values_1)
        );
    }

    public function consultRegionSeo() {
        $results = $this->getGa()->requestReportData($this->getProfileId(), array('country', 'region'), array('visitors'), 'country');
        $values = $values_1 = array();

        /* Ordenar regiones por paises */
        $last = "";
        $datos_regiones = array();
        $countries = array();
        $regions = array();
        $count = 0;

        //Agrupar
        foreach ($results as $result) {
            $country = $result->getCountry();
            $region = $result->getRegion();
            if ($last == "")
                $last = $country;

            if ($last != $country) {
                $countries[$last] = array($last, $regions);
                $regions = array();
                $count = 0;
                $last = $country;
            }
            $regions[$count] = array($region, $result->getVisitors());
            ++$count;
        }
        /* Agregar ultimo */
        $countries[$last] = array($last, $regions);

        $country_count = 0;
        foreach ($countries as $pais) {
            ++$country_count;
            $nombre_pais = $countries[$pais[0]][0];
            $visitas_pais = 0;
            $country_code = \gapi::getCountryCode($nombre_pais);
            if ($nombre_pais == "(not set)") {
                continue;
            }
            $datos_regiones = $countries[$pais[0]][1];
            $flt_datos_regiones = array();

            foreach ($datos_regiones as $value) {
                $visitas_pais += $value[1];
                $flt_datos_regiones[] = array(
                    $value[0],
                    $value[1] . ""
                );
            }

            $values[] = array(
                "country_count" => $country_count,
                "nombre_pais" => $nombre_pais,
                "visitas_pais" => $visitas_pais,
                "country_code" => $country_code,
                "regions" => $flt_datos_regiones
            );

            foreach ($datos_regiones as $reg) {
                $visitas_pais += $reg[1];
            }
        }

        return array(
            "template" => "AcmeSFSBundle:Stats:consult_region_geo.html.twig",
            "values" => array("values" => $values, "country_count" => $country_count)
        );
    }

    public function consultSoChart() {
        $result = $this->getGa()->requestReportData($this->getProfileId(), array('operatingsystem', 'operatingsystemversion'), array('visitors'), "operatingsystem");
        $values = $values_1 = array();
        foreach ($result as $x) {
            $values[] = array(
                $x->getOperatingsystem(), //getMobiledevicebranding
                $x->getVisitors(),
            );
            $values_1[] = array(
                $x->getOperatingsystem(),
                $x->getOperatingsystemversion(),
                $x->getVisitors(),
            );
        }
        return array(
            "template" => "AcmeSFSBundle:Stats:consult_so_chart.html.twig",
            "values" => array("values" => $values, "values_1" => $values_1)
        );
    }

    public function consultVisitsLine() {
        $values = $values_1 = $values_2 = $values_3 = $values_4 = array();
        $time_back = date('M j, Y', strtotime('-30 day')) . ' - ' . date('M j, Y');


        $result = $this->getGa()->requestReportData($this->getProfileId(), array('date'), array('uniquePageviews', 'pageviews', 'visitors'), 'date');
        foreach ($result as $x) {
            $values[] = array(
                date('M j', strtotime($x->getDate())), //getMobiledevicebranding
                $x->getPageviews(),
                $x->getUniquePageviews(),
                $x->getVisitors()
            );
        }

        $result = $this->getGa()->requestReportData($this->getProfileId(), "month", array('pageviews', 'uniquePageviews', 'exitRate', 'avgTimeOnPage', 'entranceBounceRate', 'newvisits', 'percentnewvisits', 'avgtimeonsite', "visitors"), "visitors");
        foreach ($result as $x) {
            $values_1[] = array(
                date("F", mktime(0, 0, 0, $x->getMonth(), 10)),
                "" . round($x->getVisitors(), 2),
                "" . round($x->getNewvisits(), 2),
                "" . number_format($x->getPageviews()),
                "" . number_format($x->getUniquepageviews()),
                "" . \gapi::secondMinute($x->getAvgtimeonpage()),
                "" . \gapi::secondMinute($x->getAvgtimeonsite()),
                round($x->getEntrancebouncerate(), 2) . "%",
                round($x->getExitrate(), 2) . "%",
                round($x->getPercentnewvisits(), 2) . "%"
            );
        }
        $result = $this->getGa()->requestReportData($this->getProfileId(), "keyword", array('pageviews', 'uniquePageviews', 'exitRate', 'avgTimeOnPage', 'entranceBounceRate', 'newvisits', 'percentnewvisits', 'avgtimeonsite', "visitors"), "visitors");
        foreach ($result as $x) {
            $values_2[] = array(
                $x->getKeyword(),
                "" . round($x->getVisitors(), 2),
                "" . round($x->getNewvisits(), 2),
                "" . number_format($x->getPageviews()),
                "" . number_format($x->getUniquepageviews()),
                "" . \gapi::secondMinute($x->getAvgtimeonpage()),
                "" . \gapi::secondMinute($x->getAvgtimeonsite()),
                round($x->getEntrancebouncerate(), 2) . "%",
                round($x->getExitrate(), 2) . "%",
                round($x->getPercentnewvisits(), 2) . "%"
            );
        }
        $result = $this->getGa()->requestReportData($this->getProfileId(), "source", array('pageviews', 'uniquePageviews', 'exitRate', 'avgTimeOnPage', 'entranceBounceRate', 'newvisits', 'percentnewvisits', 'avgtimeonsite', "visitors"), "visitors");
        foreach ($result as $x) {
            $values_3[] = array(
                $x->getSource(),
                "" . round($x->getVisitors(), 2),
                "" . round($x->getNewvisits(), 2),
                "" . number_format($x->getPageviews()),
                "" . number_format($x->getUniquepageviews()),
                "" . \gapi::secondMinute($x->getAvgtimeonpage()),
                "" . \gapi::secondMinute($x->getAvgtimeonsite()),
                round($x->getEntrancebouncerate(), 2) . "%",
                round($x->getExitrate(), 2) . "%",
                round($x->getPercentnewvisits(), 2) . "%"
            );
        }
        $result = $this->getGa()->requestReportData($this->getProfileId(), "searchdestinationpage", array('pageviews', 'uniquePageviews', 'exitRate', 'avgTimeOnPage', 'entranceBounceRate', 'newvisits', 'percentnewvisits', 'avgtimeonsite', "visitors"), "visitors");
        foreach ($result as $x) {
            $values_4[] = array(
                $x->getSearchdestinationpage(),
                "" . round($x->getVisitors(), 2),
                "" . round($x->getNewvisits(), 2),
                "" . number_format($x->getPageviews()),
                "" . number_format($x->getUniquepageviews()),
                "" . \gapi::secondMinute($x->getAvgtimeonpage()),
                "" . \gapi::secondMinute($x->getAvgtimeonsite()),
                round($x->getEntrancebouncerate(), 2) . "%",
                round($x->getExitrate(), 2) . "%",
                round($x->getPercentnewvisits(), 2) . "%"
            );
        }

        return array(
            "template" => "AcmeSFSBundle:Stats:consult_visits_line.html.twig",
            "values" => array(
                "values" => $values,
                "values_1" => $values_1,
                "values_2" => $values_2,
                "values_3" => $values_3,
                "values_4" => $values_4,
                "time_back" => $time_back
            )
        );
    }

    /**
     * @Route("/secured/stats/", name="stats")
     * @Template()
     */
    public function indexAction() {
        $type = $this->getRequest()->get("type", "stats");
        $this->profile_id = $this->getRequest()->get("id", "73649671");
        $overs = array("stats" => "", "geo" => "", "devices" => "");
        $overs[$type] = "select";

        return $this->render("AcmeSFSBundle:Stats:index.html.twig", array(
                    "includes" => $this->getStatsFor($type),
                    "tags" => $overs
        ));
    }

    function getStatsFor($type) {
        $includes = array();
        switch ($type) {
            case "geo":
                $includes[] = $this->consultCountryGeo();
                $includes[] = $this->consultRegionSeo();
                break;
            case "devices":
                $includes[] = $this->consultBrowsersChart();
                $includes[] = $this->consultMovilChart();
                $includes[] = $this->consultSoChart();
                break;
            default:
                $includes[] = $this->consultVisitsLine();
                break;
        }
        return $includes;
    }

    /**
     * @Route("/secured/getstats/", name="get_stats")
     * @Template()
     */
    public function getstatsAction() {
        $type = $this->getRequest()->get("type", "visits_line");
        $results = array();
        switch ($type) {
            case "browsers_chart":
                $results = $this->consultBrowsersChart();
            case "country_geo":
                $results = $this->consultCountryGeo();
            case "movil_chart":
                $results = $this->consultMovilChart();
            case "region_geo":
                $results = $this->consultRegionSeo();
            case "so_chart":
                $results = $this->consultSoChart();
            case "visits_line":
                $results = $this->consultVisitsLine();
        }
        if (count($results) > 0) {
            return $this->render($results["template"], $results["values"]);
        } else {
            return new \Symfony\Component\HttpFoundation\Response("<h1>El tipo especificado no es válido</h1>");
        }
    }

}
