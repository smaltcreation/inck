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

        $em = $this->container->get('doctrine')->getManager();
        /** @var ArticleRepository $repository */
        $repository = $em->getRepository('InckArticleBundle:Article');

        $articles = $repository->findBy(
            array('author'=> $user),
            array('updatedAt' => 'DESC')
        );

        return array(
            'user'      => $user,
            'articles'  => $articles,
        );
    }
}