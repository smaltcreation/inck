<?php

namespace Inck\UserBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inck\UserBundle\Entity\User;

class UsersToUsernamesTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an ArrayCollection (users) to a string (usernames).
     *
     * @param  ArrayCollection|null $users
     * @return string
     */
    public function transform($users)
    {
        if (!$users) {
            return '';
        }

        // Récupération des pseudos
        $usernames = array();

        /** @var User $user */
        foreach($users as $user)
        {
            $usernames[] = $user->getUsername();
        }

        return implode(',', $usernames);
    }

    /**
     * Transforms a string (usernames) to an ArrayCollection (users).
     *
     * @param  string $usernames
     * @return ArrayCollection|null
     */
    public function reverseTransform($usernames)
    {
        if (!$usernames) {
            return new ArrayCollection();
        }

        $usernames = explode(',', $usernames);

        // Récupération des utilisateurs existants
        $users = $this->om
            ->getRepository('InckUserBundle:User')
            ->findWhereUsernameIn($usernames)
        ;

        return $users;
    }
}