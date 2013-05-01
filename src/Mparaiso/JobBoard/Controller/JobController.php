<?php

namespace Mparaiso\JobBoard\Controller;

use Symfony\Component\HttpFoundation\Request;
use Mparaiso\JobBoard\Event\JobEvents;
use Symfony\Component\EventDispatcher\GenericEvent;
use Mparaiso\JobBoard\Entity\Base\Job;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Form;
use Silex\Application;

/**
 * FR : gère les jobs
 * EN : front manage jobs
 * @author M.Paraiso <mparaiso@online.fr>
 */
class JobController
{

    protected $jobService;

    function __construct($jobService)
    {
        $this->jobService = $jobService;
    }

    /**
     * @param \Silex\Application $app
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @param type $format
     * @return type
     */
    function index(Application $app, Request $req, $_format)
    {
        $limit = 10;
        $offset = 0;
        $jobs = $this->jobService->findBy(array("isActivated" => TRUE), array("updatedAt" => "DESC"), $limit, $limit * $offset);
       // $jobs = $this->jobService->getActiveJobs();
        return $app['twig']->render("mp.jobb.job.index.$_format.twig", array(
            "jobs" => $jobs,
        ));
    }

    function read(Application $app, Request $req, $company,
                  $location, $id, $position, $format)
    {
        $job = $this->jobService->find($id);
        $job === NULL AND $app->abort(404, "resource not found !");
        $history = $app['session']->get('job_history', array());
        if (!in_array(serialize($job), array_map('serialize',$history))) {
            array_unshift($history, $job);
            array_splice($history,3);
        }
        $app['session']->set('job_history', $history);
        return $app['twig']->render("mp.jobb.job.read.$format.twig", array(
            "job" => $job,
        ));
    }

    function readByToken(Application $app, Request $req, $token, $format)
    {
        $job = $this->jobService->findOneBy(array('token' => $token));
        $job === NULL AND $app->abort(404, "Job not found !");
        return $app['twig']->render("mp.jobb.job.read.$format.twig", array(
            "job"   => $job,
            "token" => $token
        ));
    }

    function create(Application $app, Request $req, $format)
    {
        $job = new $app['mp.jobb.entity.job'];
        $type = new $app['mp.jobb.form.job'];
        $form = $app['form.factory']->create($type, $job)
            ->remove('logo')
            ->remove('token');
        /* @var $form Form */
        if ("POST" === $req->getMethod()) {
            $form->bind($req);
            if ($form->isValid()) {
                $app['dispatcher']->dispatch(JobEvents::BEFORE_CREATE, new GenericEvent($job, array(
                    'form' => $form, 'app' => $app, 'request' => $req)));
                $this->jobService->save($job);
                $app['dispatcher']->dispatch(JobEvents::AFTER_CREATE, new GenericEvent($job, array(
                    'form' => $form, 'app' => $app, 'request' => $req)));
                $app['session']->getFlashBag()->set("success", "Job successfully posted !");
                return $app->redirect(
                    $app['url_generator']->generate('job_detail', array(
                        "id"       => $job->getId(), "company" => $job->getCompany(),
                        "location" => $job->getLocation(), "position" => $job->getPosition())));
            }
        }
        return $app['twig']->render("mp.jobb.job.create.$format.twig", array(
            "form" => $form->createView()
        ));
    }

    function edit(Application $app, Request $req, $id, $format)
    {
        $job = $this->jobService->find($id);
        if ($job === NULL)
            $app->abort(404, "resource not found");
        $type = new $app['mp.jobb.form.job'];
        $form = $app['form.factory']->create($type, $job)->remove('logo')->remove('token');
        /* @var $form Form */
        if ("POST" === $req->getMethod()) {
            $form->bind($req);
            if ($form->isValid()) {

                $app['dispatcher']->dispatch(JobEvents::BEFORE_UPDATE, new GenericEvent($job, array(
                    'form' => $form, 'app' => $app, 'request' => $req)));
                $this->jobService->save($job);
                $app['dispatcher']->dispatch(JobEvents::AFTER_UPDATE, new GenericEvent($job, array(
                    'form' => $form, 'app' => $app, 'request' => $req)));
                $app['session']->getFlashBag()->set("success", "Job successfully updated !");
                return $app->redirect(
                    $app['url_generator']->generate('job_detail', array(
                        "id"       => $job->getId(), "company" => $job->getCompany(),
                        "location" => $job->getLocation(), "position" => $job->getPosition())));
            }
        }
        return $app['twig']->render("mp.jobb.job.edit.$format.twig", array(
            "form" => $form->createView()
        ));
    }

    function editByToken(Application $app, Request $req, $token, $format)
    {
        $job = $this->jobService->findOneBy(array('token' => $token));
        if ($job === NULL)
            $app->abort(404, "resource not found");
        $type = new $app['mp.jobb.form.job'];
        $form = $app['form.factory']->create($type, $job)->remove('logo')->remove('token');
        /* @var $form Form */
        if ("POST" === $req->getMethod()) {
            $form->bind($req);
            if ($form->isValid()) {

                $app['dispatcher']->dispatch(JobEvents::BEFORE_UPDATE, new GenericEvent($job, array(
                    'form' => $form, 'app' => $app, 'request' => $req)));
                $this->jobService->save($job);
                $app['dispatcher']->dispatch(JobEvents::AFTER_UPDATE, new GenericEvent($job, array(
                    'form' => $form, 'app' => $app, 'request' => $req)));
                $app['session']->getFlashBag()->set("success", "Job successfully updated !");
                return $app->redirect(
                    $app['url_generator']->generate('job_detail', array(
                        "id"       => $job->getId(), "company" => $job->getCompany(),
                        "location" => $job->getLocation(), "position" => $job->getPosition())));
            }
        }
        return $app['twig']->render("mp.jobb.job.edit.$format.twig", array(
            "form" => $form->createView()
        ));
    }

    /**
     * EN : delete a job by id
     * FR : efface un job selon l'id
     * @param \Silex\Application $app
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @param type $id
     * @param type $format
     * @return type
     */
    function delete(Application $app, Request $req, $id, $format)
    {
        $job = $this->jobService->find($id);
        if ($job === NULL)
            $app->abort(404, 'resource not found');
        if ("POST" === $req->getMethod()) {
            $app['dispatcher']->dispatch(JobEvents::BEFORE_DELETE, new GenericEvent($job, array('request' => $req)));
            $this->jobService->delete($job);
            $app['dispatcher']->dispatch(JobEvents::AFTER_DELETE, new GenericEvent($job, array('request' => $req)));
            return $app->redirect($app['url_generator']->generate('job_principe'));
        }
        return $app['twig']->render("mp.jobb.job.delete.$format.twig", array(
            "job" => $job,
        ));
    }

    /**
     * EN : delete a job found by its token
     * @param \Silex\Application $app
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @param $token
     * @param $format
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    function deleteByToken(Application $app, Request $req, $token, $format)
    {
        $job = $this->jobService->findOneBy(array('token' => $token));
        if ($job === NULL)
            $app->abort(404, 'resource not found');
        if ("POST" === $req->getMethod()) {
            $app['dispatcher']->dispatch(JobEvents::BEFORE_DELETE, new GenericEvent($job, array('request' => $req)));
            $this->jobService->delete($job);
            $app['dispatcher']->dispatch(JobEvents::AFTER_DELETE, new GenericEvent($job, array('request' => $req)));
            return $app->redirect($app['url_generator']->generate('job_principe'));
        }
        return $app['twig']->render("mp.jobb.job.delete.$format.twig", array(
            "job"   => $job,
            "token" => $token,
        ));
    }

}