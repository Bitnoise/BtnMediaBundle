<?php

namespace Btn\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('btn_media');

        $rootNode
            ->children()
                ->arrayNode('media')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->cannotBeEmpty()->defaultValue('Btn\\MediaBundle\\Entity\\Media')->end()
                        ->arrayNode('allowed_extensions')
                            ->defaultValue(array('jpeg', 'jpg', 'png', 'zip', 'pdf'))
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->scalarNode('max_size')->defaultValue(null)->example('10M')->end()
                        ->booleanNode('auto_extract')->defaultValue(true)->end()
                        ->scalarNode('base_url')->defaultValue(null)->end()
                        ->arrayNode('imagine')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('filter_original')->defaultValue('btn_media_original')->end()
                            ->end()
                        ->end()

                        ->arrayNode('groups')->defaultValue(
                            array(
                                array(
                                    'name' => 'images',
                                    'label' => 'btn_media.media.groups.images',
                                    'mime_types' => array('image/jpeg', 'image/jpg', 'image/png'),
                                ),
                                array(
                                    'name' => 'documents',
                                    'label' => 'btn_media.media.groups.documents',
                                    'mime_types' => array('application/pdf'),
                                ),
                            )
                        )
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')->defaultValue(null)->end()
                                    ->scalarNode('label')->isRequired()->end()
                                    ->arrayNode('mime_types')
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                    ->end()
                ->end()
                ->arrayNode('media_category')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')
                            ->cannotBeEmpty()
                            ->defaultValue('Btn\\MediaBundle\\Entity\\MediaCategory')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('media_video_filter')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->cannotBeEmpty()->defaultValue('Btn\\MediaBundle\\Entity\\MediaVideoFilter')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addNodeContentProvider($rootNode);

        return $treeBuilder;
    }

    /**
     *
     */
    private function addNodeContentProvider(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('node_content_provider')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('media_category')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->scalarNode('route_name')->defaultValue('btn_media_media_category')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
