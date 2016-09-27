<?php

namespace Btn\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class VideoFilterCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $filterManagerId = 'btn_media.video.filter_manager';

        if (!$container->hasDefinition($filterManagerId)) {
            return;
        }

        $filterManager = $container->getDefinition($filterManagerId);

        $videoFilters = $container->findTaggedServiceIds('btn_media.video_filter');
        if (!$videoFilters) {
            return;
        }

        foreach ($videoFilters as $id => $videoFilterTags) {
            foreach ($videoFilterTags as $videoFilterTag) {
                $filterManager->addMethodCall('register', array(new Reference($id), $videoFilterTag['filterName']));
            }
        }
    }
}
