<?php

namespace Btn\MediaBundle\Video\Encoder;

class VideoEncoderFilterManager
{
    /** @var array */
    private $filters = array();

    /**
     * @param VideoEncoderFilterInterface $filter
     * @param string                      $filterName
     */
    public function register(VideoEncoderFilterInterface $filter, $filterName)
    {
        $this->filters[$filterName] = $filter;
    }

    /**
     * @param $filterName
     *
     * @return bool
     */
    public function has($filterName)
    {
        return array_key_exists($filterName, $this->filters);
    }

    /**
     * @param $filterName
     *
     * @return VideoEncoderFilterInterface
     * @throws \Exception
     */
    public function get($filterName)
    {
        if (!$this->has($filterName)) {
            throw new \Exception(sprintf('Missing video encoder filter "%s"', $filterName));
        }

        return $this->filters[$filterName];
    }
}
