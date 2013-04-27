<?php
namespace Mparaiso\JobBoard\Service;

use Silex\WebTestCase;
use Mparaiso\JobBoard\Entity\Job;

class JobServiceTest extends WebTestCase
{
    /**
     * Creates the application.
     *
     * @return HttpKernel
     */
    public function createApplication()
    {
        return \Bootstrap::getApp();
    }

    function testSave()
    {
        $l = \Bootstrap::getFixtureLoader();
        $f = $l->parse();
        $this->assertTrue(is_array($f));
        $this->assertTrue(isset($f['job_sensio_labs']));
        $s = new JobService($this->app['orm.em'],
            'Mparaiso\JobBoard\Entity\Job',
            'Mparaiso\JobBoard\Entity\Category');
        $s->save($f['job_sensio_labs']);
        $this->assertTrue(is_int($f['job_sensio_labs']->getId()));
        $this->assertTrue($f['job_sensio_labs']->getId() != NULL);
    }
}