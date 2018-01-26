<?php

namespace wjb\AutocompleteBundle\Tests\Fixtures\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use wjb\AutocompleteBundle\Tests\Fixtures\Entity\Product;
use wjb\AutocompleteBundle\Tests\Fixtures\Form\Type\ProductType;

class TestController extends Controller
{
    public function testAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $product = $em->getRepository(Product::class)->find($id);

        $form = $this->createForm(ProductType::class, $product);
        return $this->render('@wjbAutocomplete/test_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

