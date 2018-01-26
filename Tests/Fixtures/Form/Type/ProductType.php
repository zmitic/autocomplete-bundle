<?php

namespace wjb\AutocompleteBundle\Tests\Fixtures\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use wjb\AutocompleteBundle\Form\Type\AutocompleteType;
use wjb\AutocompleteBundle\Tests\Fixtures\Entity\Category;
use wjb\AutocompleteBundle\Tests\Fixtures\Entity\Product;

class ProductType extends AbstractType
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');

        $builder->add('category', AutocompleteType::class, [
            'class' => Category::class,
            'search' => function ($search) {
                return $this->entityManager->getRepository(Category::class)->createQueryBuilder('o')
                    ->andWhere('o.name LIKE :search')->setParameter('search', $search.'%')
                    ->getQuery()->getResult();
            },
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

