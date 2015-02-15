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
        } catch(TransformationFailedException $exception) {
            return null;
        }
    }

    /**
     *
     */
    public function reverseTransform($page)
    {
        try {
            return parent::reverseTransform($page);
        } catch(TransformationFailedException $exception) {
            return null;
        }
    }
}
