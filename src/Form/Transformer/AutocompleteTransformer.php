<?php

namespace wjb\AutocompleteBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class AutocompleteTransformer implements DataTransformerInterface
{
    private $options;

    public function __construct(array $options)
    {
        $this->options = $options;
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
            'value' => (string)$entity,
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
            $entity = $findByIdCallback($id);
            if ($entity) {
                return $entity;
            }
        }

        if ($value) {
            $findByValueCallback = $this->options['find_one_by_value'];
            $entity = $findByValueCallback($value);
            if ($entity) {
                return $entity;
            }
        }

        $notFoundCallback = $this->options['not_found'];

        return $notFoundCallback($value);
    }
}

