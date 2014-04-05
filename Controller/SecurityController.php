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

use FOS\UserBundle\Controller\SecurityController as BaseController;

/**
 * Description of SecurityController
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com.ve>
 */
class SecurityController extends BaseController
{
    /**
     * Agrega el token para el registro de usuario al formulario de inicio de sesion para ofrecer registro desde el mismo
     * @param array $data
     * @return type
     */
    protected function renderLogin(array $data) {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        $form = $formFactory->createForm();
        $csrf_token_register = $form->createView()->children['_token']->vars['value'];
        $data['csrf_token_register'] = $csrf_token_register;
        
        return parent::renderLogin($data);
    }
}
