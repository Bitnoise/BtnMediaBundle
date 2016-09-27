<?php

namespace Btn\MediaBundle\Video;

use Alchemy\BinaryDriver\Listeners\DebugListener;
use Btn\MediaBundle\Model\MediaInterface;
use FFMpeg\FFMpeg;
use Neutron\TemporaryFilesystem\TemporaryFilesystem;
use Symfony\Component\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;

class VideoEncoder
{
    /** @var FFMpeg */
    private $ffmpeg;
    /** @var FilterManager */
    private $filterManager;
    /** @var LoggerInterface */
    private $logger;
    /** @var Filesystem */
    private $fs;
    /** @var TemporaryFilesystem */
    private $tmpFs;

    /**
     * VideoEncoder constructor.
     *
     * @param FFMpeg             $ffmpeg
     * @param VideoFilterManager $filterManager
     */
    public function __construct(FFMpeg $ffmpeg, VideoFilterManager $filterManager, LoggerInterface $logger)
    {
        $this->ffmpeg = $ffmpeg;
        $this->filterManager = $filterManager;
        $this->logger = $logger;
        $this->fs = new Filesystem();
        $this->tmpFs = new TemporaryFilesystem($this->fs);
        $this->ffmpeg->getFFMpegDriver()->listen(new DebugListener());
        $this->ffmpeg->getFFMpegDriver()->on('debug', function ($message) {
            $this->logger->info($message);
        });
    }

    /**
     * @param MediaInterface $media
     * @param string         $filterName
     *
     * @throws \Exception
     */
    public function encode(MediaInterface $media, $filterName)
    {
        $file = $media->getPath();
        $ext = $media->getExt();
        $tmpInput = $this->tmpFs->createTemporaryFile('tmp-video-input', null, $ext);

        copy('gaufrette://btn_media/'.$file, $tmpInput);

        $video = $this->ffmpeg->open($tmpInput);

        $filter = $this->filterManager->get($filterName);

        $filterCollection = $filter->getFilterCollection($video);
        $format = $filter->getFormat($video);

        if ($filterCollection) {
            $video->setFiltersCollection($filterCollection);
        }

        $outputExt = $filter->getExt();
        $tmpOutput = $this->tmpFs->createTemporaryFile('tmp-video-output', null, $outputExt);

        $video->save($format, $tmpOutput);
        $outputFileName = $filter->getFileName($media);

        copy($tmpOutput, 'gaufrette://btn_media/'.$filterName.'/'.$outputFileName);

        $this->fs->remove($tmpOutput);
        $this->fs->remove($tmpInput);
    }
}
