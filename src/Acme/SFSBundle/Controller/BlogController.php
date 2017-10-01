<?php

namespace Acme\SFSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BlogController extends Controller {

    public function indexAction($identifier) {
        //$identifier = $this->getRequest()->get("identifier", "default");
        
        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSBlogs");
        $found = $repo->findOneBy(array("identifier" => $identifier));
        if($found == null){
            $found = new \Acme\SFSBundle\Entity\SFSBlogs();
            $found->setTitle("Blog sin título");
            $found->setContent("Blog sin contenido");
        }
        
        $returns = array(
            "blog" => $found,
            "identifier" => $identifier 
        );
        return $this->render("AcmeSFSBundle:Blog:blog.html.twig", $returns);
    }
    /**
     * @Route("/secured/blogs/crear", name="create_blogs", defaults={"_format": "json"})
     * @Template()
     */
    public function createAction() {
        $title = $this->getRequest()->get("title");
        $identifier = $this->getRequest()->get("identifier");
        //$section = $this->getRequest()->get("section");
        $content = $this->getRequest()->get("content");

        $blog = new \Acme\SFSBundle\Entity\SFSBlogs();
        
        $repo = $this->getDoctrine()->getRepository("AcmeSFSBundle:SFSBlogs");
        $manager = $this->getDoctrine()->getManager();
        $found = $repo->findOneBy(array("identifier" => $identifier));
        if($found != null){
            $blog = $found;
        }
        
        $blog->setTitle($title);
        $blog->setIdentifier($identifier);
        //$blog->setSection($section);
        $blog->setContent($content);
        
        
        $manager->persist($blog);
        $manager->flush();
        
        $returns = array(
            "title" => $blog->getTitle(),
            "identifier" => $identifier,
        //    "section" => $blog->getSection(),
            "content" => $blog->getContent()
        );
        return new \Symfony\Component\HttpFoundation\Response(json_encode($returns));
    }
}
