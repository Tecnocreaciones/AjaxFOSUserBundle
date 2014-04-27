<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\AjaxFOSUserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Description of ServicePass
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class ServicePass implements CompilerPassInterface
{
    public function process(\Symfony\Component\DependencyInjection\ContainerBuilder $container) {
        //Backward compatibility with Fos User 1.3
        if(class_exists('FOS\UserBundle\FOSUserEvents')){
            $fosUserListenerFlash = new \Symfony\Component\DependencyInjection\Definition();
            $fosUserListenerFlash
                    ->setClass('Tecnocreaciones\Bundle\AjaxFOSUserBundle\EventListener\FlashListener')
                    ->addArgument($container->getDefinition('session'))
                    ->addArgument($container->getAlias('translator'))
                    ->setTags(array('kernel.event_subscriber'));
        }
    }
}
