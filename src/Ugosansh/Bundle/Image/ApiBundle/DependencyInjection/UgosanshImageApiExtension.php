<?php

namespace Ugosansh\Bundle\Image\ApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class UgosanshImageApiExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (array_key_exists('chmod', $config)) {
            $container->setParameter('ugosansh_image_api.chmod', $config['chmod']);
        }

        if (array_key_exists('temp_directory', $config)) {
            $container->setParameter('ugosansh_image_api.temp_dir', $config['temp_directory']);
        }

        if (array_key_exists('target_directory', $config)) {
            $container->setParameter('ugosansh_image_api.root_dir', $config['target_directory']);
        }

        if (array_key_exists('default_source', $config)) {
            $container->setParameter('ugosansh_image_api.default_source', $config['default_source']);
        }
    }

}
