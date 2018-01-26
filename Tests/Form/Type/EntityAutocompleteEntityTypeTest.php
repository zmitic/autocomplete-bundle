<?php

namespace wjb\AutocompleteBundle\Tests\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use wjb\AutocompleteBundle\Tests\Fixtures\Entity\Category;
use wjb\AutocompleteBundle\Tests\Fixtures\Entity\Product;

class EntityAutocompleteEntityTypeTest extends WebTestCase
{
    /** @var ContainerInterface */
    private $container;

    /** @var FormFactory */
    private $formFactory;

    private $category;

    private $product;

    /** @var EntityManagerInterface */
    private $em;

    public function testSomething()
    {
        $client = $this->container->get('test.client');
        $response = $client->request('GET', '/test/1')->html();
    }

    protected function setUp()
    {
        require_once __DIR__ . '/../../app/TestKernel.php';

        $kernel = self::bootKernel();

        $this->container = $kernel->getContainer();
        $path = $kernel->getRootDir() . '/../../Resources/views';
        $this->container->get('twig')->getLoader()->addPath($path, 'wjbAutocompleteBundle');

        $this->formFactory = $this->container->get('form.factory');
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->container->get('cache_warmer')->warmUp($kernel->getCacheDir());
        $this->buildDB();

        $category = new Category('Test category');
        $product = new Product($category, 'Test product');

        $this->category = $category;
        $this->product = $product;

        $this->em->persist($category);
        $this->em->persist($product);
        $this->em->flush();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->em = null;
    }

    private function buildDB()
    {
        $this->em->getConnection()->getConfiguration()->setSQLLogger();
        $schemaTool = new SchemaTool($this->em);
        $classes = [
            $this->em->getClassMetadata(Category::class),
            $this->em->getClassMetadata(Product::class),
        ];

        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);
    }
}

