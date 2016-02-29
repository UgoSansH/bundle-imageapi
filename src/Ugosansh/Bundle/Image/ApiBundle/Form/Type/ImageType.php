<?php

namespace Ugosansh\Bundle\Image\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Image entity form type
 */
class ImageType extends AbstractType
{
    /**
     * @var string
     */
    protected $dataClass;

    /**
     * @var string
     */
    protected $defaultSource;

    /**
     * buildForm
     *
     * @param FormBuilderInterface $builder Form Builder
     * @param array                $options Form options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', [
                'required' => false
            ])
            ->add('mimeType', 'text', [
                'required' => false
            ])
        ;

        if ($options['data']->getId() <= 0) {
             $builder->add('binarySource', 'text');
        }
    }

    /**
     * getDefaultOptions
     *
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => $this->dataClass
        );
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return 'image';
    }

    /**
     * Set dataClass
     *
     * @param string $dataClass
     *
     * @return ImageType
     */
    public function setDataClass($dataClass)
    {
        $this->dataClass = $dataClass;

        return $this;
    }

    /**
     * Set defaultSource
     *
     * @param string $defaultSource
     *
     * @return ImageType
     */
    public function setDefaultSource($defaultSource)
    {
        $this->defaultSource = $defaultSource;

        return $this;
    }

}
