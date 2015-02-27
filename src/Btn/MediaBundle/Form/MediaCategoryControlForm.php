<?php

namespace Btn\MediaBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaCategoryControlForm extends AbstractForm
{
    /** @var string $actionRouteName */
    protected $actionRouteName = 'btn_media_mediacategorycontrol_create';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('name', null, array(
                'label' => 'btn_media.category.name',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'action' => $this->router->generate($this->getActionRouteName(), $this->getActionRouteParams()),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'btn_media_form_media_category_control';
    }
}
