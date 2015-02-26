<?php

namespace Btn\MediaBundle\DependencyInjection;

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

        $config = $this->getProcessedConfig($container, $configs);

        $container->setParameter('btn_media.media.class', $config['media']['class']);
        $container->setParameter('btn_media.media.allowed_extensions', $config['media']['allowed_extensions']);
        $container->setParameter('btn_media.media.max_size', $config['media']['max_size']);
        $container->setParameter('btn_media.media.auto_extract', $config['media']['auto_extract']);
        $container->setParameter('btn_media.media_category.class', $config['media_category']['class']);

        $container->setParameter(
            'btn_media.node_content_provider.media_category',
            $config['node_content_provider']['media_category']
        );
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        parent::prepend($container);

        if ($container->hasExtension('liip_imagine')) {
            $loader = $this->getConfigLoader($container);
            $loader->load('liip_imagine');
        }
    }
}
