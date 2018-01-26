<?php

namespace wjb\AutocompleteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AutocompleteController extends AbstractController
{
    public function search(Request $request)
    {
//        sleep(1);
        $formClass = $request->get('form_class');
        $formField = $request->get('form_field');
        $search = $request->get('search');

        if (!$search) {
            return new JsonResponse([]);
        }

        $form = $this->createForm($formClass);
        $callback = $form->get($formField)->getConfig()->getOption('search');

        $results = $callback($search);
        $map = array_map(function ($result) {
            return [
                'id' => (string)$result->getId(),
                'value' => (string)$result,
            ];
        }, $results);

        return new JsonResponse($map);
    }
}

