<?php

namespace Btn\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class VideoEncoderFilterCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $filterManagerId = 'btn_media.video.encoder.video_encoder_filter_manager';

        if (!$container->hasDefinition($filterManagerId)) {
            return;
        }

        $filterManager = $container->getDefinition($filterManagerId);

        $videoEncoderFilters = $container->findTaggedServiceIds('btn_media.video_encoder_filter');
        if (!$videoEncoderFilters) {
            return;
        }

        foreach ($videoEncoderFilters as $id => $videoEncoderFilterTags) {
            foreach ($videoEncoderFilterTags as $videoEncoderFilterTag) {
                $filterManager->addMethodCall('register', array(new Reference($id), $videoEncoderFilterTag['filterName']));
            }
        }
    }
}
