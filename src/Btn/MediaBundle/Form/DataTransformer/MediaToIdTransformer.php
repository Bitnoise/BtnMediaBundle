<?php

namespace Btn\MediaBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Btn\BaseBundle\Provider\EntityProviderInterface;
use Btn\MediaBundle\Model\MediaInterface;

class MediaToIdTransformer implements DataTransformerInterface
{
    /** @var \Btn\BaseBundle\Provider\EntityProviderInterface $entityProvider  */
    protected $entityProvider;

    /**
     *
     */
    public function __construct(EntityProviderInterface $entityProvider)
    {
        $this->entityProvider = $entityProvider;
    }

    /**
     *
     */
    public function transform($page)
    {
        if (null === $media) {
            return "";
        }

        if ($media instanceof MediaInterface) {
            return $media->getId();
        }

        return $media;
    }

    /**
     *
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        if ($id instanceof MediaInterface) {
            return $id;
        }

        $media = $this->entityProvider->getRepository()->find($id);

        if (null === $media) {
            throw new TransformationFailedException(sprintf('An media with id "%s" does not exist!', $id));
        }

        return $media;
    }
}
