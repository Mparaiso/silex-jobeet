<?php

namespace Mparaiso\JobBoard\Controller;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

class JobController
{
    protected $jobService;

    function __construct($jobService)
    {
        $this->jobService = $jobService;
    }

    function index(Application $app, Request $req,$format)
    {
        $limit = 10;
        $offset = 0;
//        $jobs = $this->jobService->findBy(array("isActivated" => TRUE), array("createdAt" => "ASC"), $limit, $limit * $offset);
        $jobs = $this->jobService->getActiveJobs();
        return $app['twig']->render("mp.jobb.job.index.$format.twig", array(
            "jobs" => $jobs,
        ));

    }

    function read(Application $app, Request $req, $company, $location, $id, $position)
    {
        $job = $this->jobService->find($id);
        //$job === NULL AND $app->abort(404, "Job not found !");
        return $app['twig']->render("mp.jobb.job.read.html.twig", array(
            "job" => $job,
        ));
    }
}