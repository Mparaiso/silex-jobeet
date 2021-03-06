<?php

namespace Mparaiso\JobBoard\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mparaiso\JobBoard\Entity\Base\Job as BaseJob;

/**
 * Job
 */
class Job extends BaseJob
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Mparaiso\JobBoard\Entity\Category
     */
    protected $category;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set category
     *
     * @param \Mparaiso\JobBoard\Entity\Category $category
     * @return Job
     */
    public function setCategory(\Mparaiso\JobBoard\Entity\Category $category = NULL)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Mparaiso\JobBoard\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}