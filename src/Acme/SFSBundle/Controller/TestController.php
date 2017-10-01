<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\ExpressionLanguage\Expression;


class TestController extends Controller {

    /**
     * @Route("test/", name="test")
     * @Template()
     */
    public function indexAction() {
        /*
          require_once __DIR__ . '\..\Libs\paginator\paginator.class.php';


          $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
          $qb->select('count(producto.id)');
          $qb->from('AcmeSiteBundle:HebrasProductos', 'producto');

          $paginator = new \PaginatorUtils($this);
          $content = $paginator->getButtonList("button", $qb);

          $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
          $qb->select('producto.id');
          $qb->from('AcmeSiteBundle:HebrasProductos', 'producto');
          $qb->setFirstResult(20);
          $qb->setMaxResults(10);


          foreach ($paginator->getAllPaginated("AcmeSiteBundle:HebrasProductos") as $x) {
          //print_r($x);
          echo $x->getId() . "<br>";
          }
         */
        $content = $this->get('security.context')->isGranted(new Expression(
                '"ROLE_ADMIN" in roles or (user and user.isSuperAdmin())'
        )) ? "SI":"NO";
        return $this->render("AcmeSFSBundle:Test:index.html.twig", array(
                    "content" => $content
        ));
    }

}
