<?php

namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AddTaggedCompilerPass implements CompilerPassInterface
{
    protected $parentServiceId;
    protected $tagName;
    protected $methodName;
    protected $attributes;

    public function __construct($parentServiceId, $tagName, $methodName, array $attributes = array())
    {
        $this->parentServiceId = $parentServiceId;
        $this->tagName = $tagName;
        $this->methodName = $methodName;
        $this->attributes = $attributes;
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->parentServiceId)) {
            return;
        }

        $definition = $container->getDefinition($this->parentServiceId);
        $services = $container->findTaggedServiceIds($this->tagName);
        foreach ($services as $id => $tagAttributes) {
            $parameters = array($this->resolveServiceId($id));
            foreach ($this->attributes as $name) {
                $parameters[] = $this->getAttribute($tagAttributes, $name, $id);
            }
            $definition->addMethodCall($this->methodName, $parameters);
        }
    }

    protected function getAttribute($attributesList, $name, $id)
    {
        foreach ($attributesList as $attributes) {
            if (isset($attributes[$name])) {
                return $attributes[$name];
            }
        }
        throw new \RuntimeException(
            sprintf('Missing attribute %s on tag %s in %s definition', $name, $this->tagName, $id)
        );
    }

    protected function resolveServiceId($id)
    {
        return new Reference($id);
    }
}