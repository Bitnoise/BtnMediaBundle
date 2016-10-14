<?php

namespace Btn\MediaBundle\Form\Filter;

use Btn\AdminBundle\Form\AbstractFilterForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaFilterForm extends AbstractFilterForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $category = $options['data']['category'] ? (int) $options['data']['category'] : null;

        $builder
            ->add('keyword', 'text', array(
                'label' => 'btn_media.keyword',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'btn_media.type_here',
                ),
            ))
            ->add('category', $options['category_field_hidden'] ? 'hidden' : 'btn_media_category', array(
                'required' => false,
            ))
            ->add('group', $options['media_group_field_hidden'] ? 'hidden' : 'btn_media_group', array(
                'required' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefined('media_group_field_hidden');
        $resolver->setDefault('media_group_field_hidden', false);

        $resolver->setDefined('category_field_hidden');
        $resolver->setDefault('category_field_hidden', false);
    }

}
