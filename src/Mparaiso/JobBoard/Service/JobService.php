<?php
namespace Mparaiso\JobBoard\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Mparaiso\JobBoard\Entity\Base\Job as BaseJob;
use Doctrine\ORM\EntityManager;

class  JobService implements ObjectRepository
{
    protected $em;
    protected $class;

    function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }

    function save(BaseJob $job, $flush = TRUE)
    {
        // update expiration date
        $job->setExpiresAt(new \DateTime("+ 30 days "));
        $this->em->persist($job);
        $flush AND $this->em->flush();
        return $job;
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
}