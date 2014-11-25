<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\AjaxFOSUserBundle\Controller;

/**
 * Metodos para extender la funcionalidad
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class ExtraController extends \Symfony\Bundle\FrameworkBundle\Controller\Controller {
    
    function reSendEmailActivateAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $data = array();
        $activateForm = $request->get('activate_form');
        $message = $email= '';
        if($activateForm != null){
            $email = $activateForm['email'];
            $userMailer = $this->get('fos_user.mailer');
            $userManager = $this->get('fos_user.user_manager');
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            
            $user = $userManager->findOneBy(array(
                'email' => $email
            ));
            if($user){
                if($user->isEnabled() === false){
                    if (null === $user->getConfirmationToken()) {
                        $user->setConfirmationToken($tokenGenerator->generateToken());
                        $userManager->updateUser($user);
                    }
                    $userMailer->sendConfirmationEmailMessage($user);
                    $message = 'ajax_fos_user.success.send_activation_activation_link';
                }else{
                    $message = 'ajax_fos_user.error.already_activated';
                }
            }else{
                $message = 'ajax_fos_user.error.user_not_exist';
            }
        }else{
            $message = 'ajax_fos_user.error.not_email';
        }
        $data['message'] = $this->get('translator')->trans($message,array('%email%' => $email),'TecnocreacionesAjaxFOSUserBundle');
        
        return new \Symfony\Component\HttpFoundation\JsonResponse($data);
    }
}
