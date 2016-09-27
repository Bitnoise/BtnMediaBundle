<?php

namespace Btn\MediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncodeVideosCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('btn:media:encode_videos')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $provider = $this->getContainer()->get('btn_media.provider.media_video_filter');

        $mediaVideoFiltersToEncode = $provider->getRepository()->findAllToEncode();

        if (!$mediaVideoFiltersToEncode) {
            return;
        }

        $videoEncoder = $this->getContainer()->get('btn_media.video.encoder');

        foreach($mediaVideoFiltersToEncode as $mediaVideoFilter) {
            $mediaVideoFilter->inProgress();
            $provider->save($mediaVideoFilter, true);

            $videoEncoder->encode($mediaVideoFilter->getMedia(), $mediaVideoFilter->getFilter());

            $mediaVideoFilter->done();
            $provider->save($mediaVideoFilter, true);
        }
    }
}
