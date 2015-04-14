<?php

namespace Btn\MediaBundle\Form\Type;

use Btn\AdminBundle\Form\Type\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class MediaCategoryType extends AbstractType
{
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
            'placeholder'   => 'btn_media.form.type.media_category.placeholder',
            'label'         => 'btn_media.form.type.media_category.label',
            'class'         => $this->entityProvider->getClass(),
            'query_builder' => function (EntityRepository $em) {
                return $em
                    ->createQueryBuilder('mc')
                    ->orderBy('mc.name', 'ASC')
                ;
            },
            'property' => 'name',
            'expanded' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'btn_select2_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'btn_media_category';
    }
}
