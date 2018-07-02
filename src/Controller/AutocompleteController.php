<?php

namespace wjb\AutocompleteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use wjb\AutocompleteBundle\Service\ObjectTransformer;

class AutocompleteController extends AbstractController
{
    /** @var ObjectTransformer */
    private $objectTransformer;

    public function __construct(ObjectTransformer $objectTransformer)
    {
        $this->objectTransformer = $objectTransformer;
    }

    /**
     * @Route("/wjb/search", name="wjb_autocomplete_suggestions")
     */
    public function suggestionsAction(Request $request)
    {
        $formField = base64_decode($request->get('ff'));
        $formClass = base64_decode($request->get('fc'));
        $search = $request->get('search');

        if (!$search) {
            return new JsonResponse([]);
        }

//        $objectTransformer = $this->get('wjb_autocomplete.object_transformer');
        $objectTransformer = $this->objectTransformer;
        $form = $this->createForm($formClass);
        $searchCallable = $form->get($formField)->getConfig()->getOption('search');
        $displayConfig = $form->get($formField)->getConfig()->getOption('display');

        $results = $searchCallable($search);
        $map = array_map(function ($entity) use ($displayConfig, $objectTransformer) {
            return [
                'id' => (string)$entity->getId(),
                'value' => $objectTransformer->convertToValue($entity, $displayConfig),
            ];
        }, $results);

        return new JsonResponse($map);
    }
}

