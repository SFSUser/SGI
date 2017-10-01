<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;

class AdminController extends Controller {
    
    /**
     * @Route("/admin/check", name="admincheck")
     * @Template()
     */
    public function checkAction() {
        
    }

    /**
     * @Route("/admin/logout", name="adminlogout")
     * @Template()
     */
    public function logoutsAction() {
        
    }
    
     /**
     * @Route("/admin/login", name="adminlogin")
     * @Template()
     
    public function loginAction() {
        $request = $this->getRequest();
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        //print_r($error);
        return $this->render("AcmeSFSBundle:Admin:loginform.html.twig", array(
                    "last" => $this->getRequest()->getSession()->get(SecurityContext::LAST_USERNAME),
                    "error" => $error,
                    "section" => "admin",
                    "template_parent" => "AcmeSiteBundle:SFS:login.html.twig",
        ));
    }
    
//
//    public function login($user, $password) {
//        $session = $this->getRequest()->getSession();
//        $login_state = false;
//
//        if ($user == "admin" && $password == "6722102") {
//            $login_state = true;
//            $session->set("login_" . $user, $login_state);
//
//
//            $em = $this->getDoctrine();
//            $repo = $em->getRepository("UserBundle:User"); //Entity Repository
//            $user = $repo->loadUserByUsername($user);
//            if (!$user) {
//                throw new UsernameNotFoundException("User not found");
//            } else {
//                $token = new UsernamePasswordToken($user, null, "your_firewall_name", $user->getRoles());
//                $this->get("security.context")->setToken($token); //now the user is logged in
//                //now dispatch the login event
//                $request = $this->get("request");
//                $event = new InteractiveLoginEvent($request, $token);
//                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
//            }
//        }
//        return $login_state ? 1 : 0;
//    }
//
//    public function logout($user) {
//        $session = $this->getRequest()->getSession();
//        $session->remove("login_" . $user);
//        return 1;
//    }
//
//    public function checkLogin($user) {
//        $session = $this->getRequest()->getSession();
//        return $session->get("login_" . $user, false) ? 1 : 0;
//    }
//
//    /**
//     * @Route("adminlogin", name="admin_login", defaults={"_format": "json"})
//     */
//    public function loginAction(\Symfony\Component\HttpFoundation\Request $request) {
//        $password = $request->get("password");
//        $user = $request->get("user");
//        $user = strtolower($user);
//        $array = array("result" => $this->login($user, $password));
//        return new \Symfony\Component\HttpFoundation\Response(json_encode($array));
//    }
//
//    /**
//     * @Route("adminlogout", name="admin_logout", defaults={"_format": "json"})
//     */
//    public function logoutAction(\Symfony\Component\HttpFoundation\Request $request) {
//        $user = $request->get("user");
//        $array = array("result" => $this->logout($user));
//        return new \Symfony\Component\HttpFoundation\Response(json_encode($array));
//    }
//
//    /**
//     * @Route("adminform", name="admin_form")
//     * Template()
//     */
//    public function loginformAction(\Symfony\Component\HttpFoundation\Request $request, $user) {
//        return $this->render("AcmeSFSBundle:Admin:login.html.twig", array(
//                    "user" => $user,
//                    "login" => $request->getSession()->get("login_$user", false) ? "true" : "false"
//        ));
//    }

}
