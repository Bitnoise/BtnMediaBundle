<?php
namespace Btn\MediaBundle\Twig;

use Btn\MediaBundle\Url\UrlResolver;

class MediaUrlExtension extends \Twig_Extension
{
    /** @var UrlResolver */
    private $urlResolver;

    public function __construct(UrlResolver $urlResolver)
    {
        $this->urlResolver = $urlResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(new \Twig_SimpleFilter('btn_get_media_url', array($this, 'getMediaUrl')),);
    }

    public function getMediaUrl($input, $filter)
    {
        return $this->urlResolver->getBrowserPath($input, $filter);
    }

    public function getName()
    {
        return 'btn.media.media_url_extensions';
    }
}
