<?php

namespace Btn\MediaBundle\Video;

use Btn\MediaBundle\Model\MediaInterface;
use FFMpeg\Filters\FiltersCollection;
use FFMpeg\Media\Video;
use FFMpeg\Format\VideoInterface;

interface VideoFilterInterface
{
    /**
     * @param Video $video
     *
     * @return FiltersCollection
     */
    public function getFilterCollection(Video $video);

    /**
     * @param Video $video
     *
     * @return VideoInterface
     */
    public function getFormat(Video $video);

    /**
     * @return string
     */
    public function getExt();

    /**
     * @param MediaInterface $media
     *
     * @return mixed
     */
    public function getFileName(MediaInterface $media);
}
