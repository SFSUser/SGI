<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CaptchaController extends Controller {

    function getCaptchaText($capid = "captcha_text") {
        $session = $this->getRequest()->getSession();
        $values = $session->get($capid);

        if (isset($values["text"])) {
            return $values["text"];
        } else {
            return "-666";
        }
    }

    function getCaptchaValue($capid = "captcha_text") {
        $session = $this->getRequest()->getSession();
        $values = $session->get($capid);

        if (isset($values["value"])) {
            return $values["value"];
        } else {
            return "-666";
        }
    }

    function checkCaptcha($value, $capid = "captcha_text") {
        return (self::getCaptchaValue($capid) . "" == $value . "");
    }

    public function genNewCaptcha($capid = "captcha_text") {
        $session = $this->getRequest()->getSession();

        $captcha_value = rand("50", "9999");
        $captcha_gen = new CaptchaGen();
        $captcha_text = $captcha_gen->docenumeros($captcha_value);

        $captcha_values = array("value" => $captcha_value, "text" => $captcha_text);
        $session->set($capid, $captcha_values);

        return array("text" => $captcha_text, "value" => $captcha_value);
    }

    /**
     * @Route("captcha", name="captcha")
     * @Template()
     */
    public function formAction($captcha_id) {
        $values = $this->genNewCaptcha($captcha_id);
        return $this->render("AcmeSFSBundle:Captcha:input.html.twig", array(
                    "captcha_id" => $captcha_id,
                    "captcha" => $values["text"]
        ));
    }

    /**
     * @Route("generatecaptcha", name="generate_captcha", defaults={"_format": "json"})
     */
    public function generateAction(\Symfony\Component\HttpFoundation\Request $request) {
        $capid = $request->get("capid");
        $array = $this->genNewCaptcha($capid);
        delete($array["value"]);
        return new \Symfony\Component\HttpFoundation\Response(json_encode($array));
    }

    /**
     * @Route("checkcaptcha", name="check_captcha", defaults={"_format": "json"})
     */
    public function checkAction(\Symfony\Component\HttpFoundation\Request $request) {
        $value = $request->get("value");
        $capid = $request->get("capid");
        $array = array(
            "result" => $this->checkCaptcha($value, $capid),
        );
        return new \Symfony\Component\HttpFoundation\Response(json_encode($array));
    }

}

class CaptchaGen {

    protected $numeros = array("-", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve");
    protected $numerosX = array("-", "un", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve");
    protected $numeros100 = array("-", "ciento", "doscientos", "trecientos", "cuatrocientos", "quinientos", "seicientos", "setecientos", "ochocientos", "novecientos");
    protected $numeros11 = array("-", "once", "doce", "trece", "catorce", "quince", "dieciseis", "diecisiete", "dieciocho", "diecinueve");
    protected $numeros10 = array("-", "-", "-", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa");

    public function tresnumeros($n, $last) {
        //global $numeros100, $numeros10, $numeros11, $numeros, $numerosX;
        $numeros100 = $this->numeros100;
        $numeros10 = $this->numeros10;
        $numeros11 = $this->numeros11;
        $numeros = $this->numeros;
        $numerosX = $this->numerosX;

        if ($n == 100)
            return "cien ";
        if ($n == 0)
            return "cero ";
        $r = "";
        $cen = floor($n / 100);
        $dec = floor(($n % 100) / 10);
        $uni = $n % 10;
        if ($cen > 0)
            $r .= $numeros100[$cen] . " ";

        switch ($dec) {
            case 0: $special = 0;
                break;
            case 1: $special = 10;
                break;
            case 2: $special = 20;
                break;
            default: $r .= $numeros10[$dec] . " ";
                $special = 30;
                break;
        }
        if ($uni == 0) {
            if ($special == 30)
                ;
            else if ($special == 20)
                $r .= "veinte ";
            else if ($special == 10)
                $r .= "diez ";
            else if ($special == 0)
                ;
        } else {
            if ($special == 30 && !$last)
                $r .= "y " . $numerosX[$n % 10] . " ";
            else if ($special == 30)
                $r .= "y " . $numeros[$n % 10] . " ";
            else if ($special == 20) {
                if ($uni == 3)
                    $r .= "veintitrés ";
                else if (!$last)
                    $r .= "veinti" . $numerosX[$n % 10] . " ";
                else
                    $r .= "veinti" . $numeros[$n % 10] . " ";
            } else if ($special == 10)
                $r .= $numeros11[$n % 10] . " ";
            else if ($special == 0 && !$last)
                $r .= $numerosX[$n % 10] . " ";
            else if ($special == 0)
                $r .= $numeros[$n % 10] . " ";
        }
        return $r;
    }

    public function seisnumeros($n, $last) {
        if ($n == 0)
            return "cero ";
        $miles = floor($n / 1000);
        $units = $n % 1000;
        $r = "";
        if ($miles == 1)
            $r .= "mil ";
        else if ($miles > 1)
            $r .= $this->tresnumeros($miles, false) . "mil ";
        if ($units > 0)
            $r .= $this->tresnumeros($units, $last);
        return $r;
    }

    public function docenumeros($n) {
        if ($n == 0)
            return "cero ";
        $millo = floor($n / 1000000);
        $units = $n % 1000000;
        $r = "";
        if ($millo == 1)
            $r .= "un millón ";
        else if ($millo > 1)
            $r .= $this->seisnumeros($millo, false) . "millones ";
        if ($units > 0)
            $r .= $this->seisnumeros($units, true);
        return $r;
    }

//echo $captcha_value . "->" . $captcha_text;
}
