<?php

namespace Mparaiso\JobBoard\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mparaiso\JobBoard\Entity\Base\Affiliate as BaseAffiliate;

/**
 * Affiliate
 */
class Affiliate extends BaseAffiliate
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $categories;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add categories
     *
     * @param \Mparaiso\JobBoard\Entity\Category $categories
     * @return Affiliate
     */
    public function addCategory(\Mparaiso\JobBoard\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Mparaiso\JobBoard\Entity\Category $categories
     */
    public function removeCategory(\Mparaiso\JobBoard\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }
}