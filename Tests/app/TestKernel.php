<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class TestKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array();

        if ('test' === $this->getEnvironment()) {
            $bundles[] = new \Symfony\Bundle\TwigBundle\TwigBundle();
//            $bundles[] = new \Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle();
            $bundles[] = new Symfony\Bundle\FrameworkBundle\FrameworkBundle();
            $bundles[] = new Doctrine\Bundle\DoctrineBundle\DoctrineBundle();
            $bundles[] = new wjb\AutocompleteBundle\wjbAutocompleteBundle();
        }

        return $bundles;
    }

    /**
     * @param LoaderInterface $loader
     *
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/test_config.yml');
    }

    public function getCacheDir()
    {
        return $this->rootDir.'/../var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return $this->rootDir.'/../var/logs/'.$this->environment;
    }

//    public function getProjectDir()
//    {
//        dump(parent::getRootDir());die;
//    }


}

