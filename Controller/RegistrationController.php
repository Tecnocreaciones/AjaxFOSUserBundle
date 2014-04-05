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
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Manejador de registro de FOSUserBundle por Ajax
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com.ve>
 */
class RegistrationController extends BaseController
{
    public function registerAction(Request $request) {
        if($request->isXmlHttpRequest()){
            $view = View::create();
            $view->setFormat('json');
            $response = new JsonResponse();
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
