<?php

namespace wjb\AutocompleteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use wjb\AutocompleteBundle\Helper\ObjectToDisplayValueConverter;

class AutocompleteController extends AbstractController
{
    public function suggestions(Request $request)
    {
        $formField = base64_decode($request->get('ff'));
        $formClass = base64_decode($request->get('fc'));
        $search = $request->get('search');

        if (!$search) {
            return new JsonResponse([]);
        }

        $form = $this->createForm($formClass);
        $searchCallable = $form->get($formField)->getConfig()->getOption('search');
        $displayConfig = $form->get($formField)->getConfig()->getOption('display');

        $results = $searchCallable($search);
        $map = array_map(function ($entity) use ($displayConfig) {
            return [
                'id' => (string)$entity->getId(),
                'value' => ObjectToDisplayValueConverter::convertObject($entity, $displayConfig),
            ];
        }, $results);

        return new JsonResponse($map);
    }
}

