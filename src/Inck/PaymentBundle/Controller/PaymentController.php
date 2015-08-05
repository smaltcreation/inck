<?php

namespace Inck\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Buzz\Message\Request as BuzzRequest;
use Buzz\Message\Response;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;
use Inck\PaymentBundle\Entity\Transaction;

class PaymentController extends Controller
{
    private $response;

    /**
     * @Route("paypal/test")
     * @Template()
     */
    public function indexAction(){
        return $this->render('InckPaymentBundle:Default:index.html.twig');
    }

    public function getToken()
    {
        $buzz = $this->get('buzz');

        $username= $this->container->getParameter('paypal_client_id');;
        $password= $this->container->getParameter('paypal_secret');;

        $buzz->call('https://api.sandbox.paypal.com/v1/oauth2/token', BuzzRequest::METHOD_POST, array(
            'Accept' => 'application/json',
            'Accept-language' => 'en_US',
            'Authorization' => 'Basic '.base64_encode($username.':'.$password)
        ), http_build_query(array(
            'grant_type' => 'client_credentials'
        )));
        $this->response = json_decode($buzz->getLastResponse()->getContent());

        return $this->response->access_token;
    }

    /**
     * @Route("/paypal/card", name="inck_payment_creditCard")
     * @Template()
     */
    public function callCardAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('card_number', 'text', [
                'constraints' => new Assert\Luhn(),
                'attr' => [
                    'minLength' => 16,
                    'maxLength' => 16,
                    'placeholder' => '4111111111111111'
                ]
            ])
            ->add('card_type', 'afe_select2_choice', [
                'required'      => true,
                'choices'       => [
                    'visa'       => 'Visa',
                    'mastercard' => 'Mastercard',
                    'amex'     => 'Amex',
                    'discover'   => 'Discover',
                ],
                'multiple'      => false,
            ])
            ->add('expire_month', 'integer', [
                'attr' => [
                    'min' => 0,
                    'max' => 12,
                    'placeholder' => '02'
                ]
            ])
            ->add('expire_year', 'integer', [
                'attr' => [
                    'min' => 2015,
                    'placeholder' => '2017'
                ]
            ])
            ->add('cvv2', 'integer', [
                'constraints' => new Length(array('min' => 3, 'max' => 4)),
                'attr' => [
                    'min' => 0,
                    'minLength' => 3,
                    'maxLength' => 4,
                    'placeholder' => '123'
                ]
            ])
            ->add('first_name', 'text', [
                'attr' => [
                    'placeholder' => 'Anakin'
                ]
            ])
            ->add('last_name', 'text', [
                'attr' => [
                    'placeholder' => 'Skywalker'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $buzz = $this->get('buzz');

            $buzz->call('https://api.sandbox.paypal.com/v1/payments/payment', BuzzRequest::METHOD_POST, array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->getToken(),
            ), json_encode(array(
                'intent' => 'sale',
                'payer' => array(
                    'payment_method' => 'credit_card',
                    'funding_instruments' => array(
                        array(
                            'credit_card' => array(
                                'number' => $data['card_number'],
                                'type' => $data['card_type'],
                                'expire_month' => $data['expire_month'],
                                'expire_year' => $data['expire_year'],
                                'cvv2' => $data['cvv2'],
                                'first_name' => $data['first_name'],
                                'last_name' => $data['last_name']
                            )
                        )
                    )
                ),
                'transactions' => array(
                    array(
                        'amount' => array(
                            'total' => 8.47,
                            'currency' => 'EUR'
                        ),
                        'description' => 'This is the payment transaction description'
                    )
                )
            )));

            $response = json_decode($buzz->getLastResponse()->getContent());
            if (isset($response->state) && $response->state == 'approved') {
                $transaction = new Transaction();
                $transaction->setTransactionId($response->id);
                $transaction->setBuyer($this->getUser());
                $transaction->setCreatedAt(new \DateTime($response->create_time));
                $transaction->setAmountNetOfTax(json_decode($response->transactions[0]->amount->total));
                $transaction->setValue(1);
                $transaction->setSubject('credits');

                $em= $this->getDoctrine()->getManager();
                $em->persist($transaction);
                $em->flush();
                $request->getSession()->getFlashBag()->add('success', 'Payment has been approved!');
                return $this->render('InckPaymentBundle:Default:index.html.twig');
            }
            else{
                $error = $response->message."\n";
                if(isset($response->details)){
                    foreach ($response->details as $detail) {
                        $error .= $detail->issue . "\n";
                    }
                }
                $request->getSession()->getFlashBag()->add('danger', $error);
                return $this->render('InckPaymentBundle:Default:index.html.twig');
            }

        }

        return $this->render('InckPaymentBundle:CreditCard:index.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
