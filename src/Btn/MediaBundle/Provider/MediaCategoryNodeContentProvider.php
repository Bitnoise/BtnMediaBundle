<?php

namespace Btn\MediaBundle\Provider;

use Btn\NodeBundle\Provider\NodeContentProviderInterface;
use Btn\MediaBundle\Form\NodeContentType;
use Btn\BaseBundle\Provider\EntityProviderInterface;

class MediaCategoryNodeContentProvider implements NodeContentProviderInterface
{
    /** @var \Btn\BaseBundle\Provider\EntityProviderInterface */
    protected $provider;
    protected $configuration;

    /**
     *
     */
    public function __construct(EntityProviderInterface $provider, array $configuration)
    {
        $this->provider = $provider;
        $this->configuration = $configuration;
    }

    /**
     *
     */
    public function isEnabled()
    {
        return $this->configuration['enabled'];
    }

    /**
     *
     */
    public function getForm()
    {
        $medias = $this->provider->getRepository()->findAll();

        $data = array();
        foreach ($medias as $media) {
            $data[$media->getId()] = $media->getName();
        }

        return new NodeContentType($data);
    }

    /**
     *
     */
    public function resolveRoute($formData = array())
    {
        return $this->configuration['route_name'];
    }

    /**
     *
     */
    public function resolveRouteParameters($formData = array())
    {
        return isset($formData['category']) ? array('id' => $formData['category']) : array();
    }

    /**
     *
     */
    public function resolveControlRoute($formData = array())
    {
        return 'btn_media_mediacontrol_media_index_category';
    }

    /**
     *
     */
    public function resolveControlRouteParameters($formData = array())
    {
        return isset($formData['category']) ? array('category' => $formData['category']) : array();
    }

    /**
     *
     */
    public function getName()
    {
        return 'btn_media.media_category_node_content_provider.name';
    }
}
