<?php

namespace Inck\ArticleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Entity\Vote;
use Inck\UserBundle\DataFixtures\ORM\LoadUserData;
use Inck\UserBundle\Entity\User;

class LoadVoteData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * Nombre total de votes
     * @return int
     */
    public function getMax()
    {
        return (LoadArticleData::MAX * LoadUserData::MAX) - (LoadArticleData::MAX * LoadUserData::MAX / 100 * 10);
    }

    /**
     * Préfixe de la référence d'un vote
     */
    const REFERENCE_PREFIX = 'vote-';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $votes = array();

        for($key = 1; $key <= self::getMax(); $key++)
        {
            $vote = new Vote();

            $vote->setUp(boolval(rand(0, 1)));
            $vote->setSubmittedOn(LoadArticleData::getRandomDate());

            $userKey = null;
            $articleKey = null;

            do
            {
                $userKey    = rand(1, LoadUserData::MAX);
                $articleKey = rand(1, LoadArticleData::MAX);
                $finalKey   = "$userKey-$articleKey";
            }
            while(isset($votes[$finalKey]));

            $votes[$finalKey] = true;

            /** @var User $user */
            $user = $this->getReference(LoadUserData::REFERENCE_PREFIX.$userKey);
            $vote->setUser($user);

            /** @var Article $article */
            $article = $this->getReference(LoadArticleData::REFERENCE_PREFIX.$articleKey);
            $vote->setArticle($article);

            $manager->persist($vote);
            $this->addReference(self::REFERENCE_PREFIX.$key, $vote);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Inck\UserBundle\DataFixtures\ORM\LoadUserData',
            'Inck\ArticleBundle\DataFixtures\ORM\LoadArticleData',
        );
    }
}