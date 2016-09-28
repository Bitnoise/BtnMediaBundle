<?php

namespace Btn\MediaBundle\Url;

use Btn\MediaBundle\Model\MediaInterface;
use Btn\MediaBundle\Video\VideoFilterResolver;

class UrlResolver
{
    /** @var string */
    private $baseUrl;

    /** @var array */
    private $typeHandlers = [];

    /**
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/').'/';
    }

    /**
     * @param UrlResolverTypeHandlerInterface $typeHandler
     * @param string                          $type
     */
    public function addTypeHandler(UrlResolverTypeHandlerInterface $typeHandler, $type)
    {
        $this->typeHandlers[$type] = $typeHandler;
    }

    /**
     * @param MediaInterface $media
     * @param string         $filter
     *
     * @return string
     */
    public function getBrowserPath(MediaInterface $media, $filter = '')
    {
        $type = $media->getType();
        $path = $media->getPath();

        if (array_key_exists($type, $this->typeHandlers)) {
            $path = $this->typeHandlers[$type]->getBrowserPath($media, $filter);
        }

        if ($path) {
            return $this->baseUrl.($filter ? $filter.'/' : '').$path;
        }

        return '';
    }
}
