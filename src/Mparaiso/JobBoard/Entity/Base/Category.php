<?php

namespace Mparaiso\JobBoard\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Category
 */
class Category
{
    /**
     * @var string
     */
    protected $name;

    function __toString()
    {
        return $this->name;
    }

    function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata
            ->addPropertyConstraint("name", new NotNull())
            ->addPropertyConstraint("name", new Length(array(
            'min' => 4, "max" => 255)));
    }


    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}