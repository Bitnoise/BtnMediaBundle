<?php

namespace Btn\MediaBundle\Form\DataTransformer;

class IdToMediaTransformer extends MediaToIdTransformer
{
    /**
     *
     */
    public function transform($id)
    {
        return parent::reverseTransform($id);
    }

    /**
     *
     */
    public function reverseTransform($page)
    {
        return parent::transform($page);
    }
}
