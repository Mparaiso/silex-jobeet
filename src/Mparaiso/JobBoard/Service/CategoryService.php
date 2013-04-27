<?php
namespace Mparaiso\JobBoard\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Mparaiso\JobBoard\Entity\Base\Category as BaseCategory;
use DateTime;
use Mparaiso\JobBoard\Entity\Base\Job as BaseJob;
use Doctrine\ORM\EntityManager;

class  CategoryService implements ObjectRepository
{
    protected $em;
    protected $class;

    function __construct(EntityManager $em, $class, $jobClass)
    {
        $this->em = $em;
        $this->class = $class;
        $this->jobClass = $jobClass;
    }

    function save(BaseCategory $category, $flush = TRUE)
    {

        $this->em->persist($category);
        $flush AND $this->em->flush();
        return $category;
    }


    function find($id)
    {
        return $this->em->find($this->class, $id);
    }


    function findAll(array $criteria = array(), array $orderBy = array(), $limit = NULL, $offset = NULL)
    {
        return $this->em->getRepository($this->class)->findBy($criteria, $orderBy, $limit, $offset);
    }


    function findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        return $this->em->getRepository($this->class)->findBy($criteria, $orderBy, $limit, $offset);
    }


    function findOneBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findOneBy($criteria);
    }


    function getClassName()
    {
        return $this->class;
    }

    /**
     * Get categories that have a job associated with them
     * @return mixed
     */
    function getWithJob()
    {
        # @note @doctrine JOIN
        $q = $this->em->createQuery(
            " select c from $this->class c JOIN $this->jobClass j
          where c = j.category AND j IS NOT NULL "
        );
        return $q->execute();
    }

    function getOneWithActiveJob($id)
    {
        $q = $this->em->createQuery(
            "select c from $this->class c
            JOIN $this->jobClass j
            where c = j.category AND j is NOT NULL AND c.id = :id
            AND j.isActivated = true "
        );
        return $q->setParameters(array("id" => $id))->getSingleResult();

    }

    function getWithActiveJob()
    {
        # @note @doctrine JOIN
        $q = $this->em->createQuery(
            " select c from $this->class c JOIN c.jobs j
          where c = j.category AND j  IS NOT NULL AND j.isActivated = TRUE
         "
        );
        return new Paginator($q);

    }

    function countActiveJobsByCategory($category)
    {
        return $this->em
            ->createQuery("
            SELECT count(j) FROM
            $this->jobClass j
            WHERE j.category = :category
            AND j.isActivated = true
            ")
            ->setParameters(array('category' => $category))
            ->getSingleScalarResult();
    }
}