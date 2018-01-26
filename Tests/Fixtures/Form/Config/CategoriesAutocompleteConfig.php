<?php

namespace wjb\AutocompleteBundle\Tests\Fixtures\Form\Config;

use Doctrine\ORM\EntityRepository;
use wjb\AutocompleteBundle\DependencyInjection\wjbAutocompleteExtension;
use wjb\AutocompleteBundle\Model\AutocompleteConfigInterface;
use wjb\AutocompleteBundle\Tests\Fixtures\Entity\Category;

/**
 * @see wjbAutocompleteExtension
 */
class CategoriesAutocompleteConfig implements AutocompleteConfigInterface
{
    public function getClassName()
    {
        return Category::class;
    }

    public function getQueryBuilder(EntityRepository $repository)
    {
        return $repository->createQueryBuilder('o');
    }
}

