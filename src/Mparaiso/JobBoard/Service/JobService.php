<?php

namespace Mparaiso\JobBoard\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Document;
use DateInterval;
use DateTime;
use Mparaiso\JobBoard\Entity\Base\Job as BaseJob;
use Doctrine\ORM\EntityManager;
use ZendSearch\Lucene\Lucene;


class JobService implements ObjectRepository
{
    /**
     * @var string
     */
    protected $em;
    protected $class;
    protected $tokenGen;
    /**
     * @var LuceneJobService
     */
    protected $luceneJobService;

    function count()
    {
        return $this->em->createQuery("SELECT count(j) FROM $this->class j ")->getSingleScalarResult();
    }

    function __construct(EntityManager $em, $class, $categoryClass)
    {
        $this->em = $em;
        $this->class = $class;
        $this->categoryClass = $categoryClass;
    }

    public function setLuceneJobService(LuceneJobService $luceneJobService)
    {
        $this->luceneJobService = $luceneJobService;
    }

    /**
     * @return LuceneJobService
     */
    public function getLuceneJobService()
    {
        return $this->luceneJobService;
    }

    function delete(BaseJob $job, $flush = TRUE)
    {
        $this->em->remove($job);
        $this->luceneJobService->deleteJobInLucenceIndex($job);
        $flush AND $this->em->flush();
        return $job;
    }

    function save(BaseJob $job, $flush = TRUE)
    {
        if ($job->getToken() === NULL) {
            $job->setToken(call_user_func($this->tokenGen, $job->getEmail()));
        }
        // update expiration date
        if ($job->getIsActivated() === NULL) {
            $job->setIsActivated(TRUE);
        }
        if ($job->getExpiresAt() === NULL) {
            $job->setExpiresAt(new DateTime("+ 30 days "));
        }
        $job->setUpdatedAt(new DateTime());
        if (NULL === $job->getCreatedAt()) {
            $job->setCreatedAt(new DateTime());
        }
        $this->em->persist($job);
        // update lucene index or delete it if the job is not activated
        if ($this->isExpired($job) || $job->getIsActivated() === FALSE) {
            $this->luceneJobService->deleteJobInLucenceIndex($job);
        } else {
            $this->luceneJobService->updateLuceneIndex($job);

        }
        $flush AND $this->em->flush();
        return $job;
    }

    function activateAndSaveJob(BaseJob $job, $flush = TRUE)
    {
        if (!$this->isExpired($job)) {
            $job->setIsActivated(TRUE);
            $this->save($job);
        }
        return $job;
    }

    function getActiveJobs()
    {
        return $this->em->createQuery("
        SELECT j FROM $this->class j
        JOIN $this->categoryClass c
        WITH j.category = c WHERE j.isActivated = true
        ORDER BY j.createdAt ASC , c.name DESC , j.company DESC
        ")->execute();
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
     * HELPER METHODS
     */


    function isExpired(BaseJob $job)
    {
        return (int)$this->getDaysBeforeExpires($job) < 0;
    }

    function expiresSoon(BaseJob $job)
    {
        return (int)$this->getDaysBeforeExpires($job) < 5;
    }

    /**
     * FR : retourne le nombre de jours avant l'expiration de l'offre d'emploi
     * FR : return the number of days before job expiration
     * @param \Mparaiso\JobBoard\Entity\Base\Job $job
     * @return int
     */
    function getDaysBeforeExpires(BaseJob $job)
    { #@note @php différence entre 2 dates
        $now = new DateTime;
        return $now->diff($job->getExpiresAt())
            ->format("%r%a");
    }

    function getLatestJob()
    {
        return $this->em->getRepository($this->class)->findOneBy(array("isActivated" => TRUE, 'isPublic' => TRUE), array("createdAt" => "DESC"));
    }

    function setTokenGen($callback)
    {
        $this->tokenGen = $callback;
    }




}