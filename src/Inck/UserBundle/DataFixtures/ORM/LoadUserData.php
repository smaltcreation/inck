<?php

namespace Inck\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inck\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
    /**
     * Nombre total d'utilisateurs
     */
    const MAX = 20;

    /**
     * Préfixe de la référence d'un utilisateur
     */
    const REFERENCE_PREFIX = 'user-';

    /**
     * Noms des utilisateurs
     * @var array
     */
    private $usernames;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Génère les noms des utilisateurs
     */
    private function initialize()
    {
        $this->usernames = array(1 => 'admin');

        for($i = 2; $i <= self::MAX; $i++)
        {
            $this->usernames[$i] = 'user'.($i - 1);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->initialize();

        foreach($this->usernames as $key => $username)
        {
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($username.'@inck.com');
            $user->setEnabled(true);

            // Mot de passe
            $encoder = $this->container
                ->get('security.encoder_factory')
                ->getEncoder($user)
            ;

            $user->setPassword($encoder->encodePassword($username, $user->getSalt()));

            // Role
            if($username === 'admin')
            {
                $user->setRoles(array(User::ROLE_SUPER_ADMIN));
            }

            $manager->persist($user);
            $this->addReference(self::REFERENCE_PREFIX.$key, $user);
        }

        $manager->flush();
    }
}