<?php

namespace Gravity\CmsBundle\Display\Handler;

use Gravity\CmsBundle\Entity\FieldableEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FieldHandler
 *
 * @author Andy Thorne <contrabandvr@gmail.com>
 */
class FieldHandler implements DisplayHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'field';
    }

    /**
     * @inheritDoc
     */
    public function setOptions(OptionsResolver $optionsResolver, array $options = [])
    {
        $optionsResolver->setDefaults(
            [
                'fields'   => [
                    'type'         => null,
                    'label'        => true,
                    'label_inline' => false,
                ],
                'template' => 'GravityCmsBundle:Node:view.html.twig',
            ]
        )
            ->setAllowedTypes('fields', 'array');
    }

    /**
     * @inheritDoc
     */
    public function supportsRequest(Request $request)
    {
        return $request->attributes->get('_format') === 'html';
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(array $options = [])
    {
        return $options['template'];
    }

    /**
     * @inheritDoc
     */
    public function getTemplateOptions(FieldableEntity $fieldableEntity, array $options = [])
    {
        return [
            'node'             => $fieldableEntity,
            'display_mappings' => $options['fields'],
        ];
    }
}
