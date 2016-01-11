<?php

namespace Btn\MediaBundle\Form\Filter;

use Btn\AdminBundle\Form\AbstractFilterForm;
use Symfony\Component\Form\FormBuilderInterface;

class MediaFilterForm extends AbstractFilterForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $category = $options['data']['category'] ? (int) $options['data']['category'] : null;

        $builder
            ->add('keyword', 'text', array(
                'label' => 'bostons_control.keyword',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'bostons_control.type_here',
                ),
            ))
            ->add('category', 'btn_media_category', array(
                'required' => false,
            ))
        ;
    }
}
