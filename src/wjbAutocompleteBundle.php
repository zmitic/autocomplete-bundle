<?php

namespace wjb\AutocompleteBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use wjb\AutocompleteBundle\DependencyInjection\Compiler\AutocompleteConfigLocatorPass;

class wjbAutocompleteBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new AutocompleteConfigLocatorPass());
    }
}

