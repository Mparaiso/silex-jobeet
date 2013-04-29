<?php
namespace Mparaiso\JobBoard\Service;

use Silex\WebTestCase;
use DateTime;
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

    function testgetDaysBeforeExpires()
    {
        // a job is set to expire 30 days later
        $job = new Job;
        $job->setExpiresAt(new \DateTime('+30 days'));
        $jobService = new JobService($this->app['orm.em'],
            'Mparaiso\JobBoard\Entity\Job',
            'Mparaiso\JobBoard\Entity\Category');
        $days = $jobService->getDaysBeforeExpires($job);
        $this->assertEquals(30, $days);

        // a job is already expired
        $job1 = new Job;
        $job1->setExpiresAt(new \DateTime('-40 days'));
        $days = $jobService->getDaysBeforeExpires($job1);
        $this->assertEquals(-40, $days);
    }

    function testIsExpired()
    {
        // a job is expired
        $job =new Job;
        $job->setExpiresAt(new DateTime('-40 days'));
        $jobService = $this->app['mp.jobb.service.job'];
        $this->assertTrue($jobService->isExpired($job));

        // a job is not expired
        $job1 = new Job;
        $job1->setExpiresAt(new DateTime('+3 days'));
        $this->assertFalse($jobService->isExpired($job1));
    }

    function testExpiresSoon(){
        // a job expires soon
        $job =new Job;
        $job->setExpiresAt(new DateTime('+3 days'));
        $jobService = $this->app['mp.jobb.service.job'];
        $this->assertTrue($jobService->expiresSoon($job));

        // a job doesnt expires soon
        $job1 = new Job;
        $job1->setExpiresAt(new \DateTime('+6 days'));
        $this->assertFalse($jobService->expiresSoon($job1));
    }

}