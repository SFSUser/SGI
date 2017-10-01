<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class LoginController extends Controller {
    
    /**
     * @Route("/manageuserinfo/", name="manage_user_info")
     * @Template()
     */
    public function proccesAction(\Symfony\Component\HttpFoundation\Request $request) {
        $msg = "On blank item > ";
        $product = new \Acme\SFSBundle\Entity\SFSUsersInfo();
        $id = $request->get("id");

        //Databases
        $querier = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSUsersInfo");
        $manager = $this->getDoctrine()->getManager();

        //Get and edit
        if ($id != null) {
            $result = $querier->find($id);
            if ($result != null) {
                $msg .= "On get item > ";
                $product = $result;
            }
        }

        //Update / Create
        $form = $this->createForm(new \Acme\SFSBundle\Form\Type\SFSUsersInfoType(), $product);
        if ($request->getMethod() == "POST") {
            //$request->attributes->remove("id");
            $form->handleRequest($request);
            $data = $form->getData();

            if ($id == null) {
                $manager->persist($data);
                $manager->flush();
                $id = $data->getId();
                $msg .= "On create item";
            } else {
                $manager->persist($data);
                $manager->flush();
                $msg .= "On update item";
            }
        }

        $errores = array();
        return $this->render("AcmeSFSBundle:Users:editor.html.twig", array(
                    "form" => $form->createView(), "errores" => $errores, "msg" => $msg, "id" => $id
        ));
    }

    /**
     * @Route("/login/", name="user_login")
     * @Template()
     */
    public function loginAction($redirect_link, $register_link) {
        return $this->render("AcmeSFSBundle:Users:login.html.twig", array(
                    "onlogin" => $redirect_link,
                    "register" => $register_link
        ));
    }

    /**
     * @Route("/register/", name="user_register")
     * @Template()
     */
    public function registerAction(\Symfony\Component\HttpFoundation\Request $request) {
        $ajax = "";
        $months = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        return $this->render("AcmeSFSBundle:Users:register.html.twig", array(
                    "months" => $months,
                    "ajax" => $ajax
        ));
    }

    static function convertToMySQLDate($date) {
        return new \DateTime(date('Y-m-d', strtotime($date)));
    }

    /**
     * @Route("/user/register/", name="user_register_action", defaults={"_format": "json"})
     */
    public function registerUserAction(\Symfony\Component\HttpFoundation\Request $request) {

        $username = $request->get("username", ""); //Utils::getParam($data, "username", "");
        $name = $request->get("name", ""); //Utils::getParam($data, "name", "");
        $apellido = $request->get("apellido", ""); //Utils::getParam($data, "apellido", "");
        $email = $request->get("email", ""); //Utils::getParam($data, "email", "");
        $password = $request->get("password", ""); //Utils::getParam($data, "password", "");
        $day = $request->get("day", "") + 0; //Utils::getParam($data, "day", "") + 0;
        $month = $request->get("month", "") + 0; //Utils::getParam($data, "month", "") + 0;
        $year = $request->get("year", "") + 0; //Utils::getParam($data, "year", "") + 0;
        $gender = $request->get("sexo", "male"); //Utils::getParam($data, "sexo", "male");
        $avatar = $request->get("avatar", ""); //Utils::getParam($data, "avatar", "");
        $country = $request->get("country", "Colombia"); //Utils::getParam($data, "country", "Colombia");
        $notices = $request->get("notices", "") != ""; //Utils::getParam($data, "notices", "") != "";
        $birthday = new \DateTime("$year-$month-$day 11:14:15.638276");//$request->get(self::convertToMySQLDate("$day-$month-$year")); //MySQLUtils::convertToMySQLDate("$day-$month-$year");

        /*
          $username = MySQLUtils::getSecureValue($username);
          $name = MySQLUtils::getSecureValue($name);
          $apellido = MySQLUtils::getSecureValue($apellido);
          $email = MySQLUtils::getSecureValue($email);
          $password = MySQLUtils::getSecureValue($password);
          $gender = MySQLUtils::getSecureValue($gender);
          $avatar = MySQLUtils::getSecureValue($avatar);
          $country = MySQLUtils::getSecureValue($country);
         */

        //$validator = '/^[w.-]+@[w.-]+.[a-zA-Z]{2,5}$/';
        $error = "";
        if (!ctype_alnum(str_replace(array("-", "_"), '', $username))) {
            $returns = array("result" => 1, "message" => "Nombre de usuario con carácteres no válidos");
            return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
        }
        if (strlen($username) > 15) {
            $returns = array("result" => 1, "message" => "Nombre de usuario muy largo");
            return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
        }
        if (!$this->checkUserName($username)) {
            $returns = array("result" => 1, "message" => "EL nombre de usuario ya esta en uso");
            return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
        }
        if (!ctype_alnum(str_replace(array(" "), '', $name))) {
            $returns = array("result" => 2, "message" => "Nombre no válido");
            return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
        }
        if (!ctype_alnum(str_replace(array(" "), '', $apellido))) {
            $returns = array("result" => 3, "message" => "Apellido no válido");
            return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $returns = array("result" => 4, "message" => "Correo electrónico no válido");
            return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
        }
        if (strlen($password) <= 5) {
            $returns = array("result" => 5, "message" => "La contraseña es muy corta");
            return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
        }
        if (!checkdate($month, $day, $year) || ($year <= 1900 || $year > 2020) || ($day <= 0 || $day > 31) || ($month <= 0 || $month > 12)) {
            $returns = array("result" => 6, "message" => "La fecha de nacimiento no es válida");
            return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
        }
        
        $manager = $this->getDoctrine()->getManager();
        
        //Persist user profile
        $user = new \Acme\SFSBundle\Entity\SFSUsers();
        $user->setUser($username);
        $user->setAccessLevel(1);
        $user->setAccountStatus(1);
        $user->setConfirmKey(md5(uniqid(rand(), true)));
        $user->setConfirmed(new \DateTime("now"));
        $user->setCreated(new \DateTime("now"));
        $user->setEmail($email);
        $user->setLastAccess(new \DateTime("now"));
        $user->setPassword($password);
        $user->setSessionToken(md5(uniqid(rand(), true)));
        $manager->persist($user);
        
        $info = new \Acme\SFSBundle\Entity\SFSUsersInfo();
        $info->setApellido($apellido);
        $info->setAvatar($avatar);
        $info->setCiudad("");
        $info->setDireccion("");
        $info->setMovil("");
        $info->setNacimiento($birthday);
        $info->setNombre($name);
        $info->setPais($country);
        $info->setRegion("");
        $info->setSexo("");
        $info->setTelefono("");
        $info->setUsuario($username);
        
        $manager->persist($info);
        
        //Save changes
        $manager->flush();

        $this->login($user, $password, false);

        $returns = array("result" => 0, "Cuenta creada correctamente");
        return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));

        /*
          $entity = Entities::getEntity("users");
          $account = array(
          "user" => $username,
          //"name" => $name,
          //"surname" => $apellido,
          "email" => $email,
          "password" => $password,
          "access_level" => "0",
          "account_status" => 0,
          "confirm_key" => md5(uniqid(rand(), true)),
          "timestamp_last_access" => MySQLUtils::getNowDate(),
          );
          $entity->insertElement($account);
         * 
         */
        /*
          $entity = Entities::getEntity("users_ads_info");
          $account_info = array(
          "usuario" => $username,
          "nombre" => $name,
          "apellido" => $apellido,
          "nacimiento" => $birthday,
          "sexo" => ($gender != "male") ? "Femenino" : "Masculino",
          "avatar" => $avatar,
          "pais" => $country
          );
          $entity->insertElement($account_info);
         */
        //self::login($username, $password);
        //print_r($account);
        //return Utils::getAjaxParsed("result", 0);
    }

    public function login($user, $password, $keep) {
        $doc = $this->getDoctrine();
        $result = $doc->getRepository("AcmeSFSBundle:SFSUsers")->findOneBy(array(
            "user" => $user,
            "password" => $password
        ));


        $logged = $result != null;
        if ($logged) {
            $session = $this->getRequest()->getSession();
            $session->start();
            $session->set("user_info", $result);
            $result->setLastAccess(new \DateTime("now"));
            $doc->getManager()->flush();
        }
        return $logged? 1 : 0;
    }

    /**
     * @Route("/user/login/", name="user_login_action", defaults={"_format": "json"})
     * @Template()
     */
    public function loginUserAction(\Symfony\Component\HttpFoundation\Request $request) {

        $user = $request->get("user");
        $password = $request->get("password");

        $returns = array("result" => $this->login($user, $password, false));
        return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
    }

    public function checkUserName($user) {
        $result = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSUsers")->findBy(array("user" => $user));
        return count($result) <= 0;
    }

    /**
     * @Route("/user/check/", name="user_check_action", defaults={"_format": "json"})
     * @Template()
     */
    public function checkUserAction(\Symfony\Component\HttpFoundation\Request $request) {
        $user = $request->get("user");
        $returns = array("result" => $this->checkUserName($user));
        return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
    }

}
