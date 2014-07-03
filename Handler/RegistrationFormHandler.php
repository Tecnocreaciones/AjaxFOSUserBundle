<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\AjaxFOSUserBundle\Handler;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Tecnocreaciones\Bundle\AjaxFOSUserBundle\FOSUserEvents;
use Tecnocreaciones\Bundle\AjaxFOSUserBundle\Event\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RegistrationFormHandler
{
    protected $request;
    protected $userManager;
    protected $form;
    protected $mailer;
    protected $tokenGenerator;
    protected $event_dispatcher;

    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, EventDispatcherInterface $event_dispatcher)
    {
        $this->form = $form;
        $this->request = $request;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->event_dispatcher = $event_dispatcher;
    }

    /**
     * @param boolean $confirmation
     */
    public function process($confirmation = false)
    {
        $user = $this->createUser();
        $this->form->setData($user);

        if ('POST' === $this->request->getMethod()) {
            $this->form->bind($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($user, $confirmation);

                return true;
            }
        }

        return false;
    }

    /**
     * @param boolean $confirmation
     */
    protected function onSuccess(UserInterface $user, $confirmation)
    {
        
        if ($confirmation) {
            $user->setEnabled(false);
            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $this->mailer->sendConfirmationEmailMessage($user);
        } else {
            $user->setEnabled(true);
        }
        
        $event = new FormEvent($this->form, $this->request);
        $this->event_dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
        
        $this->userManager->updateUser($user);
    }

    /**
     * @return UserInterface
     */
    protected function createUser()
    {
        return $this->userManager->createUser();
    }
}
