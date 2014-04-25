<?php

/*
 * This file is part of the Tecnocreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\AjaxFOSUserBundle\EventListener;

use FOS\UserBundle\EventListener\EmailConfirmationListener as BaseListener;

/**
 * Description of EmailConfirmationListener
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class EmailConfirmationListener extends BaseListener
{
    public function __construct(\FOS\UserBundle\Mailer\MailerInterface $mailer, \FOS\UserBundle\Util\TokenGeneratorInterface $tokenGenerator, \Symfony\Component\Routing\Generator\UrlGeneratorInterface $router, \Symfony\Component\HttpFoundation\Session\SessionInterface $session) {
        parent::__construct($mailer, $tokenGenerator, $router, $session);
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
    }
    /**
     * Traductor
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;


    public function onRegistrationSuccess(\FOS\UserBundle\Event\FormEvent $event) {
        if($event->getRequest()->isXmlHttpRequest()){
            /** @var $user \FOS\UserBundle\Model\UserInterface */
            $user = $event->getForm()->getData();

            $user->setEnabled(false);
            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $this->mailer->sendConfirmationEmailMessage($user);
            
            $data = array(
                'message' => $this->translator->trans('registration.check_email',array('%email%' => $user->getEmail()),'FOSUserBundle')
            );
            $event->setResponse(new \Symfony\Component\HttpFoundation\JsonResponse($data));
        }else{
            return parent::onRegistrationSuccess($event);
        }
    }

    /**
     * 
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function setTranslator(\Symfony\Component\Translation\TranslatorInterface $translator) {
        $this->translator = $translator;
    }
}
