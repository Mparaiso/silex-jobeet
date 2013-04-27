<?php

namespace Mparaiso\JobBoard\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\GenericEvent;
use Mparaiso\JobBoard\Entity\Base\Job;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Form;
use Silex\Application;

class JobController
{
    protected $jobService;

    function __construct($jobService)
    {
        $this->jobService = $jobService;
    }

    function index(Application $app, Request $req, $format)
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

    function create(Application $app, Request $req, $format)
    {
        $job = new $app['mp.jobb.entity.job'];
        $type = new  $app['mp.jobb.form.job'];
        $form = $app['form.factory']->create($type, $job);
        /* @var $form Form */
        if ("POST" === $req->getMethod()) {
            $form->bind($req);
            if ($form->isValid()) {
                // upload logo
                if ($form->get('logo_file')->getData() != NULL) {
                    $logoFile = $form->get('logo_file')->getData();
                    /* @var $logoFile UploadedFile */
                    $logoFileName = $logoFile->getClientOriginalName() . uniqid("_");
                    $logoFile->move(
                        $app['mp.jobb.params.upload_dir'],
                        $logoFileName
                    );
                    $job->setLogo($logoFileName);
                }
                $app['dispatcher']->dispatch('job_before_create', new GenericEvent($job));
                $app['mp.jobb.service.job']->save($job);
                $app['dispatcher']->dispatch('job_after_create', new GenericEvent($job));
            }
        }
        return $app['twig']->render("mp.jobb.job.create.$format.twig", array(
            "form" => $form->createView()
        ));
    }


}