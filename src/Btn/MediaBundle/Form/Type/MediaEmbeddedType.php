<?php

namespace Btn\MediaBundle\Form\Type;

use Btn\AdminBundle\Form\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaEmbeddedType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'allow_add'    => true,
            'allow_delete' => true,
            'sortable'     => true,
            'by_reference' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'btn_embedded';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'btn_media_embedded';
    }
}
