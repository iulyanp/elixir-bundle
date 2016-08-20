<?php

namespace Iulyanp\ElixirBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('iulyanp_elixir');

        $rootNode
            ->children()
                    ->scalarNode('web_dir')
                    ->isRequired()
                ->end()
                    ->scalarNode('build_dir')
                    ->defaultValue('build')
                ->end()
                    ->scalarNode('assets_dir')
                    ->defaultValue('app/Resources/public')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
