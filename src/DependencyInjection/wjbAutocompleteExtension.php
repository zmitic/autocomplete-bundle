<?php

namespace wjb\AutocompleteBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class wjbAutocompleteExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $themes = ['@wjbAutocomplete/form/autocomplete.html.twig'];
        $twigConfig = $container->getExtensionConfig('twig');

        foreach ($twigConfig as $config) {
            if (isset($config['form_themes'])) {
                $themes = array_merge($config['form_themes'], $themes);
            }
        }
        $container->prependExtensionConfig('twig', [
            'form_themes' =>  $themes,
        ]);
    }
}

