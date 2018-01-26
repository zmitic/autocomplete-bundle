<?php

namespace wjb\AutocompleteBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="tbl_category")
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="wjb\AutocompleteBundle\Tests\Fixtures\Entity\Product", mappedBy="category")
     */
    private $products;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return (string)$this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getProducts()
    {
        return $this->products;
    }
}

