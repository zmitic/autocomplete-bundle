<?php

namespace wjb\AutocompleteBundle\Service;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ObjectTransformer
{
    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function convertToValue($entity, $display)
    {
        if (null === $display) {
            return (string)$entity;
        }

        if (is_callable($display)) {
            return $display($entity);
        }

        return $this->propertyAccessor->getValue($entity, $display);
    }
}

