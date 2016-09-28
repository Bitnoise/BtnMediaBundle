<?php

namespace Btn\MediaBundle\Video\Encoder;

use Btn\MediaBundle\Model\MediaInterface;
use FFMpeg\Media\Video;
use FFMpeg\Format\Video\X264;
use Psr\Log\LoggerInterface;

abstract class AbstractVideoEncoderFilter implements VideoEncoderFilterInterface
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldEncode(Video $video)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat(Video $video)
    {
        $format = new X264();

        $bitreate = (int) $video->getStreams()->videos()->first()->get('bit_rate');
        if ($bitreate) {
            $format->setKiloBitrate($bitreate / 1024);
        }

        if ( $this->logger) {
            $format->on('progress', function ($video, $format, $percentage) {
                $this->logger->info(sprintf('%s%% transcoded', $percentage));
            });
        }

        return $format;
    }

    /**
     * {@inheritdoc}
     */
    public function getExt()
    {
        return 'mp4';
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName(MediaInterface $media)
    {
        return $media->getBaseFileName().'.'.$this->getExt();
    }
}
