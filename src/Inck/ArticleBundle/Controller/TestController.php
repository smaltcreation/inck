<?php

namespace Inck\ArticleBundle\Controller;

use Inck\ArticleBundle\Entity\ArticleRepository;
use Inck\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/test")
 */
class TestController extends Controller
{
    /**
     * @Route("/user/{id}")
     * @Template()
     * @ParamConverter("user", class="InckUserBundle:User")
     * @param User $user
     * @return array
     */
    public function indexAction(User $user)
    {
        $repository = $this->get('inck_article.repository.article_repository');

        /** @var ArticleRepository $articles */
        $articles = $repository->findByFilters(array(
            'type'      => 'published',
            'author'    => $user,
        ), false);

        return array(
            'user'      => $user,
            'articles'  => $articles,
        );
    }
}