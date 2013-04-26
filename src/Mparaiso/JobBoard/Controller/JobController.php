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

    function index(Application $app, Request $req)
    {
        $limit = 10;
        $offset = 0;
        $jobs = $this->jobService->findBy(array("isActivated"=>true), array("createdAt" => "DESC"), $limit, $limit * $offset);
        return $app['twig']->render("mp.jobb.job.index.html.twig", array(
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