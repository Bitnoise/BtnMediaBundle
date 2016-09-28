<?php

namespace Btn\MediaBundle\DependencyInjection;

use Symfony\Component\HttpKernel\Kernel;
use Btn\BaseBundle\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BtnMediaExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        parent::load($configs, $container);

        $loader = $this->getConfigLoader($container);
        $loader->tryLoadFromArray(array('filters'));

        $config = $this->getProcessedConfig($container, $configs);

        $container->setParameter('btn_media.media.class', $config['media']['class']);
        $container->setParameter('btn_media.media.allowed_extensions', $config['media']['allowed_extensions']);
        $container->setParameter('btn_media.media.max_size', $config['media']['max_size']);
        $container->setParameter('btn_media.media.base_url', $config['media']['base_url']);
        $container->setParameter('btn_media.media.auto_extract', $config['media']['auto_extract']);
        $container->setParameter('btn_media.media_category.class', $config['media_category']['class']);
        $container->setParameter(
            'btn_media.media.imagine.filter_original',
            $config['media']['imagine']['filter_original']
        );
        $container->setParameter(
            'btn_media.node_content_provider.media_category',
            $config['node_content_provider']['media_category']
        );
        $container->setParameter('btn_media.media_video_filter.class', $config['media_video_filter']['class']);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        parent::prepend($container);

        $this->prependVideo($container);

        $loader = $this->getConfigLoader($container);

        if ($container->hasExtension('liip_imagine')) {
            $loader->load('liip_imagine');
        }

        // add form resource
        if ($container->hasExtension('twig')) {
            if (Kernel::VERSION_ID < 20700) {
                $container->prependExtensionConfig('twig', array(
                    'form' => array(
                        'resources' => array('BtnMediaBundle:Form:fields.html.twig')
                    ),
                ));
            } else {
                $container->prependExtensionConfig('twig', array(
                    'form_themes' => array(
                        'BtnMediaBundle:Form:fields.html.twig',
                    ),
                ));
            }
        }
    }

    private function prependVideo(ContainerBuilder $container ) {
        if (!$container->hasExtension('dubture_f_fmpeg')) {
            return;
        }

        $loader = $this->getConfigLoader($container);
        $loader->load('video_services');

        $extensionConfig = $container->getExtensionConfig('btn_media');
        if (!array_key_exists('allowed_extensions', $extensionConfig[0]['media'])) {
            $config = $this->getProcessedConfig($container);
            $container->prependExtensionConfig('btn_media', array(
                'media' => array(
                    'allowed_extensions' => array_merge($config['media']['allowed_extensions'], array('mp4')),
                ),
            ));
        }
    }
}
