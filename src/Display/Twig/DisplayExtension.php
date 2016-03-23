<?php


namespace Gravity\CmsBundle\Display\Twig;

use Gravity\CmsBundle\Display\DisplayManager;
use Gravity\CmsBundle\Entity\FieldableEntity;
use Gravity\CmsBundle\Field\FieldManager;

/**
 * Class DisplayExtension
 *
 * @author Andy Thorne <contrabandvr@gmail.com>
 */
class DisplayExtension extends \Twig_Extension
{
    /**
     * @var DisplayManager
     */
    protected $displayManager;

    /**
     * @var FieldManager
     */
    protected $fieldManager;

    /**
     * @var array
     */
    protected $javascripts = [];

    /**
     * @var array
     */
    protected $stylesheets = [];

    /**
     * FieldExtension constructor.
     *
     * @param DisplayManager $displayManager
     * @param FieldManager $fieldManager
     */
    public function __construct(DisplayManager $displayManager, FieldManager $fieldManager)
    {
        $this->displayManager = $displayManager;
        $this->fieldManager = $fieldManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'gravity_cms_display';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'render_field_display', [$this, 'renderFieldDisplay'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new \Twig_SimpleFunction('display_field_javascripts', [$this, 'getDisplayJavascripts']),
            new \Twig_SimpleFunction('display_field_stylesheets', [$this, 'getDisplayStylesheets']),
        ];
    }

    /**
     * @param \Twig_Environment $environment
     * @param FieldableEntity $entity
     * @param string $field
     *
     * @return string
     * @internal param \Twig_Environment $environment
     */
    public function renderFieldDisplay(\Twig_Environment $environment, FieldableEntity $entity, $field)
    {
        $class = get_class($entity);
        $displayMapping = $this->displayManager->getEntityConfig($class);
        $fieldSettings = $this->fieldManager->getEntityFieldMapping($class);

        $html = '';

        if ($displayMapping['options']['fields'][$field]) {
            $displayFieldSettings = $displayMapping['options']['fields'][$field] + [
                    'type'         => null,
                    'label'        => true,
                    'label_inline' => false,
                    'options'      => [],
                ];
            $display = $this->displayManager->getDisplayDefinition($displayFieldSettings['type']);
            $fieldEntity = call_user_func([$entity, 'get'.$field]);
            $fieldDisplayOptions = $displayFieldSettings['options'] ?: [];

            $templateOptions = [
                'entity'         => $fieldEntity,
                'field_settings' => $fieldSettings,
                'field_name'     => $field,
                'label'          => is_string(
                    $displayFieldSettings['label']
                ) ?: ($displayFieldSettings['label'] ? $field : false),
                'label_inline'   => $displayFieldSettings['label_inline'],
            ];

            if ($fieldSettings[$field]['options']['limit'] > 1) {
                $subHtml = '';

                $subTemplateOptions = $templateOptions;
                $subTemplateOptions['label'] = false;
                foreach ($fieldEntity as $fieldEntityItem) {
                    $subHtml .= $environment->render(
                        $display->getTemplate(),
                        $subTemplateOptions +
                        $display->getTemplateOptions($fieldEntityItem, $fieldDisplayOptions)
                    );
                }

                $templateOptions['rows'] = $subHtml;
                $html = $environment->render(
                    $display->getListTemplate(),
                    $templateOptions +
                    $display->getListTemplateOptions($fieldEntity, $fieldDisplayOptions)
                );
            } else {
                $html = $environment->render(
                    $display->getTemplate(),
                    $templateOptions + $display->getTemplateOptions($fieldEntity, $fieldDisplayOptions)
                );
            }
        }

        return $html;
    }

    public function getDisplayJavascripts(FieldableEntity $entity)
    {
        $class = get_class($entity);
        $displayMapping = $this->displayManager->getEntityConfig($class);

        $javascripts = [];
        foreach ($displayMapping['options']['fields'] as $fieldMapping) {
            if(isset($fieldMapping['type'])) {
                $display = $this->displayManager->getDisplayDefinition($fieldMapping['type']);
                foreach ($display->getAssetLibraries() as $library) {
                    $javascripts = array_merge($javascripts, $library->getJavascripts());
                }
            }
        }

        return $javascripts;
    }

    public function getDisplayStyleSheets(FieldableEntity $entity)
    {
        $class = get_class($entity);
        $displayMapping = $this->displayManager->getEntityConfig($class);

        $stylesheets = [];
        foreach ($displayMapping['options']['fields'] as $field => $fieldMapping) {
            if(isset($fieldMapping['type'])) {
                $display = $this->displayManager->getDisplayDefinition($fieldMapping['type']);
                foreach ($display->getAssetLibraries() as $library) {
                    $stylesheets = array_merge($stylesheets, $library->getStylesheets());
                }
            }
        }

        return $stylesheets;
    }
}
