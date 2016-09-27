<?php

namespace Btn\MediaBundle\Url;

use Btn\MediaBundle\Model\MediaInterface;
use Btn\MediaBundle\Video\VideoFilterResolver;

class UrlResolver
{
    /** @var VideoFilterResolver */
    private $videoFilterResolver;

    /** @var string */
    private $baseUrl;

    public function __construct($baseUrl, VideoFilterResolver $videoFilterResolver)
    {
        $this->baseUrl = rtrim($baseUrl, '/').'/';
        $this->videoFilterResolver = $videoFilterResolver;
    }

    /**
     * @param MediaInterface $media
     * @param string         $filter
     *
     * @return string
     */
    public function getBrowserPath(MediaInterface $media, $filter = '')
    {
        $path = $media->getPath();

        if ($media->isVideo()) {
            $path = $this->videoFilterResolver->resolve($media, $filter);
        }

        if ($path) {
            return $this->baseUrl.($filter ? $filter.'/' : '').$path;
        }

        return '';
    }
}
