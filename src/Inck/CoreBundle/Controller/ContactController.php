<?php

namespace Inck\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Inck\CoreBundle\Form\Type\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ContactController extends Controller
{
    /**
     * @Route("/contact", name="inck_core_contact")
     */
    public function indexAction()
    {
        $form = $this->get('form.factory')->create(new ContactType());

        $request = $this->get('request');

        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);

            $data = $form->getData();

            //Création du message
            $message = \Swift_Message::newInstance()
                ->setContentType('text/html')
                ->setSubject($data['subject'])
                ->setFrom($data['email'])
                ->setTo('spydey007@gmail.com');

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

        return $this->render('InckCoreBundle:Contact:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}