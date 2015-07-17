<?php

namespace Inck\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Inck\CoreBundle\Form\Type\ContactType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home", options={"sitemap" = true})
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/about", name="inck_core_default_about", options={"sitemap" = true})
     * @Template()
     */
    public function aboutAction()
    {
        $form = $this->get('form.factory')->create(new ContactType());

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest();

            $data = $form->getData();

            //Création du message
            $message = \Swift_Message::newInstance()
                ->setContentType('text/html')
                ->setSubject($data['subject'])
                ->setFrom($data['email'])
                ->setTo('contact@smaltcreation.com');

            $date = new \DateTime('now');
            $date->setTimezone(new \DateTimeZone('Europe/Paris'));
            $frDate = $date->format('d-m-Y H:i:s');
            $header = '<strong>Email : </strong>'.$data['email'].'<br><strong>Objet : </strong>'.$data['subject'].'<br><strong>Date : </strong>'.$frDate.'<br><br>';
            $line = '------------------------------------------------------------------------------------------------'.'<br><br>';
            $body = $header.$line.$data['content'];
            $message->setBody($body);

            //Vérification du formulaire
            if($form->isValid())
            {
                //Envoi du message
                $this->get('mailer')->send($message);

                //Affichage message de succès
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Votre message a été envoyé avec succès !'
                );
            }
        }

        return array('form' => $form->createView());
    }
}
