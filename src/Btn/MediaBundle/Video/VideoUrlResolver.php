<?php

namespace Btn\MediaBundle\Video;

use Btn\MediaBundle\Model\MediaInterface;
use Btn\BaseBundle\Provider\EntityProviderInterface;
use Btn\MediaBundle\Url\UrlResolverTypeHandlerInterface;
use Btn\MediaBundle\Video\Encoder\VideoEncoderFilterManager;

class VideoUrlResolver implements UrlResolverTypeHandlerInterface
{
    /** @var EntityProviderInterface */
    private $provider;
    /** @var VideoEncoderFilterManager */
    private $filterManager;

    /**
     * @param EntityProviderInterface   $provider
     * @param VideoEncoderFilterManager $filterManager
     */
    public function __construct(EntityProviderInterface $provider, VideoEncoderFilterManager $filterManager)
    {
        $this->provider = $provider;
        $this->filterManager = $filterManager;
    }

    /**
     * @param MediaInterface $media
     * @param string         $filterName
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function getBrowserPath(MediaInterface $media, $filterName = null)
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

        return '';
    }
}
