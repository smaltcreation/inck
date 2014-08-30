<?php

namespace Inck\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    /**
    * @Route("/profile/{username}/preview", name="fos_user_profile_preview")
    * @Template("InckUserBundle:Profile:preview.html.twig")
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
        $articles = $repository->findByFilters(array(
                'type'      => 'published',
                'author'    => $user,
            ));

        /* On retourne la preview du profile de l'utilisateur */
        return array(
            'user' => $user,
            'articles' => $articles,
        );
    }

    /**
     * @Route("/user/autocomplete/{username}", name="inck_user_user_autocomplete", options={"expose"=true})
     */
    public function autocomplete($username)
    {
        // Récupération des utilisateurs
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('InckUserBundle:User')->getAutocompleteResults($username) ?: array();

        // Création du tableau utilisé par Select2
        $results = array();

        foreach($users as $user)
        {
            $results[] = array(
                'id'    => $user['id'],
                'text'  => $user['username'],
            );
        }

        // Renvoi des données
        return new JsonResponse(array(
            'results'   => $results,
        ));
    }
}