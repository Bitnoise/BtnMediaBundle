<?php

namespace Btn\MediaBundle\Form\Type;

use Btn\AdminBundle\Form\Type\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaGroupType extends AbstractType
{
    private $groups;

    /**
     * @param $groups
     */
    public function __construct($groups)
    {
        $this->groups = $groups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'placeholder' => 'btn_media.form.type.media_group.placeholder',
            'label' => 'btn_media.form.type.media_group.label',
            'expanded' => false,
            'choices' => $this->generateChoices(),
        ));
    }

    private function generateChoices() {
        $choices = array();
        foreach ($this->groups as $group) {
            $choices[$group['name']] = $group['label'];
        }

        return $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'btn_select2_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'btn_media_group';
    }
}
