<?php

namespace Btn\MediaBundle\Form\Type;

use Btn\AdminBundle\Form\Type\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Btn\MediaBundle\Form\DataTransformer\IdToMediaTransformer;
use Btn\MediaBundle\Form\DataTransformer\IdToMediaQuietTransformer;
use Btn\MediaBundle\Model\MediaInterface;
use Doctrine\ORM\EntityRepository;

class MediaType extends AbstractType
{
    /** @var string $modalRouteName */
    private $modalRouteName = 'btn_media_mediacontrol_modal';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if (!empty($options['data_class'])) {
            // add view transformer duo form exception
            $builder->addViewTransformer(new IdToMediaTransformer($this->entityProvider));
        } else {
            $builder->addModelTransformer(new IdToMediaQuietTransformer($this->entityProvider));
        }

        $this->assetLoader->load('btn_media_modal_js');
        $this->assetLoader->load('btn_media_preview_css');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setOptional(array(
            'preview',
            'media_group',
        ));

        $resolver->setDefaults(array(
            'label'         => 'btn_media.form.type.media.label',
            'empty_value'   => 'btn_media.form.type.media.empty_value',
            'preview'       => false,
            'class'         => $this->class,
            'data_class'    => $this->class,
            'attr'          => array(
                'btn-media-select'   => $this->trans('btn_media.form.type.media.select'),
                'btn-media-edit'     => $this->trans('btn_media.form.type.media.edit'),
                'btn-media-edit-url' => $this->router->generate('btn_media_mediacontrol_media_edit', array('id' => 0)),
                'btn-media-delete'   => $this->trans('btn_media.form.type.media.remove'),
                ),
            'query_builder' => function (EntityRepository $em) {
                return $em
                    ->createQueryBuilder('mf')
                    ->orderBy('mf.name', 'ASC')
                ;
            },
            'property' => 'choiceLabel',
            'required' => true,
            'expanded' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        // correct value from data transformer for choice to select correctly
        if (!empty($options['data_class']) && $view->vars['value'] instanceof MediaInterface) {
            $view->vars['value'] = (string) $view->vars['value']->getId();
        }

        $view->vars['preview'] = $options['preview'] ? true : false;
        $view->vars['preview_filter'] = is_string($options['preview']) ? $options['preview'] : 'btn_media_thumb';

        if (!array_key_exists('btn-media', $view->vars)) {
            $params = array('filter' => array());
            if (array_key_exists('media_group', $options)) {
                // $params['filter']['group'] = $options['media_group'];
                $params['group'] = $options['media_group'];
            }
            $view->vars['attr']['btn-media'] = $this->router->generate($this->getModalRouteName(), $params);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'btn_media';
    }

    /**
     * Get modal action route name
     *
     * @return string
     */
    public function getModalRouteName()
    {
        return $this->modalRouteName;
    }
}
