<?php

declare(strict_types=1);

namespace Codeclipse\SortableBehaviorBundle\DependencyInjection;

use Codeclipse\SortableBehaviorBundle\Services\PositionHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Alias;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CodeclipseSortableBehaviorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('codeclipse.sortable.behavior.position.field', $config['position_field']);
        $container->setParameter('codeclipse.sortable.behavior.sortable_groups', $config['sortable_groups']);

        $positionHandler = sprintf(
            'codeclipse_sortable_behavior.position.%s',
            $config['db_driver']
        );

        $container->setAlias('codeclipse_sortable_behavior.position', new Alias($positionHandler));
        $container->getAlias('codeclipse_sortable_behavior.position')->setPublic(true);

        $container->setAlias(PositionHandler::class, new Alias($positionHandler));
        $container->getAlias(PositionHandler::class)->setPublic(true);
    }
}
