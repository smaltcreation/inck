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
    public static $max = 5;
    private $usernames = array();

    /**
     * @var ContainerInterface
     */
    private $container;

    private function initialize()
    {
        $this->usernames = array('admin');

        for($i = 1; $i <= self::$max - 1; $i++)
        {
            $this->usernames[] = 'user'.$i;
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
            if($username === "admin")
            {
                $user->setRoles(array(User::ROLE_SUPER_ADMIN));
            }

            $manager->persist($user);
            $this->addReference('user-'.$key, $user);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}