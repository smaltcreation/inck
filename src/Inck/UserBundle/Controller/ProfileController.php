<?php

namespace Inck\UserBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseController;
use FOS\UserBundle\Model\UserInterface;
use Inck\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends BaseController
{
    /**
     * Show the user
     */
    public function showAction()
    {
        /** @var User $user */
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $em = $this->container->get('doctrine')->getManager();
        $repository = $em->getRepository('InckArticleBundle:Article');

        $articlesAsDraft = $repository->findByFilters(array(
            'type'      => 'as_draft',
            'author'    => $user->getId(),
        ), false);

        $articlesPublished = $repository->findByFilters(array(
            'type'      => 'published',
            'author'    => $user->getId(),
        ), false);

        $articlesPosted = $repository->findByFilters(array(
            'type'      => 'posted',
            'author'    => $user->getId(),
        ), false);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Profile:show.html.'.$this->container->getParameter('fos_user.template.engine'), array('user' => $user, 'articlesAsDraft' => $articlesAsDraft, 'articlesPublished' => $articlesPublished, 'articlesPosted' => $articlesPosted));
    }
}