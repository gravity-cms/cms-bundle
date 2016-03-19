<?php


namespace Gravity\CmsBundle\Display;

use Gravity\CmsBundle\Display\Handler\DisplayHandlerInterface;
use Gravity\CmsBundle\Display\Type\DisplayDefinitionInterface;
use Gravity\CmsBundle\Entity\FieldableEntity;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DisplayManager
 *
 * @author Andy Thorne <contrabandvr@gmail.com>
 */
class DisplayManager
{
    /**
     * @var DisplayHandlerInterface[]
     */
    protected $handlers = [];

    /**
     * @var DisplayDefinitionInterface[]
     */
    protected $definitions = [];

    /**
     * @var array
     */
    protected $config;

    /**
     * DisplayManager constructor.
     *
     * @param DisplayHandlerInterface[] $handlers
     * @param DisplayDefinitionInterface[] $definitions
     * @param array $config
     */
    public function __construct(array $handlers, array $definitions, array $config)
    {
        foreach ($handlers as $handler) {
            $this->handlers[$handler->getName()] = $handler;
        }

        foreach ($definitions as $definition) {
            $this->definitions[$definition->getName()] = $definition;
        }

        $this->config = $config;
    }

    /**
     * Get the display config for the entity class
     *
     * @param string $entityClass
     *
     * @return array
     */
    public function getEntityConfig($entityClass)
    {
        $config = $this->config[$entityClass];
        $optionsResolver = new OptionsResolver();
        $handler = $this->handlers[$config['handler']];
        $handler->setOptions($optionsResolver, $config['options']);

        $config['options'] = $optionsResolver->resolve($config['options']);

        return $config;
    }

    /**
     * @param DisplayHandlerInterface $handler
     * @param FieldableEntity $fieldableEntity
     *
     * @deprecated use DisplayManager::getEntityConfig
     *
     * @return array
     */
    public function getHandlerOptions(DisplayHandlerInterface $handler, FieldableEntity $fieldableEntity)
    {
        $config = $this->getEntityConfig(get_class($fieldableEntity));
        $optionsResolver = new OptionsResolver();
        $handler->setOptions($optionsResolver, $config['options']);

        return $optionsResolver->resolve($config['options']);
    }

    /**
     * @return DisplayHandlerInterface
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * @param string $name
     *
     * @return DisplayHandlerInterface
     */
    public function getHandler($name)
    {
        return $this->handlers[$name];
    }

    /**
     * @param FieldableEntity $entity
     *
     * @return DisplayHandlerInterface
     */
    public function getHandlerForEntity(FieldableEntity $entity)
    {
        $config = $this->getEntityConfig(get_class($entity));

        return $this->handlers[$config['handler']];
    }


    /**
     * @return DisplayDefinitionInterface[]
     */
    public function getFieldDisplayDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @param string $name
     *
     * @return DisplayDefinitionInterface
     */
    public function getDisplayDefinition($name)
    {
        return $this->definitions[$name];
    }
}
