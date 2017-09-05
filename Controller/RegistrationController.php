<?php

/*
 * This file is part of the Tecnocreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\AjaxFOSUserBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Tecnocreaciones\Bundle\AjaxFOSUserBundle\Event\FilterUserResponseEvent;
use Tecnocreaciones\Bundle\AjaxFOSUserBundle\Event\FormEvent;
use Tecnocreaciones\Bundle\AjaxFOSUserBundle\Event\GetResponseUserEvent;
use Tecnocreaciones\Bundle\AjaxFOSUserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Manejador de registro de FOSUserBundle por Ajax
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class RegistrationController extends BaseController
{
    public function registerAction()
    {
        $request = $this->container->get('request');
        if($request->isXmlHttpRequest()){
            $view = View::create();
            $view->setFormat('json');
            $response = new JsonResponse();
            
            //Backward compatibility with Fos User 1.3
            if(!class_exists('FOS\UserBundle\FOSUserEvents')){
                $form = $this->container->get('fos_user.registration.form');
                $formHandler = $this->container->get('fos_user.registration.form.handler');
                $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

                $process = $formHandler->process($confirmationEnabled);
                if ($process) {
                    $user = $form->getData();

                    $authUser = false;
                    if ($confirmationEnabled) {
                        $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                        $route = 'fos_user_registration_check_email';
                        $message = $this->trans('registration.check_email',array('%email%' => $user->getEmail()));
                    } else {
                        $authUser = true;
                        $route = 'fos_user_registration_confirmed';
                        $message = $this->trans('registration.confirmed',array('%username%' => $user->getUserName()));
                    }

                    $this->setFlash('fos_user_success', 'registration.flash.user_created');
                    $url = $this->container->get('router')->generate($route);
                    $response = new JsonResponse();
                    $data = array(
                        'message' => $message,
                        'targetUrl' => $url,
                        'authUser' => $authUser,
                    );
                    $response->setData($data);

                    if ($authUser) {
                        $this->authenticateUser($user, $response);
                    }
                    
                    return $response;
                }
            }elseif(\Symfony\Component\HttpKernel\Kernel::VERSION >= 3){
                /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
                $formFactory = $this->container->get('fos_user.registration.form.factory');
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->container->get('fos_user.user_manager');
                /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
                $dispatcher = $this->container->get('event_dispatcher');

                $user = $userManager->createUser();
                $user->setEnabled(true);

                $event = new GetResponseUserEvent($user, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

                if (null !== $event->getResponse()) {
                    return $event->getResponse();
                }

                $form = $formFactory->createForm();
                $form->setData($user);
                $form->handleRequest($request);

                if ('POST' === $request->getMethod()) {
                    if ($form->isValid()) {
                        $targetUrl = '';
                        if($this->getSession()){
                            $session = $this->getSession();
                            if(($token = $this->container->get('security.token_storage')->getToken()) && is_a($token, 'Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')){
                                $targetUrl = $session->get('_security.' .$token->getProviderKey(). '.target_path');
                            }
                        }
                        $event = new FormEvent($form, $request);
                        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                        $userManager->updateUser($user);

                        if (null === $response = $event->getResponse()) {
                            $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
                            $response = new JsonResponse();
                            $data = array(
                                'message' => $this->trans('registration.confirmed',array('%username%' => $user->getUserName())),
                                'targetUrl' => $targetUrl,
                            );
                            $response->setData($data);
                        }

                        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                        return $response;
                    }
                }
            }else{
                /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
                $formFactory = $this->container->get('fos_user.registration.form.factory');
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->container->get('fos_user.user_manager');
                /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
                $dispatcher = $this->container->get('event_dispatcher');

                $user = $userManager->createUser();
                $user->setEnabled(true);

                $event = new GetResponseUserEvent($user, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

                if (null !== $event->getResponse()) {
                    return $event->getResponse();
                }

                $form = $formFactory->createForm();
                $form->setData($user);

                if ('POST' === $request->getMethod()) {
                    $form->bind($request);

                    if ($form->isValid()) {
                        $targetUrl = '';
                        if($this->getSession()){
                            $session = $this->getSession();
                            if(($token = $this->container->get('security.context')->getToken()) && is_a($token, 'Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')){
                                $targetUrl = $session->get('_security.' .$token->getProviderKey(). '.target_path');
                            }
                        }
                        $event = new FormEvent($form, $request);
                        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                        $userManager->updateUser($user);

                        if (null === $response = $event->getResponse()) {
                            $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
                            $response = new JsonResponse();
                            $data = array(
                                'message' => $this->trans('registration.confirmed',array('%username%' => $user->getUserName())),
                                'targetUrl' => $targetUrl,
                            );
                            $response->setData($data);
                        }

                        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                        return $response;
                    }
                }
            }
            $view->setData($form);
            return $this->handleView($view);
        }else{
            return parent::registerAction($request);
        }
    }
    
    /**
     * Convert view into a response object.
     *
     * Not necessary to use, if you are using the "ViewResponseListener", which
     * does this conversion automatically in kernel event "onKernelView".
     *
     * @param View $view
     *
     * @return Response
     */
    protected function handleView(View $view)
    {
        return $this->container->get('fos_rest.view_handler')->handle($view);
    }
    
    /**
     * Sesion
     * @return  \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected function getSession()
    {
        return $this->container->get('session');
    }
    
    /**
     * 
     * @param type $message
     * @param array $params
     * @return type
     */
    private function trans($message, array $params = array())
    {
        return $this->container->get('translator')->trans($message, $params, 'FOSUserBundle');
    }
}
