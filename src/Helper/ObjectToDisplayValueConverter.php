<?php

namespace wjb\AutocompleteBundle\Helper;

use Symfony\Component\PropertyAccess\PropertyAccess;

class ObjectToDisplayValueConverter
{

    public static function convertObject($entity, $display)
    {
        if (null === $display) {
            return (string)$entity;
        }

        if (is_callable($display)) {
            return call_user_func($display, $entity);
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($entity, $display);
    }
}

