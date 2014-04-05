<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\AjaxFOSUserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Description of ValidationPass
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com.ve>
 */
class ValidationPass implements CompilerPassInterface
{
    public function process(\Symfony\Component\DependencyInjection\ContainerBuilder $container) {
        if (!$container->hasParameter('fos_user.registration.confirmation.enabled')) {
            return;
        }
        if($container->getParameter('fos_user.registration.confirmation.enabled') == true){
            $definition = $container->getDefinition('fos_user.listener.email_confirmation');
            $definition
                    ->setClass('Tecnocreaciones\Bundle\AjaxFOSUserBundle\EventListener\EmailConfirmationListener')
                    ->addMethodCall('setTranslator',array($container->getDefinition('translator.default')))
                    ;
        }
        
    }
}
