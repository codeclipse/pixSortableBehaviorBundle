<?php

declare(strict_types=1);

namespace Codeclipse\SortableBehaviorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $supportedDrivers = ['orm', 'mongodb'];

        $treeBuilder = new TreeBuilder('codeclipse_sortable_behavior');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('db_driver')
                ->info(sprintf(
                    'These following drivers are supported: %s',
                    implode(', ', $supportedDrivers)
                ))
                ->validate()
                    ->ifNotInArray($supportedDrivers)
                    ->thenInvalid('The driver "%s" is not supported. Please choose one of ('.implode(', ', $supportedDrivers).')')
                ->end()
                ->cannotBeOverwritten()
                ->cannotBeEmpty()
                ->defaultValue('orm')
            ->end()

            ->arrayNode('position_field')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('default')
                        ->defaultValue('position')
                    ->end()
                    ->arrayNode('entities')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()

            ->arrayNode('sortable_groups')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('entities')
                        ->useAttributeAsKey('name')
                        ->prototype('variable')
                        ->end()
                    ->end()
                ->end()
            ->end()

        ;

        return $treeBuilder;
    }
}
