<?php

namespace wjb\AutocompleteBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use wjb\AutocompleteBundle\Service\ObjectTransformer;

class AutocompleteTransformer implements DataTransformerInterface
{
    private $options;

    /** @var ObjectTransformer */
    private $objectTransformer;

    public function __construct(array $options, ObjectTransformer $objectTransformer)
    {
        $this->options = $options;
        $this->objectTransformer = $objectTransformer;
    }

    public function transform($entity)
    {
        if (!$entity) {
            return [
                'id' => '',
                'value' => '',
            ];
        }

        return [
            'id' => $entity->getId(),
            'value' => $this->objectTransformer->convertToValue($entity, $this->options['display']),
        ];
    }

    public function reverseTransform($data)
    {
        $id = $data['id'];
        $value = $data['value'];

        if (!$id && !$value) {
            return null;
        }

        // try finding entity by id first
        if ($id) {
            $findByIdCallback = $this->options['find_one_by_id'];
            $entity = call_user_func($findByIdCallback, $id);
            if ($entity) {
                return $entity;
            }
        }

        if ($value) {
            $findByValueCallback = $this->options['find_one_by_value'];
            $entity = call_user_func($findByValueCallback, $value);
            if ($entity) {
                return $entity;
            }
        }

        $notFoundCallback = $this->options['not_found'];

        return call_user_func($notFoundCallback, $value);
    }
}

