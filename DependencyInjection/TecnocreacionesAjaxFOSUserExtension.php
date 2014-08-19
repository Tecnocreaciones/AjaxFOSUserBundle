<?php

namespace Tecnocreaciones\Bundle\AjaxFOSUserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TecnocreacionesAjaxFOSUserExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        
        //Compatibilidad con fos user 1.3
        if(!class_exists('FOS\UserBundle\FOSUserEvents')){
            $registrationFormHandlerDefinition = new Definition("Tecnocreaciones\Bundle\AjaxFOSUserBundle\Handler\RegistrationFormHandler");
            $registrationFormHandlerDefinition->setScope("request");
            $registrationFormHandlerDefinition->setArguments(array(
                new Reference("fos_user.registration.form"),
                new Reference("request"),
                new Reference("fos_user.user_manager"),
                new Reference("fos_user.mailer"),
                new Reference("fos_user.util.token_generator"),
                new Reference("event_dispatcher"),
            ));
            $container->setDefinition("fos_user.registration.form.handler.default", $registrationFormHandlerDefinition);
        }
    }
}
