<?php

namespace Inck\UserBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseController;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends BaseController
{
    /**
     * Show the user
     */
    public function showAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $em = $this->container->get('doctrine')->getManager();
        $repository = $em->getRepository('InckArticleBundle:Article');

        $articlesAsDraft = $repository->findByFilters(array(
            'filters'   => array(
                'type'      => 'as_draft',
                'authors'   => array(
                    $user,
                ),
            ),
        ));

        $articlesPublished = $repository->findByFilters(array(
            'filters'   => array(
                'type'      => 'published',
                'authors'   => array(
                    $user,
                ),
            ),
        ));

        $articlesPosted = $repository->findByFilters(array(
            'filters'   => array(
                'type'      => 'posted',
                'authors'   => array(
                    $user,
                ),
            ),
        ));

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Profile:show.html.'.$this->container->getParameter('fos_user.template.engine'), array('user' => $user, 'articlesAsDraft' => $articlesAsDraft, 'articlesPublished' => $articlesPublished, 'articlesPosted' => $articlesPosted));
    }
}