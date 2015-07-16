<?php

namespace Inck\UserBundle\Controller;

use FOS\UserBundle\Controller\GroupController as BaseController;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterGroupResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GroupEvent;
use FOS\UserBundle\Event\GetResponseGroupEvent;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Inck\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GroupController extends BaseController
{
    /**
     * List all group
     * @Secure(roles="ROLE_ADMIN")
     */
    public function listAction()
    {
        parent::listAction();
    }

    /**
     * Show one group
     * @Secure(roles="ROLE_USER")
     */
    public function showAction($groupName)
    {
        $group = $this->findGroupBy('name', $groupName);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if($user->hasGroup($group->getName()))
            return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:show.html.twig', array('group' => $group));
        else
            throw new AccessDeniedException("Vous n'avez pas le droit d'accèder à ce groupe");
    }

    /**
     * Show the new form
     * @Secure(roles="ROLE_USER")
     */
    public function newAction(Request $request)
    {
        /** @var $user User */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        /** @var $groupManager \FOS\UserBundle\Model\GroupManagerInterface */
        $groupManager = $this->container->get('fos_user.group_manager');
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.group.form.factory');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $group = $groupManager->createGroup('');

        $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_INITIALIZE, new GroupEvent($group, $request));

        $form = $formFactory->createForm();
        $form->setData($group);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_SUCCESS, $event);

                $groupManager->updateGroup($group);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_group_show', array('groupName' => $group->getName()));
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));

                $em = $this->container->get('doctrine')->getManager();
                $user->addGroup($group);
                $role = 'ROLE_GROUP_'.$group->getId().'_SUPER_ADMIN';
                $user->addRole($role);
                $em->persist($user);
                $em->flush();

                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:new.html.twig', array(
            'form' => $form->createview(),
        ));
    }

    /**
     * Edit one group, show the edit form
     * @Secure(roles="ROLE_USER")
     */
    public function editAction(Request $request, $groupName)
    {
        /** @var $user User */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $group = $this->findGroupBy('name', $groupName);

        if(!$user && !$user->hasRole('ROLE_SUPER_ADMIN') && !$user->hasRole('ROLE_GROUP_'.$group->getId().'_ADMIN') && !$user->hasRole('ROLE_GROUP_'.$group->getId().'_SUPER_ADMIN'))
            throw new AccessDeniedException("Vous n'avez pas le droit d'éditer ce groupe");

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $event = new GetResponseGroupEvent($group, $request);
        $dispatcher->dispatch(FOSUserEvents::GROUP_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.group.form.factory');

        $form = $formFactory->createForm();
        $form->setData($group);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $groupManager \FOS\UserBundle\Model\GroupManagerInterface */
                $groupManager = $this->container->get('fos_user.group_manager');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::GROUP_EDIT_SUCCESS, $event);

                $groupManager->updateGroup($group);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_group_show', array('groupName' => $group->getName()));
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::GROUP_EDIT_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));

                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:edit.html.twig', array(
            'form'      => $form->createview(),
            'group_name'  => $group->getName(),
        ));
    }

    /**
     * Delete one group
     * @Secure(roles="ROLE_USER")
     */
    public function deleteAction(Request $request, $groupName)
    {
        $group = $this->findGroupBy('name', $groupName);

        if(!$group)
            throw new NotFoundHttpException('Le groupe est inexistant');

        /** @var $user User */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if(!$user->hasRole('ROLE_SUPER_ADMIN') && !$user->hasRole('ROLE_GROUP_'.$group->getId().'_SUPER_ADMIN'))
            throw new AccessDeniedException("Vous n'avez pas le droit de supprimer ce groupe");

        parent::deleteAction($request, $groupName);
    }

    /**
     * @Route("/group/{groupName}/users", name="inck_user_group_users")
     * @Template()
     * @Secure(roles="ROLE_USER")
     * @param Request $request
     */
    public function usersAction(Request $request, $groupName)
    {
        $group = $this->findGroupBy('name', $groupName);
        if(!$group)
            throw new NotFoundHttpException('Le groupe est inexistant');

        /** @var $user User */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if(!$user && !$user->hasRole('ROLE_SUPER_ADMIN') && !$user->hasRole('ROLE_GROUP_'.$group->getId().'_ADMIN') && !$user->hasRole('ROLE_GROUP_'.$group->getId().'_SUPER_ADMIN'))
            throw new AccessDeniedException("Vous n'avez pas le droit d'accèder à cette page");

        $users = array($user);

        return array(
            'group' => $group,
            'users' => $users,
        );

        return array();
    }
}