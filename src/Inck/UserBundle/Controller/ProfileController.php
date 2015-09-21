<?php

namespace Inck\UserBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseController;
use FOS\UserBundle\Model\UserInterface;
use Inck\ArticleBundle\Entity\ArticleRepository;
use Inck\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends BaseController
{
    /**
     * @Route("/profile", options={"sitemap" = true})
     * @Template()
     */
    public function showAction()
    {
        /** @var User $user */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var ArticleRepository $repository */
        $repository = $this->get('inck_article.repository.article_repository');

        $articles = $repository->findBy(
            array('author'=> $user),
            array('updatedAt' => 'DESC')
        );

        return array(
            'user'      => $user,
            'articles'  => $articles,
        );
    }

    /**
     * @Route("/profile/preview/{username}", name="fos_user_profile_preview", options={"sitemap" = true})
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
        $repository = $this->get('inck_article.repository.article_repository');
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
        $badges = $em->createQuery('SELECT b, u FROM
            InckUserBundle:Badge b JOIN b.users u WHERE u.id = '.$user->getId().' ORDER BY b.lvl DESC')->setMaxResults('3')->getResult();

        return array(
            'user' => $user,
            'articles' => $articles,
            'nbFollowers' => $nbFollowers,
            'badges' => $badges
        );
    }
}
