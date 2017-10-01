<?php

namespace Acme\SFSBundle\Controller;

//require_once __DIR__ . '\..\Libs\jbbcode\jbbcode-1.2.0\Parser.php';

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CommentController extends Controller {

    public $visible_comments = 4;

    public function removeCommentChain($id) {
        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSComments");
        $manager = $this->getDoctrine()->getManager();
        $comment = $repo->find($id);
        $comments = $repo->findBy(array(
            "respuesta" => $id
        ));

        foreach ($comments as $x) {
            $this->removeCommentChain($x->getId());
            $manager->remove($x);
        }
        $manager->remove($comment);
        $manager->flush();
    }

    public function getCommentChain($hilo) {
        return $this->getCommentReplies("-1", $hilo);
    }

    public function getCommentReplies($id, $hilo) {
        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSComments");
        //$manager = $this->getDoctrine()->getManager();

        /* $responser = count($repo->findAll())

          $query = $manager->createQuery("SELECT c FROM AcmeSFSBundle:SFSComments c WHERE c.respuesta = :id AND c.hilo = :hilo")
          ->setParameter("id", $id)
          ->setParameter("hilo", $hilo)
          ->setFirstResult(2)->setMaxResults(4)
          ;
          $result = $query->getResult();
         */

        $resultset = $repo->findBy(array(
            "respuesta" => $id,
            "hilo" => $hilo
        ));

        $result = array();

        $count_max = count($resultset) - 1;
        $visible_index = $count_max - $this->visible_comments;
        if ($visible_index > 0) {
            for ($x = $visible_index; $x <= $count_max; ++$x) {
                $result[] = $resultset[$x];
            }
        } else {
            $result = $resultset;
        }

        $more_comments = count($resultset) - count($result);

        $comments = array();
        foreach ($result as $x) {
            $comments[] = $this->getCommentLine($x, $this->getCommentReplies($x->getId(), $hilo));
        }

        if ($more_comments > 0) {
            $comments[0]["more"] = $more_comments;
        }
        return $comments;
    }

    public function getAntResponses($id) {
        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSComments");
        $manager = $this->getDoctrine()->getManager();
        $comment = $repo->find($id);

        $query = $manager->createQuery("SELECT c FROM AcmeSFSBundle:SFSComments c WHERE c.respuesta = :id AND c.id < :currentid AND c.hilo = :hilo ORDER BY c.id ASC")
                        ->setParameter("id", $comment->getRespuesta())
                        ->setParameter("currentid", $comment->getId())
                        ->setParameter("hilo", $comment->getHilo())->setMaxResults($this->visible_comments + 1);
        //print_r("QUery : " . $query);
        $result = $query->getResult();

        $comments = array();
        foreach ($result as $x) {
            $comments[] = $this->getCommentLine($x, $this->getCommentReplies($x->getId(), $comment->getHilo()));
        }
        return $comments;
    }

    public function getComment($id) {
        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSComments");

        $result = $repo->findOneBy(array(
            "id" => $id,
        ));

        return $this->getCommentLine($result, $this->getCommentReplies($id, $result->getHilo()));
    }

    public function getCommentLine(\Acme\SFSBundle\Entity\SFSComments $comment_line, $responses) {
        \Acme\SFSBundle\Controller\UtilsController::includeSFSUtil("Parser");
        //$admin = new AdminController();
        $gustan = substr_count($comment_line->getGustan(), ";");
        $nogustan = substr_count($comment_line->getNogustan(), ";");

        $contenido = $comment_line->getContenido();
        $contenido = strip_tags($contenido);

        $parser = new \JBBCode\Parser();
        $builder = new \JBBCode\CodeDefinitionBuilder('font', '<span style="font-family:{option};">{param}</span>');
        $builder->setUseOption(true); //->setOptionValidator(new JBBCode\validators\UrlValidator());
        $parser->addCodeDefinition($builder->build());
        $builder = new \JBBCode\CodeDefinitionBuilder('size', '<span style="font-size:{option}px;">{param}</span>');
        $builder->setUseOption(true);
        $parser->addCodeDefinition($builder->build());
        $builder = new \JBBCode\CodeDefinitionBuilder('youtube', '<iframe width="420" height="315" src="//www.youtube.com/embed/{param}" frameborder="0" allowfullscreen></iframe>');
        $parser->addCodeDefinition($builder->build());
        $builder = new \JBBCode\CodeDefinitionBuilder('ul', '<ul>{param}</ul>');
        $parser->addCodeDefinition($builder->build());
        $builder = new \JBBCode\CodeDefinitionBuilder('li', '<li>{param}</li>');
        $parser->addCodeDefinition($builder->build());
        $builder = new \JBBCode\CodeDefinitionBuilder('quote', '<div class="comment-box sty-box-padding-5 sty-box-margin-5">{param}</div>');
        $parser->addCodeDefinition($builder->build());
        $parser->addCodeDefinitionSet(new \JBBCode\DefaultCodeDefinitionSet());
        $parser->parse($contenido);
        $contenido = $parser->getAsHtml();

        $array = array(
            "usuario" => $comment_line->getUsuario(),
            "contenido" => $comment_line->getContenido(),
            "response_id" => count($responses) > 0 ? $responses[0]["id"] : -1,
            "response_count" => isset($responses[0]["more"]) ? $responses[0]["more"] : 0,
            "admin" => $this->getRequest()->getSession()->get("login_admin", false), //$admin->checkLogin("admin"),
            "contenido" => $contenido,
            "reply_from" => 1000,
            "num_gustan" => $gustan,
            "num_nogustan" => $nogustan,
            "votos_color" => "red",
            "total_votos" => $gustan - $nogustan,
            "fecha_amigable" => "hace 20 horas",
            "path_level" => "",
            "id" => $comment_line->getId(),
            "respuesta" => $comment_line->getRespuesta(),
            "modo" => "admin",
            "fecha" => $comment_line->getFecha(),
            "correo" => $comment_line->getCorreo(),
            "responses" => $responses,
        );
        return $array;
    }

    /**
     * @Route("/getcomment/", name="get_comment")
     * @Template()
     */
    public function getcommentAction(\Symfony\Component\HttpFoundation\Request $request) {
        $id = $request->get("id");
        $index = $this->getComment($id);
        return $this->render("AcmeSFSBundle:Comment:comment.html.twig", $index);
    }

    /**
     * @Route("/getcommentant/", name="get_comment_ant")
     * @Template()
     */
    public function getcommentantAction(\Symfony\Component\HttpFoundation\Request $request) {
        $id = $request->get("id");
        $index = $this->getAntResponses($id);
        return $this->render("AcmeSFSBundle:Comment:chain.html.twig", array("comments" => $index));
    }

    /**
     * @Route("/comments/{hilo}/", name="comments_index")
     * @Template()
     */
    public function indexAction($hilo) {
        $array = $this->getCommentChain($hilo);
        $index = array(
            "total_comments" => count($array),
            "admin" => $this->getRequest()->getSession()->get("login_admin", false),
            "user" => "SFS",
            "email" => "sfstricks@hotmail.com",
            "thread" => $hilo,
            "responses" => $array
        );
        return $this->render("AcmeSFSBundle:Comment:index.html.twig", $index);
    }

    /**
     * @Route("/addcomment/", name="add_comment", defaults={"_format": "json"})
     * @Template()
     */
    public function addcommentAction(\Symfony\Component\HttpFoundation\Request $request) {
        $manager = $this->getDoctrine()->getManager();
        $comment = new \Acme\SFSBundle\Entity\SFSComments();
        $comment->setContenido($request->get("contenido", ""));
        $comment->setCorreo($request->get("correo", ""));
        $comment->setFecha(new \DateTime("now"));
        $comment->setGustan("");
        $comment->setHilo($request->get("hilo", ""));
        $comment->setIp($this->get('request')->getClientIp());
        $comment->setModo($request->get("modo", "user"));
        $comment->setNogustan("");
        $comment->setRespuesta($request->get("respuesta", "-1"));
        $comment->setUsuario($request->get("usuario", ""));
        $comment->setVotos("0");

        $manager->persist($comment);
        $manager->flush();

        //$user = $request->get("user");
        $returns = array(
            "result" => 1,
            "id" => $comment->getId(),
            "respuesta" => $comment->getRespuesta(),
            "comment_html" => $this->getComment($comment->getId())
        );
        return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
    }

    /**
     * @Route("/secured/removecomment/", name="remove_comment", defaults={"_format": "json"})
     * @Template()
     */
    public function removecommentAction(\Symfony\Component\HttpFoundation\Request $request) {
        $id = $request->get("id", "");
        $this->removeCommentChain($id);
        return new \Symfony\Component\HttpFoundation\Response(json_encode(array("result" => 1)));
    }

    /**
     * @Route("/votecomment/", name="vote_comment", defaults={"_format": "json"})
     * @Template()
     */
    public function votecommentAction(\Symfony\Component\HttpFoundation\Request $request) {
        $id = $request->get("id", "");
        $vote = $request->get("vote", "");

        $manager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSComments");
        $comment = $repo->find($id);

        $ip = $this->get('request')->getClientIp();
        $gustan = $comment->getGustan();
        $nogustan = $comment->getNogustan();
        $can_vote = substr_count($gustan . $nogustan, $ip) <= 0;
        $result = 0;

        if ($vote > 0) {
            if ($can_vote) {
                $comment->setGustan("$gustan$ip;");
                $result = 1;
            }
        } else {
            if ($can_vote) {
                $comment->setNogustan("$nogustan$ip;");
                $result = 1;
            }
        }
        $manager->flush();

        return new \Symfony\Component\HttpFoundation\Response(json_encode(array("result" => $result)));
    }

}
