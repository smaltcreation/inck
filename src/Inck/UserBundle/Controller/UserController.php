<?php

namespace Inck\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Inck\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends Controller
{
    /**
     * @Route("/profile/{username}/preview", name="fos_user_profile_preview")
     * @Template("InckUserBundle:Profile:preview.html.twig")
     * @param string $username
     * @return array
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
        $articles = $repository->findByFilters(
            array(
                'type'      => 'published',
                'author'    => $user,
            )
        );

        /* On compte le nombre de followers */
        $repository = $em->getRepository('InckSubscriptionBundle:UserSubscription');
        $nbFollowers = $repository->countByUser($user);

        /* On retourne la preview du profile de l'utilisateur */
        return array(
            'user' => $user,
            'articles' => $articles,
            'nbFollowers' => $nbFollowers
        );
    }

    /**
     * @Route("/user/autocomplete/{input}", name="inck_user_user_autocomplete", options={"expose"=true})
     */
    public function autocomplete($input)
    {
        // Récupération des utilisateurs
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('InckUserBundle:User')->getAutocompleteResults($input) ?: array();

        // Création du tableau utilisé par Select2
        $results = array();

        foreach($users as $user)
        {
            if($user['firstname'] && $user['lastname'])
            {
                $text = sprintf('%s %s (%s)', $user['firstname'], $user['lastname'], $user['username']);
            }

            else
            {
                $text = $user['username'];
            }

            $results[] = array(
                'id'    => $user['id'],
                'text'  => $text,
            );
        }

        // Renvoi des données
        return new JsonResponse(array(
            'results'   => $results,
        ));
    }

    /**
     * @Route("user/{id}/disable", name="inck_user_user_disable", requirements={"id" = "\d+"})
     * @ParamConverter("user", options={"mapping": {"id": "id"}})
     * @Secure(roles="ROLE_ADMIN")
     * @param User $user
     * @return RedirectResponse
     */
    public function disable(User $user)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $user->setEnabled(false);
            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Utilisateur désactivé avec succès !'
            );
        }
        catch(\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                $e
            );
        }

        return $this->redirect($this->generateUrl('inck_core_admin_index'));
    }
}