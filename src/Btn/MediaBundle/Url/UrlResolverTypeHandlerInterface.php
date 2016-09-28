<?php

namespace Btn\MediaBundle\Url;

use Btn\MediaBundle\Model\MediaInterface;

interface UrlResolverTypeHandlerInterface
{
    public function getBrowserPath(MediaInterface $media, $filter = null);
}
