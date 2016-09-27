<?php

namespace Btn\MediaBundle\Video;

class VideoFilterManager
{
    /** @var array */
    private $filters = array();

    /**
     * @param VideoFilterInterface $filter
     * @param string               $filterName
     */
    public function register(VideoFilterInterface $filter, $filterName)
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
     * @return mixed
     * @throws \Exception
     */
    public function get($filterName)
    {
        if (!$this->has($filterName)) {
            throw new \Exception(sprintf('Missing video filter "%s"', $name));
        }

        return $this->filters[$filterName];
    }
}
