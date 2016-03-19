<?php

namespace Gravity\CmsBundle\Field\Type\Choice\Widget\Select;

use Doctrine\Common\Collections\ArrayCollection;
use Gravity\CmsBundle\Entity\FieldChoice;
use Gravity\CmsBundle\Field\AbstractFieldWidgetDefinition;
use Gravity\CmsBundle\Field\FieldDefinitionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SelectWidget
 *
 * @package Gravity\CmsBundle\Field\Type\Choice\Widget\Select
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class SelectWidget extends AbstractFieldWidgetDefinition
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'choice.select';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Dropdown Box';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Choice Using a Dropdown Box';
    }

    /**
     * Get the form type for the widget
     *
     * @return AbstractType|string
     */
    public function getForm()
    {
        return new SelectWidgetForm();
    }

    /**
     * @param FormMapper               $formMapper
     * @param FieldDefinitionInterface $fieldDefinition
     * @param string                   $field
     * @param array                    $fieldOptions
     * @param array                    $widget
     * @param array                    $widgetOptions
     */
    public function configureForm(
        FormMapper $formMapper,
        FieldDefinitionInterface $fieldDefinition,
        $field,
        array $fieldOptions,
        $widget,
        array $widgetOptions
    ) {
        $isMultiple = $fieldOptions['limit'] > 1 || $fieldOptions['limit'] < 0;

        $formMapper->add(
            $field,
            'choice',
            [
                'multiple' => true,
                'expanded' => false,
                'choices'  => $fieldOptions['choices'],
            ]
        );
    }


    /**
     * Checks if this widget supports the given field
     *
     * @param FieldDefinitionInterface $field
     *
     * @return string
     */
    public function supportsField(FieldDefinitionInterface $field)
    {
        return ($field->getName() === 'choice');
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefaults(
            [
                'expanded' => false
            ]
        );
    }
}
