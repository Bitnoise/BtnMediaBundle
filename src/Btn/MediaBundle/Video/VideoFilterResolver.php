<?php

namespace Btn\MediaBundle\Video;

use Btn\MediaBundle\Model\MediaInterface;
use Btn\BaseBundle\Provider\EntityProviderInterface;

class VideoFilterResolver
{
    /** @var EntityProviderInterface */
    private $provider;
    /** @var VideoFilterManager */
    private $filterManager;

    /**
     * VideoFilterResolver constructor.
     *
     * @param $provider
     */
    public function __construct(EntityProviderInterface $provider, VideoFilterManager $filterManager)
    {
        $this->provider = $provider;
        $this->filterManager = $filterManager;
    }

    public function resolve(MediaInterface $media, $filterName)
    {
        $filter = $this->filterManager->get($filterName);

        $mediaVideoFilter = $this->provider->getRepository()->findOneByMediaAndFilterName($media, $filterName);
        if (!$mediaVideoFilter) {
            $mediaVideoFilter = $this->provider->create($media, $filterName);
            $this->provider->save($mediaVideoFilter, true);
        }

        if ($mediaVideoFilter->isComplete()) {
            return $filter->getFileName($media);
        }

        return false;
    }

    public function isResolved(MediaInterface $mediaInterface, $filterName)
    {
        return $this->resolve($mediaInterface, $filterName);
    }
}
