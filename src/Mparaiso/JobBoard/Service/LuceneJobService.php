<?php

namespace Mparaiso\JobBoard\Service;

use ZendSearch\Lucene\Lucene;
use Mparaiso\JobBoard\Entity\Base\Job as BaseJob;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Document;

/**
 * Created by JetBrains PhpStorm.
 * User: mark prades
 * Date: 01/05/13
 * Time: 13:40
 * To change this template use File | Settings | File Templates.
 */
class LuceneJobService
{
    /**
     * LUCENE SEARCH
     */

    /**
     * @var string
     */
    public $luceneIndexDir;

    function __construct($luceneIndexDir)
    {
        $this->luceneIndexDir = $luceneIndexDir;
    }

    /** get lucene index */
    function getLuceneIndex()
    {
        if (file_exists($this->luceneIndexDir)) {
            return Lucene::open($this->luceneIndexDir);
        }
        return Lucene::create($this->luceneIndexDir);
    }

    function getLuceneIndexDir()
    {
        return $this->luceneIndexDir;
    }

    /**
     * set lucene index file path
     * @param string $luceneIndexDir
     */
    public function setLuceneIndexDir($luceneIndexDir)
    {
        $this->luceneIndexDir = $luceneIndexDir;
    }

    /** update lucene index file */
    public function updateLuceneIndex(BaseJob $job)
    {
        $index = $this->getLuceneIndex();
        // remove existing entries
        foreach ($index->find('id:' . $job->getId()) as $hit) {
            $index->delete($hit->id);
        }
        //dont index expired and non active jobs (moved to job service
//        if ($this->isExpired($job) || $job->getIsActivated() === FALSE) {
//            return;
//        }
        $doc = new Document();
        // store job primary key to identify it in the search results
        $doc->addField(Field::keyword('id', $job->getId()));
        // index job fields
        $doc->addField(Field::unStored('position', $job->getPosition()));
        $doc->addField(Field::unStored('company', $job->getCompany()));
        $doc->addField(Field::unStored('location', $job->getLocation()));
        $doc->addField(Field::unStored('description', $job->getDescription()));

        // add job to the index
        $index->addDocument($doc);
        $index->commit();
    }

    /** delete a job in the lucene index */
    public function deleteJobInLucenceIndex(BaseJob $job)
    {
        $index = $this->getLuceneIndex();
        foreach ($index->find('id:' . $job->getId()) as $hit) {
            $index->delete($hit->id);
        }
    }

    public function deleteAllJobsInLuceneIndex()
    {
        if (file_exists($this->getLuceneIndexDir())) {

        }
    }


}
