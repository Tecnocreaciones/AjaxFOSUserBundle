<?php

namespace Tecnocreaciones\Bundle\AjaxFOSUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tecnocreaciones\Bundle\AjaxFOSUserBundle\DependencyInjection\Compiler\ValidationPass;

class TecnocreacionesAjaxFOSUserBundle extends Bundle
{
    public function build(ContainerBuilder $container) {
        parent::build($container);
        $container->addCompilerPass(new ValidationPass());
        $container->addCompilerPass(new DependencyInjection\Compiler\ServicePass());
    }
    
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
