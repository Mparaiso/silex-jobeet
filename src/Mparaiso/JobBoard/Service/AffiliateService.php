<?php

namespace Mparaiso\JobBoard\Service;

use Doctrine\ORM\EntityManager;
use Mparaiso\JobBoard\Entity\Category;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \DateTime;
use Mparaiso\JobBoard\Entity\Base\Affiliate as BaseAffiliate;
use Mparaiso\JobBoard\Entity\Affiliate;

class AffiliateService
{
    protected $em;
    protected $class;
    protected $jobClass;
    protected $categoryClass;

    protected $tokenStrategy;

    function __construct(EntityManager $em, $class, $jobClass, $categoryClass)
    {
        $this->em = $em;
        $this->class = $class;
        $this->jobClass = $jobClass;
        $this->categoryClass = $categoryClass;
    }


    function save(BaseAffiliate $affiliate, $flush = TRUE)
    {
        if ($affiliate->getIsActive() === NULL)
            $affiliate->setIsActive(FALSE);
        if ($affiliate->getCreatedAt() === NULL) {
            $affiliate->setCreatedAt(new DateTime);
        }
        if ($affiliate->getToken() === NULL) {
            $token = call_user_func($this->tokenStrategy, $affiliate->getEmail());
            $affiliate->setToken($token);
        }
        $this->em->persist($affiliate);
        $flush AND $this->em->flush();
        return $affiliate;
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

    function count()
    {
        return $this->em->createQuery("SELECT COUNT(c) FROM " . $this->class . " c ")->getSingleScalarResult();
    }

    /**
     * given a affiliate token , return active jobs in the category followed by the affiliate
     * @param $token
     * @return object
     */
    function getForToken($token)
    {
//        $affiliate = $this->em->getRepository($this->class)->findOneBy(array('token' => $token, 'isActive' => TRUE));
//        /* @var $affiliate Affiliate */
//        if ($affiliate === NULL) {
//            throw new NotFoundHttpException("Affiliate with token \"$token\" does not exist or is not activated.");
//        }
        return $this->em
            ->createQuery("
             SELECT j FROM $this->jobClass j
             JOIN j.category c
             JOIN $this->class a
            WHERE j.isActivated = true AND a.token = :token AND a.isActive = true AND c MEMBER a.categories ")
            ->execute(array("token" => $token));

    }

    public function getTokenStrategy()
    {
        return $this->tokenStrategy;
    }

    public function setTokenStrategy($tokenStrategy)
    {
        $this->tokenStrategy = $tokenStrategy;
    }
}
