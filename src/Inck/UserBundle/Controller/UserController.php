<?php

namespace Inck\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/profile")
 */
class UserController extends Controller
{
    /**
    * @Route("/{username}/preview", name="fos_user_profile_preview")
    */
    public function previewAction($username)
    {
        /* On récupère l'utilisateur en fonction de l'username unique */
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('InckUserBundle:User');
        $user = $repository->findOneBy(array('username' => $username));
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur inexistant.');
        }

        /* S'il existe, on récupère ses articles publiés */
        $repository = $em->getRepository('InckArticleBundle:Article');
        $articles = $repository->superQuery('published', $user);

        /* On retourne la preview du profile de l'utilisateur */
        return $this->render('InckUserBundle:Profile:preview.html.twig', array(
            'user' => $user,
            'articles' => $articles,
        ));
    }
}