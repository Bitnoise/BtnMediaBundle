<?php

namespace Btn\MediaBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;

class IdToMediaQuietTransformer extends IdToMediaTransformer
{
    /**
     *
     */
    public function transform($id)
    {
        try {
            return parent::transform($id);
        } catch (TransformationFailedException $exception) {
            return;
        }
    }

    /**
     *
     */
    public function reverseTransform($media)
    {
        try {
            return parent::reverseTransform($media);
        } catch (TransformationFailedException $exception) {
            return;
        }
    }
}
