<?php

namespace Inck\UserBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseController;
use FOS\UserBundle\Model\UserInterface;
use Inck\ArticleBundle\Entity\ArticleRepository;
use Inck\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends BaseController
{
    /**
     * @Template()
     */
    public function showAction()
    {
        /** @var User $user */
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $em = $this->container->get('doctrine')->getManager();
        /** @var ArticleRepository $repository */
        $repository = $em->getRepository('InckArticleBundle:Article');

        $articlesAsDraft = $repository->findByFilters(array(
            'type'      => 'as_draft',
            'author'    => $user,
        ), false);

        $articlesPublished = $repository->findByFilters(array(
            'type'      => 'published',
            'author'    => $user,
        ), false);

        $articlesPosted = $repository->findByFilters(array(
            'type'      => 'posted',
            'author'    => $user,
        ), false);

        return array(
            'user'              => $user,
            'articlesAsDraft'   => $articlesAsDraft,
            'articlesPublished' => $articlesPublished,
            'articlesPosted'    => $articlesPosted,
        );
    }
}