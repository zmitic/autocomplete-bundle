<?php

namespace wjb\AutocompleteBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AutocompleteConfigLocatorPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('wjb_autocomplete.autocomplete_config_locator')) {
            return;
        }

        $definition = $container->getDefinition('wjb_autocomplete.autocomplete_config_locator');
        /** @var Reference[] $taggedConfigDefinitions */
        $taggedConfigDefinitions = $this->findAndSortTaggedServices('wjb_autocomplete.config', $container);

        $configs = [];
        foreach ($taggedConfigDefinitions as $taggedConfigDefinition) {
            $configs[(string)$taggedConfigDefinition] = $taggedConfigDefinition;
        }

        $definition->setArgument(0, $configs);
    }
}

