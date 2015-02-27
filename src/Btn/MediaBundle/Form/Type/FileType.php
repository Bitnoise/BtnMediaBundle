<?php

namespace Btn\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'placeholder' => 'btn_media.type.file.placeholder',
            'label'       => 'btn_media.type.file.label',
        ));
    }

    public function getParent()
    {
        return 'file';
    }

    public function getName()
    {
        return 'btn_media_type_file';
    }
}
